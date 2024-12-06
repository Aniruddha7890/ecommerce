<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function calculateTotalForVendor(Vendor $vendor)
    {
        $total = 0;
        foreach ($this->orderProducts as $product) {
            if ($product->vendor_id === $vendor->id) {
                $productTotal = ($product->unit_price + $product->variants_total) * $product->qty;
                $total += $productTotal;
            }
        }
        return $total;
    }
}
