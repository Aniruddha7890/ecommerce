<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariantItem;
use Illuminate\Http\Request;
use Cart;

class CartController extends Controller
{
    //Add item to cart
    public function addToCart(Request $request){
        $product = Product::findOrFail($request->product_id);
        $variants = [];

        $variantTotalAmount = 0;

        if($request->has('variant_items')){
            foreach ($request->variant_items as $item_id) {
                $variantItem = ProductVariantItem::findOrFail($item_id);
                $variants[$variantItem->productVariant->name]['name'] = $variantItem->name;
                $variants[$variantItem->productVariant->name]['price'] = $variantItem->price;
                $variantTotalAmount += $variantItem->price;
            }
        }
        
        //check discount
        $productPrice = 0;
        if(checkDiscount($product)){
            $productPrice = $product->offer_price;
        } else{
            $productPrice = $product->price;
        }

        $cartData = [];
        $cartData['id'] = $product->id;
        $cartData['name'] = $product->name;
        $cartData['qty'] = $request->qty;
        $cartData['price'] = $productPrice;
        $cartData['weight'] = 10;
        $cartData['options']['variants'] = $variants;
        $cartData['options']['variants_total'] = $variantTotalAmount;
        $cartData['options']['image'] = $product->thumb_image;
        $cartData['options']['slug'] = $product->slug;

        Cart::add($cartData);

        return response(['status' => 'success', 'message' => 'Added to cart successfully!']);
    }

    /** Show Cart Page */
    public function cartDetails(){
        $cartItems = Cart::content();
        return view('frontend.pages.cart-detail', compact('cartItems'));
    }

    /** Update Product Qantity */
    public function updateProductQty(Request $request){
        Cart::update($request->rowId, $request->quantity);
        $productTotal = $this->getProductTotal($request->rowId);

        return response(['status' => 'success', 'message' => 'Product Quantity Updated!', 'product_total' => $productTotal]);
    }

    /** Get Product Total */
    public function getProductTotal($rowId){
        $product = Cart::get($rowId);
        $total = ($product->price + $product->options->variants_total) * $product->qty;
        return $total;
    }

    /** Clear Full Cart */
    public function clearCart(){
        Cart::destroy();

        return response(['status' => 'success', 'message' => 'Cart cleared Successfully!']);
    }

    /** Remove product from Cart */
    public function removeProduct($rowId){
        Cart::remove($rowId);

        return redirect()->back();
    }
}
