<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    public const STATUS_SOLICITADA = 'Solicitada';
    public const STATUS_VALIDACION = 'Validación';
    public const STATUS_SURTIDA    = 'Surtida';

    protected $fillable = [
        'receipt_id',
        'product_id',
        'batch_id',
        'service_id',
        'user_id',
        'quantity',
        'total_price',
        'unit_price',
        'original_unit_price',
        'discount_pct',
        'payment_method',
        'amount_paid',
        'change_amount',
        'cash_register_session_id',
        'client_id',
        'cash_payment',
        'card_payment',
        'transfer_payment',
        'has_receta_completa',
        'cedula_medica',
        'status',
    ];

    /**
     * Transiciona el estado de la venta según el flujo de medicamentos controlados:
     * Solicitada -> Validación -> Surtida
     */
    public function transitionTo(string $newStatus): self
    {
        $this->status = $newStatus;
        $this->save();

        return $this;
    }

    public function cashRegisterSession()
    {
        return $this->belongsTo(CashRegisterSession::class);
    }

    // La venta pertenece a un medicamento
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // La venta pertenece a un lote específico
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    // La venta puede pertenecer a un servicio
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // La venta fue realizada por un usuario
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // El cliente que compró
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id')->withTrashed();
    }
}