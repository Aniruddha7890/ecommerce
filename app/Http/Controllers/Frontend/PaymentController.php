<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaypalSetting;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    public function index()
    {
        if (!Session::has('shipping_address')) {
            return redirect()->route('user.checkout');
        }
        return view('frontend.pages.payment');
    }

    public function paymentSuccess(){
        return view('frontend.pages.payment-success');
    }

    public function storeOrder($paymentMethod, $paymentStatus){
        $setting = GeneralSetting::first();

        $order = new Order();
        $order->invoice_id = rand(1, 999999);
        $order->user_id = Auth::user()->id;
        $order->sub_total = getMainCartTotal();
        $order->amount = getFinalPayableAmount();
        $order->currency_name = $setting->currency_name;
        $order->currency_icon = $setting->currency_icon;
        $order->product_qty = \Cart::content()->count();
        $order->payment_method = $paymentMethod;
        $order->payment_status = $paymentStatus;
        $order->order_address = json_encode(Session::key('shipping_address'));
        $order->shipping_method = json_encode(Session::key('shipping_method'));
        $order->coupon = json_encode(Session::key('coupon'));
        $order->order_status = 0;
        $order->save();

        // store order product
        foreach(\Cart::content() as $item){
            $product = Product::findOrFail($item->id);

            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $product->id;
            $orderProduct->vendor_id = $product->vendor_id;
            $orderProduct->product_name = $product->name;
            $orderProduct->variants = json_encode($item->options->variants);
            $orderProduct->variants_total = $item->options->variants_total;
            $orderProduct->unit_price = $item->price;
            $orderProduct->qty = $item->qty;
            $orderProduct->save();
        }

        // store transaction details
        
    }

    public function paypalConfig()
    {
        $paypalSetting = PaypalSetting::first();
        $config = [
            'mode'    => $paypalSetting->mode == 1 ? 'live' : 'sandbox',
            'sandbox' => [
                'client_id'         => $paypalSetting->client_id,
                'client_secret'     => $paypalSetting->secret_key,
                'app_id'            => 'APP-80W284485P519543T',
            ],
            'live' => [
                'client_id'         => $paypalSetting->client_id,
                'client_secret'     => $paypalSetting->secret_key,
                'app_id'            => '',
            ],

            'payment_action' => 'Sale',
            'currency'       => $paypalSetting->currency_name,
            'notify_url'     => '',
            'locale'         => 'en_US',
            'validate_ssl'   => true,
        ];

        return $config;
    }

    public function payWithPaypal()
    {
        $config = $this->paypalConfig();
        $paypalSetting = PaypalSetting::first();
        $provider = new PayPalClient($config);
        $provider->getAccessToken();

        // calculate payable amount depending on currency rate
        $total = getFinalPayableAmount();
        $payableAmount = round($total * $paypalSetting->currency_rate, 2);

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('user.paypal.success'),
                "cancel_url" => route('user.paypal.cancel')
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => $config['currency'],
                        "value" => $payableAmount
                    ]
                ]
            ]
        ]);

        if(isset($response['id']) && $response['id'] != null){
            foreach($response['links'] as $link){
                if($link['rel'] == 'approve'){
                    return redirect()->away($link['href']);
                }
            }
        } else{
            return redirect()->route('user.paypal.cancel');
        }
    }

    public function paypalSuccess(Request $request){
        $config = $this->paypalConfig();
        $provider = new PayPalClient($config);
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);

        if(isset($response['status']) && $response['status'] == 'COMPLETED'){
            return redirect()->route('user.payment.success');
        } else{
            return redirect()->route('user.paypal.cancel');
        }
    }

    public function paypalCancel(){
        toastr('Something went wrong, try again later!', 'error', 'Payment Failed!');
        return redirect()->route('user.payment');
    }
}
