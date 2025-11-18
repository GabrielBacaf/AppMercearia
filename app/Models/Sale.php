<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Sale extends Model
{
    protected $fillable = [
        'discount',
        'delivery_price',
        'user_id',
        'updated_by',
        'client_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

}
