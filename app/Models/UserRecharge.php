<?php

namespace App\Models;

use App\Enum\PlanType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserRecharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'plan_id',
        'router_id',
        'server_id',
        'username',
        'pppoe_password',
        'namebp',
        'recharged_at',
        'expired_at',
        'status',
        'method',
        'type',
        'service_number',
    ];

    protected $casts = [
        'type' => PlanType::class,
        'recharged_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function paymentGateway(): HasOne
    {
        return $this->hasOne(PaymentGateway::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'on';
    }

    // Scope untuk pengguna yang masuk kondisi isolir
    public function scopeIsolir($query)
    {
        return $query->where('expired_at', '<', Carbon::now())
            ->orWhere('status', 'off');
    }

    // Mengecek apakah pelanggan masuk status isolir
    public function isIsolir()
    {
        return Carbon::now()->gt(Carbon::parse($this->expired_at)) || $this->status == 'off';
    }

    // Mengecek apakah pelanggan masuk kondisi tagihan (kurang dari 7 hari sebelum expired)
    public function isTagihan()
    {
        return Carbon::now()->diffInDays(Carbon::parse($this->expired_at), false) <= 7;
    }

    // Mengecek apakah pelanggan sudah menerima pesan penagihan sebelumnya
    public function hasReceivedMessage($type)
    {
        return WhatsappMessage::where('phone', $this->customer->phonenumber)
            ->where('message', 'LIKE', "%$type%")
            ->exists();
    }
}
