<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariantItem;
use Illuminate\Http\Request;
use Cart;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /** Show Cart Page */
    public function cartDetails(){
        $cartItems = Cart::content();

        if(count($cartItems) == 0){
            toastr('Please add some product in your cart to see the cart page', 'warning', 'Cart is Empty!');
            return redirect()->route('home');
        }

        return view('frontend.pages.cart-detail', compact('cartItems'));
    }

    //Add item to cart
    public function addToCart(Request $request){
        $product = Product::findOrFail($request->product_id);

        //check product quantity
        if($product->qty == 0){
            return response(['status' => 'error', 'message' => 'Product stock out!']);
        } else if($product->qty < $request->qty){
            return response(['status' => 'error', 'message' => 'Quantity not available in our stock!']);
        }

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

    /** Update Product Qantity */
    public function updateProductQty(Request $request){
        $productId = Cart::get($request->rowId)->id;
        $product = Product::findOrFail($productId);
        //check product quantity
        if($product->qty == 0){
            return response(['status' => 'error', 'message' => 'Product stock out!']);
        } else if($product->qty < $request->quantity){
            return response(['status' => 'error', 'message' => 'Quantity not available in our stock!']);
        }

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

    /** Get Cart Total Amount */
    public function cartTotal(){
        $total = 0;
        foreach (Cart::content() as $product) {
            $total += $this->getProductTotal($product->rowId);
        }
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

        toastr('Product Removed Successfully!', 'success', 'Deleted from Cart!');
        return redirect()->back();
    }

    /** get count of products in Cart */
    public function getCartCount(){
        return Cart::content()->count();
    }

    /** get all cart products */
    public function getCartProducts(){
        return Cart::content();
    }

    /** Remove product from sidebar */
    public function removeSidebarProduct(Request $request){
        Cart::remove($request->rowId);

        return response(['status' => 'success', 'message' => 'Product removed Successfully!']);
    }

    /** Apply coupon */
    public function applyCoupon(Request $request){
        if($request->coupon_code == null){
            return response(['status' => 'error', 'message' => 'Coupon field is required!']);
        }

        $coupon = Coupon::where(['code' => $request->coupon_code, 'status' => 1])->first();
        
        if($coupon == null){
            return response(['status' => 'error', 'message' => 'Coupon not exist!']);
        } else if($coupon->start_date > date('Y-m-d')){
            return response(['status' => 'error', 'message' => 'Coupon not exist!']);
        } else if($coupon->end_date < date('Y-m-d')){
            return response(['status' => 'error', 'message' => 'Coupon is expired!']);
        } else if($coupon->total_used >= $coupon->quantity){
            return response(['status' => 'error', 'message' => 'You can not use this coupon!']);
        }

        if($coupon->discount_type == 'amount'){
            Session::put('coupon', [
                'coupon_name' => $coupon->name,
                'coupon_code' => $coupon->code,
                'discount_type' => 'amount',
                'discount' => $coupon->discount
            ]);
        } else if($coupon->discount_type == 'percent'){
            Session::put('coupon', [
                'coupon_name' => $coupon->name,
                'coupon_code' => $coupon->code,
                'discount_type' => 'percent',
                'discount' => $coupon->discount
            ]);
        }

        return response(['status' => 'success', 'message' => 'Coupon applied successfully!']);
    }
}
