<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'active',
        'credit_card_number', // Added encrypted field
        'secret_notes',       // Added encrypted field
    ];

    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer',
        'active' => 'boolean',
    ];
}
