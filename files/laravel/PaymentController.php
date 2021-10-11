<?php

namespace App\Http\Controllers;

use Paystack;

use App\Models\Donations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


class PaymentController extends Controller
{
    public function confirmpay(Request $request){
        $amount = $request->amount * 100;

        $response =  $request;

        return view('default.confirm', compact('amount', 'response'));


    }
    public function redirectToGateway()
    {   
      
        try{
            return Paystack::getAuthorizationUrl()->redirectNow();
        }catch(\Exception $e) {
            return Redirect::back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
        }        
       
    }

    public function handleGatewayCallback(Request $request)
    {
     
        $paymentDetails = $request;
        $donation = new Donations;

        $donation->campaigns_id = 1;
        $donation->txn_id = $paymentDetails->trans_id;
        $donation->fullname = $paymentDetails->name;
        $donation->email = $paymentDetails->email;
        $donation->country = "Nigeria";
        $donation->postal_code = "353452";
        $donation->donation = $paymentDetails->amount;
        $donation->payment_gateway = $paymentDetails->payment;
        $donation->oauth_uid = 33;
        
        $donation->anonymous = 0;
        $donation->state = $paymentDetails->state;
        $donation->address = $paymentDetails->address;
        $donation->phone = $paymentDetails->phone;
    
        $donation->save();
  
        return view('default.success');




        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want
    }
}
