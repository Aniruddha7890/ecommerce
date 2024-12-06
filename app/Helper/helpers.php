<?php

use Illuminate\Support\Facades\Session;

/** Set Sidebar Item Active */
function setActive(array $route)
{
    if (is_array($route)) {
        foreach ($route as $r) {
            if (request()->routeIs($r)) {
                return 'active';
            }
        }
    }
}

/** Check if producct has discount */
function checkDiscount($product)
{
    $currentDate = date('Y-m-d');

    if ($product->offer_price > 0 && $currentDate >= $product->offer_start_date && $currentDate <= $product->offer_end_date) {
        return true;
    }

    return false;
}

/** Calculate discount percent */
function calculateDiscountPercent($originalPrice, $discountPrice)
{
    $discountAmmount = $originalPrice - $discountPrice;
    $discountPercent = ($discountAmmount / $originalPrice) * 100;

    return $discountPercent;
}

/** Check the product type */
function productType(string $type)
{
    switch ($type) {
        case 'new_arrival':
            return 'New';
            break;
        case 'featured_product':
            return 'Featured';
            break;
        case 'top_product':
            return 'Top';
            break;
        case 'best_product':
            return 'Best';
            break;
        default:
            return '';
            break;
    }
}

/** Get total cart amount */
function getCartTotal()
{
    $total = 0;
    foreach (\Cart::content() as $product) {
        $total += ($product->price + $product->options->variants_total) * $product->qty;
    }
    return $total;
}

/** Get payable total amount */
function getMainCartTotal()
{
    if (Session::has('coupon')) {
        $coupon = Session::get('coupon');
        $subTotal = getCartTotal();
        if ($coupon['discount_type'] == 'amount') {
            $total = $subTotal - $coupon['discount'];
            return $total;
        } else if ($coupon['discount_type'] == 'percent') {
            $discount = $subTotal - ($subTotal * $coupon['discount'] / 100);
            $total = $subTotal - $discount;
            return $total;
        }
    } else {
        return getCartTotal();
    }
}

/** Get cart discount */
function getCartDiscount()
{
    if (Session::has('coupon')) {
        $coupon = Session::get('coupon');
        $subTotal = getCartTotal();
        if ($coupon['discount_type'] == 'amount') {
            return $coupon['discount'];
        } else if ($coupon['discount_type'] == 'percent') {
            // $discount = $subTotal - ($subTotal * $coupon['discount'] / 100);
            $discount = ($subTotal * $coupon['discount'] / 100);
            return $discount;
        }
    } else {
        return 0;
    }
}

/** Get selected shipping fee from session */
function getShippingFee()
{
    if (Session::has('shipping_method')) {
        return Session::get('shipping_method')['cost'];
    } else {
        return 0;
    }
}

/** Get payable amount */
function getFinalPayableAmount()
{
    return getMainCartTotal() + getShippingFee();
}
