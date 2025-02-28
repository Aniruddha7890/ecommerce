<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\NewsletterSubscriberDataTable;
use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class SubscribersController extends Controller
{
    public function index(NewsletterSubscriberDataTable $datatable)
    {
        return $datatable->render('admin.subscriber.index');
    }

    public function destroy(string $Id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($Id)->delete();
        return response(['status' => 'success', 'message' => 'Subscriber deleted successfully']);
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'subject' => ['required'],
            'message' => ['required'],
        ]);
    }
}
