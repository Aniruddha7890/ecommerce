<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\SellerPendingProductsDataTable;
use App\DataTables\SellerProductsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SellerProductController extends Controller
{
    public function index(SellerProductsDataTable $datatable){
        return $datatable->render('admin.product.seller-product.index');
    }

    public function pendingProducts(SellerPendingProductsDataTable $datatable){
        return $datatable->render('admin.product.seller-pending-product.index');
    }
}
