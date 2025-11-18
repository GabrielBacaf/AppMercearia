<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'street',
        'number',
        'complement',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
