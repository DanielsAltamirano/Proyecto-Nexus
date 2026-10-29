<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;

class PromoService
{
    public function getActivePromos($branchId = null, $shift = null)
    {
        $now = now();
        if (!$shift) {
            $shift = ($now->format('H:i:s') >= '07:00:00' && $now->format('H:i:s') <= '14:59:59') ? 'morning' : 'afternoon';
        }

        $daysMap = [
            1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
            4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
        ];
        $todayName = $daysMap[$now->dayOfWeekIso];

        $isGenerico = $this->checkPromo('promo_generico', $todayName, $shift);
        $isReceta = $this->checkPromo('promo_receta_completa', $todayName, $shift);

        // Regla de exclusión mutua: Genéricos y Receta Completa nunca aplican el mismo día
        if ($isGenerico && $isReceta) {
            $isReceta = false;
        }

        return [
            'generico' => $isGenerico,
            'receta' => $isReceta,
            'current_day' => $todayName,
            'current_shift' => $shift
        ];
    }

    public function checkProductPromoTime(Product $product, $promoType, $todayName, $shift, $branchId = null)
    {
        $days = $product->{"{$promoType}_days"} ?? [];
        $shifts = $product->{"{$promoType}_shifts"} ?? [];
        $branches = $product->{"{$promoType}_branches"} ?? [];

        if (empty($days) || empty($shifts) || empty($branches)) {
            return false;
        }

        if (!in_array($todayName, $days) || !in_array($shift, $shifts)) {
            return false;
        }

        if (in_array('all', $branches)) {
            return true;
        }

        if ($branchId && in_array((string)$branchId, $branches)) {
            return true;
        }

        return false;
    }

    private function checkPromo($prefix, $todayName, $shift)
    {
        $days = Cache::get("{$prefix}_days", []);
        $shifts = Cache::get("{$prefix}_shifts", []);

        if (empty($days) || empty($shifts)) {
            return false;
        }

        return in_array($todayName, $days) && in_array($shift, $shifts);
    }

    private function checkPromoBranch($prefix, $todayName, $shift, $branchId)
    {
        if (!$this->checkPromo($prefix, $todayName, $shift)) {
            return false;
        }

        $branches = Cache::get("{$prefix}_branches", ['all']);
        if (in_array('all', $branches)) {
            return true;
        }
        
        if ($branchId && in_array((string)$branchId, $branches)) {
            return true;
        }
        
        return false;
    }



    public function getSettings()
    {
        return [
            'generico_discount' => Cache::get('promo_generico_discount', 25),
            'receta_discount' => Cache::get('promo_receta_completa_discount', 20),
        ];
    }

    /**
     * Calcula los precios seguros de lado del servidor para un producto
     */
    public function calculateItemPricing(Product $product, int $quantity, float $basePrice, bool $applyReceta, $branchId = null)
    {
        $promos = $this->getActivePromos($branchId);
        $settings = $this->getSettings();
        
        $todayName = $promos['current_day'];
        $shift = $promos['current_shift'];

        $originalPrice = null;
        $discountPct = null;
        $lineTotal = round($basePrice * $quantity, 2);
        $finalUnitPrice = $basePrice;

        // Verificar si el producto tiene activa la promo 4x3 hoy para turno y sucursal
        $isProduct4x3Active = $product->is_promo_4x3 && $this->checkProductPromoTime($product, 'promo_4x3', $todayName, $shift, $branchId);

        // Verificar si el producto tiene activa la promo por volumen hoy para turno y sucursal
        $isProductVolumenActive = $product->is_promo_volumen && $this->checkProductPromoTime($product, 'promo_volumen', $todayName, $shift, $branchId);

        // 1. Promo 4x3:
        // Si el producto está en la lista de 4x3 hoy, aplica su regla y queda EXCLUIDO de cualquier otra promo (genéricos/receta).
        if ($isProduct4x3Active) {
            $freeQty = floor($quantity / 4);
            $paidQty = $quantity - $freeQty;
            $lineTotal = round($basePrice * $paidQty, 2);
            $finalUnitPrice = $quantity > 0 ? round($lineTotal / $quantity, 2) : $basePrice;
            $originalPrice = $freeQty > 0 ? $basePrice : null;
            return compact('finalUnitPrice', 'originalPrice', 'discountPct', 'lineTotal');
        }

        // 2. Promo Volumen:
        // Si el producto está en la lista de Volumen hoy, aplica precio de volumen si cumple el mínimo de piezas
        // y en todo caso queda EXCLUIDO de cualquier otra promo (genéricos/receta).
        if ($isProductVolumenActive) {
            $minQty = (int) ($product->promo_volumen_min_qty ?? 2);
            if ($quantity >= $minQty && $product->promo_volumen_price !== null) {
                $finalUnitPrice = (float) $product->promo_volumen_price;
                $lineTotal = round($finalUnitPrice * $quantity, 2);
                $originalPrice = $basePrice;
            } else {
                $finalUnitPrice = $basePrice;
                $lineTotal = round($basePrice * $quantity, 2);
                $originalPrice = null;
            }
            return compact('finalUnitPrice', 'originalPrice', 'discountPct', 'lineTotal');
        }

        // 3. Promos Genéricos o Receta Completa (Mutuamente excluyentes)
        // Aplica a medicamentos tipo GEN que NO estén en las listas de 4x3 ni Volumen hoy.
        if ($product->type === 'GEN') {
            $bestDiscount = 0;
            
            if ($promos['generico']) {
                $bestDiscount = (int) $settings['generico_discount'];
            } elseif ($promos['receta'] && $applyReceta) {
                $bestDiscount = (int) $settings['receta_discount'];
            }

            if ($bestDiscount > 0) {
                $discountPct = $bestDiscount;
                $finalUnitPrice = round($basePrice * (1 - $discountPct / 100), 2);
                $lineTotal = round($finalUnitPrice * $quantity, 2);
                $originalPrice = $basePrice;
            }
        }

        return compact('finalUnitPrice', 'originalPrice', 'discountPct', 'lineTotal');
    }
}
