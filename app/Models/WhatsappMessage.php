<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_message';

    protected $fillable = [
        'phone',
        'message',
        'date',
        'status',
    ];
}
