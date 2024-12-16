<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomePageSettingController extends Controller
{
    public function index()
    {
        return view('admin.home-page-setting.index');
    }
}
