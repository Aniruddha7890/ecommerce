<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    //Add item to cart
    public function addToCart(Request $request){
        dd($request->all());
    }
}
