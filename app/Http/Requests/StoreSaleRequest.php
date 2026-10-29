<?php

namespace App\Http\Requests;

use App\Models\Batch;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Soporta tanto el flujo POS Web (cart) como el endpoint API REST (/api/v1/ventas)
        if ($this->has('cart')) {
            return [
                'cart'                => 'required',
                'branch_id'           => 'nullable|exists:branches,id',
                'payment_method'      => 'required|string',
                'amount_paid'         => 'nullable|numeric',
                'change_amount'       => 'nullable|numeric',
                'client_barcode'      => 'nullable|string|exists:users,barcode',
                'mixed_cash'          => 'nullable|numeric',
                'mixed_card'          => 'nullable|numeric',
                'mixed_transfer'      => 'nullable|numeric',
                'has_receta_completa' => 'nullable|string',
                'cedula_medica'       => 'nullable|string|min:4|max:50',
            ];
        }

        return [
            'total'          => 'required|numeric|min:0',
            'id_cliente'     => 'nullable|integer',
            'payment_method' => 'nullable|string',
            'product_id'     => 'nullable|exists:products,id',
            'quantity'       => 'nullable|integer|min:1',
            'cedula_medica'  => 'nullable|string|min:4|max:50',
        ];
    }

    /**
     * Reglas de negocio de Punto de Venta:
     * 1. Bloqueo de venta si el stock relacional es igual a 0 (o insuficiente).
     * 2. Exigir obligatoriamente 'cedula_medica' si el medicamento es Controlado (is_controlled = true).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $user = $this->user();
            $branchId = null;

            if ($user) {
                $branchId = $user->hasRole(['superadmin', 'admin', 'compras'])
                    ? $this->input('branch_id')
                    : $user->branch_id;
            } elseif ($this->filled('branch_id')) {
                $branchId = $this->input('branch_id');
            }

            // Normalizar lista de productos a validar (desde 'cart' o 'product_id')
            $itemsToValidate = [];

            if ($this->has('cart')) {
                $cart = is_array($this->cart) ? $this->cart : json_decode((string) $this->cart, true);
                if (!is_array($cart) || empty($cart)) {
                    $validator->errors()->add('cart', 'El carrito está vacío.');
                    return;
                }

                foreach ($cart as $item) {
                    if (!empty($item['is_service'])) {
                        continue;
                    }
                    $itemsToValidate[] = [
                        'product_id'    => $item['id'] ?? null,
                        'quantity'      => (int) ($item['quantity'] ?? 1),
                        'cedula_medica' => $item['cedula_medica'] ?? $this->input('cedula_medica'),
                    ];
                }
            } elseif ($this->filled('product_id')) {
                $itemsToValidate[] = [
                    'product_id'    => $this->input('product_id'),
                    'quantity'      => (int) $this->input('quantity', 1),
                    'cedula_medica' => $this->input('cedula_medica'),
                ];
            }

            foreach ($itemsToValidate as $entry) {
                $product = Product::find($entry['product_id']);
                if (!$product) {
                    $validator->errors()->add('product_id', 'El medicamento especificado no existe.');
                    continue;
                }

                // 1. Validar Stock Relacional > 0
                $batchQuery = Batch::where('product_id', $product->id)
                    ->where('stock', '>', 0)
                    ->where(function ($q) {
                        $q->where('expiration_date', '>', now())
                          ->orWhereNull('expiration_date');
                    });

                if ($branchId) {
                    $batchQuery->where('branch_id', $branchId);
                }

                $availableStock = (int) $batchQuery->sum('stock');

                if ($availableStock <= 0) {
                    $validator->errors()->add(
                        'stock',
                        "Venta bloqueada: El medicamento '{$product->name}' tiene stock relacional igual a 0."
                    );
                } elseif ($availableStock < $entry['quantity']) {
                    $validator->errors()->add(
                        'stock',
                        "Stock insuficiente para '{$product->name}'. Disponible: {$availableStock}, solicitado: {$entry['quantity']}."
                    );
                }

                // 2. Validar Cédula Médica obligatoria si el medicamento es Controlado
                if ($product->is_controlled) {
                    $cedula = trim((string) ($entry['cedula_medica'] ?? ''));
                    if ($cedula === '') {
                        $validator->errors()->add(
                            'cedula_medica',
                            "El campo cedula_medica es obligatorio para vender el medicamento controlado '{$product->name}'."
                        );
                    }
                }
            }
        });
    }
}

