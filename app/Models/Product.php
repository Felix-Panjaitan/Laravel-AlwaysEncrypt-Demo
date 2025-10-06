<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesEncryption;

class Product extends Model
{
    use HasFactory, UsesEncryption;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'active',
        'credit_card_number', // Added encrypted field
        'secret_notes',       // Added encrypted field
    ];

    /**
     * Flag to determine whether to use Azure Key Vault.
     *
     * @var bool
     */
    protected $useAzureKeyVault = false;

    /**
     * Get product with local certificate encryption.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function withLocalEncryption()
    {
        return (new static)->withLocalCertStore()->newQuery();
    }

    /**
     * Get product with Azure Key Vault encryption.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function withAzureEncryption()
    {
        return (new static)->withAzureKeyVault()->newQuery();
    }
}
