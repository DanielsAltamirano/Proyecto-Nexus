<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Product;
use App\Services\PromoService;

class PromoController extends Controller
{
    public function index(PromoService $promoService)
    {
        $settings = $promoService->getSettings();
        $genericoDiscount = $settings['generico_discount'];
        $recetaDiscount = $settings['receta_discount'];

        $genericoDays     = Cache::get('promo_generico_days', ['Lunes']);
        $genericoShifts   = Cache::get('promo_generico_shifts', ['morning', 'afternoon']);
        
        $recetaDays     = Cache::get('promo_receta_completa_days', ['Miércoles']);
        $recetaShifts   = Cache::get('promo_receta_completa_shifts', ['morning', 'afternoon']);

        // Regla: Genéricos y Receta Completa son mutuamente excluyentes
        $overlap = array_intersect($genericoDays, $recetaDays);
        if (!empty($overlap)) {
            $recetaDays = array_values(array_diff($recetaDays, $overlap));
            Cache::forever('promo_receta_completa_days', $recetaDays);
        }

        $promo4x3Count    = Product::where('is_promo_4x3', true)->count();
        $promoVolumenCount    = Product::where('is_promo_volumen', true)->count();

        $genericCount = Product::where('type', 'GEN')
            ->whereHas('batches', function ($q) {
                $q->where('stock', '>', 0)
                  ->where(function ($inner) {
                      $inner->where('expiration_date', '>', now())
                            ->orWhereNull('expiration_date');
                  });
            })->count();

        $branches = \App\Models\Branch::orderBy('name')->get();

        $promosActive = $promoService->getActivePromos();
        $isTodayGenerico = $promosActive['generico'] ?? false;
        $isTodayReceta = $promosActive['receta'] ?? false;
        $todayName = $promosActive['current_day'];

        // Determinar dinámicamente si hoy hay productos con 4x3 o volumen activos
        $isToday4x3 = Product::where('is_promo_4x3', true)
            ->whereJsonContains('promo_4x3_days', $todayName)
            ->exists();
        $isTodayVolumen = Product::where('is_promo_volumen', true)
            ->whereJsonContains('promo_volumen_days', $todayName)
            ->exists();

        return view('promos.index', compact(
            'genericoDiscount', 'genericoDays', 'genericoShifts',
            'isTodayGenerico', 'genericCount',
            'recetaDiscount', 'recetaDays', 'recetaShifts', 'isTodayReceta',
            'isToday4x3', 'promo4x3Count',
            'isTodayVolumen', 'promoVolumenCount',
            'branches', 'todayName'
        ));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'discount' => 'required|numeric|min:1|max:99',
            'days'     => 'required|array|min:1',
            'shifts'   => 'required|array|min:1',
        ]);

        $recetaDays = Cache::get('promo_receta_completa_days', ['Miércoles']);
        $conflicts = array_intersect($request->days, $recetaDays);
        if (!empty($conflicts)) {
            return response()->json([
                'success' => false,
                'message' => 'La Promo de Genéricos y Receta Completa son mutuamente excluyentes. No pueden activarse el mismo día (' . implode(', ', $conflicts) . ').'
            ], 422);
        }

        Cache::forever('promo_generico_discount', (int) $request->discount);
        Cache::forever('promo_generico_days', $request->days);
        Cache::forever('promo_generico_shifts', $request->shifts);

        return response()->json(['success' => true]);
    }

    public function updateRecetaSettings(Request $request)
    {
        $request->validate([
            'discount' => 'required|numeric|min:1|max:99',
            'days'     => 'required|array|min:1',
            'shifts'   => 'required|array|min:1',
        ]);

        $genericoDays = Cache::get('promo_generico_days', ['Lunes']);
        $conflicts = array_intersect($request->days, $genericoDays);
        if (!empty($conflicts)) {
            return response()->json([
                'success' => false,
                'message' => 'La Promo de Receta Completa y Genéricos son mutuamente excluyentes. No pueden activarse el mismo día (' . implode(', ', $conflicts) . ').'
            ], 422);
        }

        Cache::forever('promo_receta_completa_discount', (int) $request->discount);
        Cache::forever('promo_receta_completa_days', $request->days);
        Cache::forever('promo_receta_completa_shifts', $request->shifts);

        return response()->json(['success' => true]);
    }

    public function genericProducts()
    {
        $discount = Cache::get('promo_generico_discount', 25);

        $products = Product::where('type', 'GEN')
            ->whereHas('batches', function ($q) {
                $q->where('stock', '>', 0)
                  ->where(function ($inner) {
                      $inner->where('expiration_date', '>', now())
                            ->orWhereNull('expiration_date');
                  });
            })
            ->with(['batches' => function ($q) {
                $q->where('stock', '>', 0)->with('branch:id,name')->orderBy('expiration_date');
            }, 'category:id,name'])
            ->orderBy('name')
            ->get()
            ->map(function ($p) use ($discount) {
                $totalStock = $p->batches->sum('stock');
                $price      = (float) ($p->batches->first()?->selling_price ?? 0);
                $discounted = round($price * (1 - $discount / 100), 2);
                return [
                    'name'       => $p->name,
                    'sku'        => $p->sku,
                    'category'   => $p->category?->name ?? 'Sin categoria',
                    'stock'      => $totalStock,
                    'price'      => $price,
                    'discounted' => $discounted,
                ];
            });

        return response()->json($products);
    }



    public function searchProducts4x3(Request $request)
    {
        $query = Product::with(['category:id,name', 'batches' => function ($q) {
            $q->where('stock', '>', 0)->orderBy('expiration_date');
        }]);

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            });
        } else {
            $query->where('is_promo_4x3', true);
        }

        $products = $query->orderBy('name')->limit(50)->get()->map(function ($p) {
            $regularPrice = (float) ($p->batches->first()?->selling_price ?? 0);
            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'category' => $p->category?->name ?? 'Sin categoria',
                'regular_price' => $regularPrice,
                'is_promo_4x3' => $p->is_promo_4x3,
                'is_promo_volumen' => $p->is_promo_volumen,
                'promo_4x3_days' => $p->promo_4x3_days ?? ['Jueves'],
                'promo_4x3_shifts' => $p->promo_4x3_shifts ?? ['morning', 'afternoon'],
                'promo_4x3_branches' => $p->promo_4x3_branches ?? ['all'],
            ];
        });

        return response()->json($products);
    }

    public function toggleProduct4x3(Request $request, Product $product)
    {
        $request->validate([
            'is_promo_4x3' => 'required|boolean',
            'promo_4x3_days' => 'required_if:is_promo_4x3,true|array',
            'promo_4x3_shifts' => 'required_if:is_promo_4x3,true|array',
            'promo_4x3_branches' => 'required_if:is_promo_4x3,true|array',
        ]);
        
        $product->is_promo_4x3 = $request->is_promo_4x3;
        if ($product->is_promo_4x3) {
            $product->is_promo_volumen = false;
            $product->promo_volumen_min_qty = null;
            $product->promo_volumen_price = null;
            $product->promo_volumen_days = null;
            $product->promo_volumen_shifts = null;
            $product->promo_volumen_branches = null;
            
            $product->promo_4x3_days = $request->promo_4x3_days;
            $product->promo_4x3_shifts = $request->promo_4x3_shifts;
            $product->promo_4x3_branches = $request->promo_4x3_branches;
        } else {
            $product->promo_4x3_days = null;
            $product->promo_4x3_shifts = null;
            $product->promo_4x3_branches = null;
        }
        $product->save();

        $todayName = (new PromoService())->getActivePromos()['current_day'];
        $isToday4x3 = Product::where('is_promo_4x3', true)
            ->whereJsonContains('promo_4x3_days', $todayName)
            ->exists();
        $isTodayVolumen = Product::where('is_promo_volumen', true)
            ->whereJsonContains('promo_volumen_days', $todayName)
            ->exists();
        $promo4x3Count = Product::where('is_promo_4x3', true)->count();
        $promoVolumenCount = Product::where('is_promo_volumen', true)->count();

        return response()->json([
            'success' => true, 
            'is_promo_4x3' => (bool) $product->is_promo_4x3,
            'is_promo_volumen' => (bool) $product->is_promo_volumen,
            'isToday4x3' => $isToday4x3,
            'isTodayVolumen' => $isTodayVolumen,
            'promo4x3Count' => $promo4x3Count,
            'promoVolumenCount' => $promoVolumenCount,
        ]);
    }



    public function searchProductsVolumen(Request $request)
    {
        $query = Product::with(['category:id,name', 'batches' => function ($q) {
            $q->where('stock', '>', 0)->orderBy('expiration_date');
        }]);

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            });
        } else {
            $query->where('is_promo_volumen', true);
        }

        $products = $query->orderBy('name')->limit(50)->get()->map(function ($p) {
            $regularPrice = (float) ($p->batches->first()?->selling_price ?? 0);
            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'category' => $p->category?->name ?? 'Sin categoria',
                'regular_price' => $regularPrice,
                'is_promo_volumen' => $p->is_promo_volumen,
                'is_promo_4x3' => $p->is_promo_4x3,
                'promo_volumen_min_qty' => $p->promo_volumen_min_qty ?? 2,
                'promo_volumen_price' => $p->promo_volumen_price ?? $regularPrice,
                'promo_volumen_days' => $p->promo_volumen_days ?? ['Viernes'],
                'promo_volumen_shifts' => $p->promo_volumen_shifts ?? ['morning', 'afternoon'],
                'promo_volumen_branches' => $p->promo_volumen_branches ?? ['all'],
            ];
        });

        return response()->json($products);
    }

    public function toggleProductVolumen(Request $request, Product $product)
    {
        $request->validate([
            'is_promo_volumen' => 'required|boolean',
            'promo_volumen_min_qty' => 'required_if:is_promo_volumen,true|nullable|integer|min:2',
            'promo_volumen_price' => 'required_if:is_promo_volumen,true|nullable|numeric|min:0.01',
            'promo_volumen_days' => 'required_if:is_promo_volumen,true|array',
            'promo_volumen_shifts' => 'required_if:is_promo_volumen,true|array',
            'promo_volumen_branches' => 'required_if:is_promo_volumen,true|array',
        ]);
        
        $product->is_promo_volumen = $request->is_promo_volumen;
        
        if ($product->is_promo_volumen) {
            $product->is_promo_4x3 = false;
            $product->promo_4x3_days = null;
            $product->promo_4x3_shifts = null;
            $product->promo_4x3_branches = null;
            
            $product->promo_volumen_min_qty = $request->promo_volumen_min_qty;
            $product->promo_volumen_price = $request->promo_volumen_price;
            $product->promo_volumen_days = $request->promo_volumen_days;
            $product->promo_volumen_shifts = $request->promo_volumen_shifts;
            $product->promo_volumen_branches = $request->promo_volumen_branches;
        } else {
            $product->promo_volumen_min_qty = null;
            $product->promo_volumen_price = null;
            $product->promo_volumen_days = null;
            $product->promo_volumen_shifts = null;
            $product->promo_volumen_branches = null;
        }
        
        $product->save();

        $todayName = (new PromoService())->getActivePromos()['current_day'];
        $isToday4x3 = Product::where('is_promo_4x3', true)
            ->whereJsonContains('promo_4x3_days', $todayName)
            ->exists();
        $isTodayVolumen = Product::where('is_promo_volumen', true)
            ->whereJsonContains('promo_volumen_days', $todayName)
            ->exists();
        $promo4x3Count = Product::where('is_promo_4x3', true)->count();
        $promoVolumenCount = Product::where('is_promo_volumen', true)->count();

        return response()->json([
            'success' => true, 
            'is_promo_volumen' => (bool) $product->is_promo_volumen,
            'is_promo_4x3' => (bool) $product->is_promo_4x3,
            'promo_volumen_min_qty' => $product->promo_volumen_min_qty,
            'promo_volumen_price' => $product->promo_volumen_price,
            'isTodayVolumen' => $isTodayVolumen,
            'isToday4x3' => $isToday4x3,
            'promoVolumenCount' => $promoVolumenCount,
            'promo4x3Count' => $promo4x3Count,
        ]);
    }
}
