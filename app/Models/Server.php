<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'router_id',
    ];
    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }
}
