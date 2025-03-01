<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
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
        } else if ($request->has('brand')) {
            $brand = Brand::where('slug', $request->brand)->firstOrFail();
            $products = Product::where([
                'brand_id' => $brand->id,
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
        } else if ($request->has('search')) {
            $products = Product::where(['status' => 1, 'is_approved' => 1])
                ->where(function ($query) use ($request) {
                    $query
                        ->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('short_description', 'like', '%' . $request->search . '%')
                        ->orWhere('long_description', 'like', '%' . $request->search . '%');
                })
                // ->orWhereHas('category', function ($query) use ($request) {
                //     $query->where('name', 'like', '%' . $request->search . '%')
                //         ->orWhere('long_description', 'like', '%' . $request->search . '%');
                // })
                ->orWhereRelation('category', 'categories.name', 'like', '%' . $request->search . '%')
                ->orWhereRelation('sub_category', 'sub_categories.name', 'like', '%' . $request->search . '%')
                ->orWhereRelation('child_category', 'child_categories.name', 'like', '%' . $request->search . '%')
                ->paginate(12);
        } else {
            $products = Product::where(['status' => 1, 'is_approved' => 1])
                ->orderBy('id', 'DESC')
                ->paginate(12);
        }

        $brands = Brand::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();
        return view('frontend.pages.product', compact('products', 'categories', 'brands'));
    }

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
