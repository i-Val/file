<?php

namespace App\Http\Controllers;

use App\Models\Donations;
use Illuminate\Http\Request;
use KingFlamez\Rave\Facades\Rave as Flutterwave;


class FlutterwaveController extends Controller
{
    public function initialize()
    {
        //This generates a payment reference
        $reference = Flutterwave::generateReference();

        // Enter the details of the payment
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => request()->amount,
            'email' => request()->email,
            'tx_ref' => $reference,
            'currency' => "NGN",
            'redirect_url' => route('flutterwavecallback'),
            'customer' => [
                'email' => request()->email,
                "phone_number" => "0000",
                "name" => request()->name
            ],

            "customizations" => [
                "title" => 'Donation',
                "description" => "Donation for the cause"
            ]
        ];

        $payment = Flutterwave::initializePayment($data);


        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return;
        }

        return redirect($payment['data']['link']);
    }

    public function callback(Request $request)
    {   
        dd($request);
        $paymentDetails = $request;
        $donation = new Donations;

        $donation->campaigns_id = 1;
        $donation->txn_id = $paymentDetails->trans_id;
        $donation->fullname = $paymentDetails->name;
        $donation->email = $paymentDetails->email;
        $donation->country = "Nigeria";
        $donation->postal_code = "353452";
        $donation->donation = $paymentDetails->amount;
        $donation->payment_gateway = "Flutterwave";
        $donation->oauth_uid = 33;
        
        $donation->anonymous = 0;
        $donation->state = $paymentDetails->state;
        $donation->address = $paymentDetails->address;
        $donation->phone = $paymentDetails->phone;
    
        $donation->save();
  
        return view('default.success');

    }

    public function success(){
        return view('default.success');
    }
}
