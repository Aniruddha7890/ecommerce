<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FrontendProductController extends Controller
{
    public function productsIndex(Request $request)
    {
        // dd($request->all());
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->firstOrFail();
            $products = Product::where([
                'category_id' => $category->id,
                'status' => 1,
                'is_approved' => 1
            ])
                ->when($request->has('range'), function ($query) use ($request) {
                    $price = explode(';', $request->range);
                    $from = $price[0];
                    $to = $price[1];

                    return $query->where('price', '>=', $from)->where('price', '<=', $to);
                })
                ->paginate(12);
        } else if ($request->has('subCategory')) {
            $category = SubCategory::where('slug', $request->subCategory)->firstOrFail();
            $products = Product::where([
                'sub_category_id' => $category->id,
                'status' => 1,
                'is_approved' => 1
            ])
                ->when($request->has('range'), function ($query) use ($request) {
                    $price = explode(';', $request->range);
                    $from = $price[0];
                    $to = $price[1];

                    return $query->where('price', '>=', $from)->where('price', '<=', $to);
                })
                ->paginate(12);
        } else if ($request->has('childCategory')) {
            $category = ChildCategory::where('slug', $request->childCategory)->firstOrFail();
            $products = Product::where([
                'child_category_id' => $category->id,
                'status' => 1,
                'is_approved' => 1
            ])
                ->when($request->has('range'), function ($query) use ($request) {
                    $price = explode(';', $request->range);
                    $from = $price[0];
                    $to = $price[1];

                    return $query->where('price', '>=', $from)->where('price', '<=', $to);
                })
                ->paginate(12);
        }
        $categories = Category::where('status', 1)->get();
        return view('frontend.pages.product', compact('products', 'categories'));
    }

    // public function productsIndex(Request $request)
    // {

    //     $withRange = false;

    //     $from = 0;

    //     $to = 10000;

    //     if ($request->has('range') && !empty($request->range)) {

    //         $withRange = true;

    //         $price = explode(';', $request->range);

    //         $from = $price[0] ? $price[0] : 0;

    //         $to = isset($price[1]) ? $price[1] : 10000;
    //     }

    //     if ($request->has('category')) {

    //         $category = Category::where('slug', $request->category)->first();

    //         $products = Product::where([

    //             'category_id' => $category->id,

    //             'status' => 1,

    //             'is_approved' => 1,

    //         ])

    //             ->when($withRange, function ($query) use ($from, $to) {

    //                 return $query->where('price', '>=', $from)->where('price', '<=', $to);
    //             })

    //             ->paginate(12);
    //     } elseif ($request->has('subcategory')) {

    //         $subCategory = SubCategory::where('slug', $request->subcategory)->first();

    //         $products = Product::where([

    //             'sub_category_id' => $subCategory->id,

    //             'status' => 1,

    //             'is_approved' => 1,

    //         ])

    //             ->when($withRange, function ($query) use ($from, $to) {

    //                 return $query->where('price', '>=', $from)->where('price', '<=', $to);
    //             })

    //             ->paginate(12);
    //     } elseif ($request->has('childcategory')) {

    //         $childCategory = ChildCategory::where('slug', $request->childcategory)->first();

    //         $products = Product::where([

    //             'child_category_id' => $childCategory->id,

    //             'status' => 1,

    //             'is_approved' => 1,

    //         ])

    //             ->when($withRange, function ($query) use ($from, $to) {

    //                 return $query->where('price', '>=', $from)->where('price', '<=', $to);
    //             })

    //             ->paginate(12);
    //     }



    //     $categories = Category::where(['status' => 1])->get();

    //     return view('frontend.pages.product', compact('products', 'categories'));
    // }

    public function showProduct(string $slug)
    {
        $product = Product::with(['vendor', 'category', 'productImageGalleries', 'variants', 'brand'])->where('slug', $slug)->where('status', 1)->first();
        return view('frontend.pages.product-detail', compact('product'));
    }

    public function changeListView(Request $request)
    {
        Session::put('product_list_style', $request->style);
    }
}
