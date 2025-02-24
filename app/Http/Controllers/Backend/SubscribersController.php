<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\NewsletterSubscriberDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscribersController extends Controller
{
    public function index(NewsletterSubscriberDataTable $datatable)
    {
        return $datatable->render('admin.subscriber.index');
    }
}
