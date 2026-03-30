<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Sale extends Model
{
    protected $fillable = [
        'discount',
        'total_value',
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
    
        return $this->morphMany(Payment::class, 'payable');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('amount', 'sale_value')
            ->withTimestamps();
    }
}
