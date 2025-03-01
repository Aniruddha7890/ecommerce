<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\NewsletterSubscriberDataTable;
use App\Http\Controllers\Controller;
use App\Mail\Newsletter;
use App\Helper\MailHelper;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscribersController extends Controller
{
    public function index(NewsletterSubscriberDataTable $datatable)
    {
        return $datatable->render('admin.subscriber.index');
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'subject' => ['required'],
            'message' => ['required'],
        ]);

        $emails = NewsletterSubscriber::where('is_verified', 1)->pluck('email')->toArray();

        // set mail config
        MailHelper::setMailConfig();

        Mail::to($emails)->send(new Newsletter($request->subject, $request->message));
        toastr('Mail has been sent', 'success', 'Success');

        return redirect()->back();
    }

    public function destroy(string $Id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($Id)->delete();
        return response(['status' => 'success', 'message' => 'Subscriber deleted successfully']);
    }
}
