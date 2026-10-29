<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Provider;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Batch;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['provider', 'user'])->orderBy('created_at', 'desc')->get();
        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    public function create(Request $request)
    {
        $providers = Provider::orderBy('name', 'asc')->get();
        $products = Product::orderBy('name', 'asc')->get();
        $productRequestId = $request->query('product_request_id');
        $productRequest = null;
        
        if ($productRequestId) {
            $productRequest = \App\Models\ProductRequest::with('product')->find($productRequestId);
        }

        return view('purchase_orders.create', compact('providers', 'products', 'productRequest'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'product_request_id' => 'nullable|exists:product_requests,id'
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            
            $order = PurchaseOrder::create([
                'provider_id' => $request->provider_id,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'notes' => $request->notes,
                'total_amount' => 0
            ]);

            foreach ($request->products as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $totalAmount += $subtotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);

            if ($request->filled('product_request_id')) {
                $productRequest = \App\Models\ProductRequest::find($request->product_request_id);
                $productRequest->update([
                    'status' => 'ordered_to_provider',
                    'purchase_order_id' => $order->id
                ]);

                \App\Models\ProductRequestHistory::create([
                    'product_request_id' => $productRequest->id,
                    'user_id' => auth()->id(),
                    'action' => 'Generó Orden de Compra',
                    'details' => 'Se generó la Orden de Compra #' . $order->id
                ]);
            }

            DB::commit();

            return redirect()->route('purchase-orders.index')->with('success', 'Orden de compra generada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar la orden de compra: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['provider', 'user', 'items.product']);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    public function downloadPdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['provider', 'user', 'items.product']);
        
        // Ensure DomPDF handles custom fonts and layouts correctly or use default config
        $pdf = Pdf::loadView('purchase_orders.pdf', compact('purchaseOrder'));
        
        return $pdf->download('orden_compra_' . $purchaseOrder->id . '.pdf');
    }

    public function downloadXml(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['provider', 'user', 'items.product']);

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><PurchaseOrder></PurchaseOrder>');
        $xml->addChild('ID', $purchaseOrder->id);
        $xml->addChild('Date', $purchaseOrder->created_at->format('Y-m-d H:i:s'));
        $xml->addChild('Status', $purchaseOrder->status);
        $xml->addChild('TotalAmount', $purchaseOrder->total_amount);
        $xml->addChild('Notes', htmlspecialchars($purchaseOrder->notes ?? ''));

        $provider = $xml->addChild('Provider');
        $provider->addChild('ID', $purchaseOrder->provider->id);
        $provider->addChild('Name', htmlspecialchars($purchaseOrder->provider->name));
        
        $items = $xml->addChild('Items');
        foreach ($purchaseOrder->items as $i) {
            $item = $items->addChild('Item');
            $item->addChild('ProductID', $i->product_id);
            $item->addChild('ProductName', htmlspecialchars($i->product->name));
            $item->addChild('Quantity', $i->quantity);
            $item->addChild('UnitPrice', $i->unit_price);
            $item->addChild('Subtotal', $i->subtotal);
        }

        return response($xml->asXML(), 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="orden_compra_' . $purchaseOrder->id . '.xml"',
        ]);
    }

    public function receiveForm(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)->with('error', 'Esta orden ya fue recibida.');
        }

        $purchaseOrder->load(['items.product']);
        $branches = Branch::orderBy('name', 'asc')->get();
        return view('purchase_orders.receive', compact('purchaseOrder', 'branches'));
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return redirect()->route('purchase-orders.index')->with('error', 'La orden ya fue recibida anteriormente.');
        }

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'invoice_pdf' => 'nullable|file|mimes:pdf|max:5120',
            'invoice_xml' => 'nullable|file|mimes:xml|max:5120',
        ]);

        DB::beginTransaction();
        try {
            // Manejar subida de archivos
            if ($request->hasFile('invoice_pdf')) {
                $pdfPath = $request->file('invoice_pdf')->store('invoices/pdf', 'public');
                $purchaseOrder->provider_invoice_pdf = $pdfPath;
            }
            if ($request->hasFile('invoice_xml')) {
                $xmlPath = $request->file('invoice_xml')->store('invoices/xml', 'public');
                $purchaseOrder->provider_invoice_xml = $xmlPath;
            }

            // Ingresar productos a inventario
            foreach ($purchaseOrder->items as $item) {
                // Crear un Lote (Batch) por cada item
                $batch = Batch::create([
                    'product_id' => $item->product_id,
                    'branch_id' => $request->branch_id,
                    'stock' => $item->quantity,
                    'cost_price' => $item->unit_price,
                    // Dejamos selling_price igual que cost por defecto, o que el user edite despues
                    'selling_price' => $item->unit_price, 
                    'batch_number' => 'OC-' . $purchaseOrder->id . '-' . date('ymd'),
                    'expiration_date' => null, // Se puede editar en inventario si es medicamento
                ]);

                // Registrar movimiento
                InventoryMovement::create([
                    'product_id' => $item->product_id,
                    'branch_id' => $request->branch_id,
                    'batch_id' => $batch->id,
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'reason' => 'Recepción de Orden de Compra #' . $purchaseOrder->id,
                    'user_id' => auth()->id()
                ]);
            }

            $purchaseOrder->status = 'received';
            $purchaseOrder->save();

            DB::commit();
            return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Orden de compra recibida correctamente. El inventario ha sido actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hubo un error al recibir la orden: ' . $e->getMessage());
        }
    }
}
