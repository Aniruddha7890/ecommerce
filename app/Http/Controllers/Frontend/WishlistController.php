<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistProducts = Wishlist::with('product')->where(['user_id' => Auth::user()->id])->get();
        return view('frontend.pages.wishlist', compact('wishlistProducts'));
    }

    public function addToWishlist(Request $request)
    {
        if (!Auth::check()) {
            return response(['status' => 'error', 'message' => 'Login before add a product into wishlist!']);
        }

        $wishlistCount = Wishlist::where(['product_id' => $request->id, 'user_id' => Auth::user()->id])->count();

        if ($wishlistCount > 0) {
            return response(['status' => 'error', 'message' => 'The product is already at wishlist!']);
        }

        $wishlist = new Wishlist();
        $wishlist->product_id = $request->id;
        $wishlist->user_id = Auth::user()->id;
        $wishlist->save();

        return response(['status' => 'success', 'message' => 'Product is added into the wishlist!']);
    }

    public function destroy(string $id)
    {
        $wishlistProduct = Wishlist::where('id', $id)->firstOrFail();
        if ($wishlistProduct->user_id !== Auth::user()->id) {
            return redirect()->back();
        }
        $wishlistProduct->delete();

        toastr('Product removed Successfully', 'success', 'Success');

        return redirect()->back();
    }
}
