<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingUserRecharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_recharge_id',
        'customer_id',
        'plan_id',
        'router_id',
        'server_id',
        'username',
        'price',
        'status',
        'scheduled_for',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
    ];

    public function userRecharge(): BelongsTo
    {
        return $this->belongsTo(UserRecharge::class);
    }

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
}
