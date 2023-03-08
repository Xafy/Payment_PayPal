<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Omnipay\Omnipay;
use Throwable;

class PaymentController extends Controller
{
    private $gateway;

    public function __construct()
    {   
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->initialize(array(
                'clientId' => env('PAYPAL_CLIENT_ID'),
                'secret'   => env('PAYPAL_CLIENT_SECRET'),
                'testMode' => true
            ));
    }

    public function index(){
        return view('index');
    }

    public function pay(Request $req){
        $req->validate(['amount' => 'required|numeric']);
        try {
            $response = $this->gateway->purchase(array(
                'amount'        => $req->amount,
                'currency'      => env('PAYPAL_CURRENCY'),
                'description'   => 'This is a test purchase transaction.',
                'returnUrl'     => url('success'),
                'cancelUrl'     => url('error'),
                ))->send();
            session()->put([
                'amount' => $response->getData()['transactions'][0]['amount']['total'],
                'currency' => $response->getData()['transactions'][0]['amount']['currency'] ]);
            if ($response->isRedirect()){
                $response->redirect();
            } else {
                return redirect()->to(route('index'))->with(['error' => $response->getMessage() ?? "Enter a correct value"]);
            }
        } catch (Throwable $th){
            return redirect()->to(route('index'))->with(['error' => $th->getMessage() ?? "Enter a correct value"]);
        }
    }

    public function success(Request $req){
            try {
                $response = $this->gateway->completePurchase(array(
                    'payer_id' => $req->input('PayerID'),
                    'transactionReference' => $req->input('paymentId')
                ))->send();
                if($response->isSuccessful()){
                    $data = $response->getData();
                    Payment::create([
                        'payment_id' =>$data['id'], 
                        'payer_id' =>$data['payer']['payer_info']['payer_id'], 
                        'payer_email' =>$data['payer']['payer_info']['email'], 
                        'amount' =>$data['transactions'][0]['amount']['total'], 
                        'currency' =>env('PAYPAL_CURRENCY'), 
                        'status' =>$data['state'],
                    ]);
                    $result = ['success' => "Payment was completed successfuly"];
                } else {
                    $result = ['error' => $response->getMessage() ?? "Payment was not completed successfuly"];
                }
            } catch (Throwable $err) {
                Payment::create([
                    'payment_id' =>$req->input('paymentId'), 
                    'payer_id' =>$req->input('PayerID'), 
                    'payer_email' =>$req->input('PayerId'), 
                    'amount' =>session()->get('amount'), 
                    'currency' =>session()->get('currency'), 
                    'status' => "Failed",
                ]);;
                $result = ['error' => "Something went wrong with the transaction"];
            }
        return redirect()->to('payments')->with($result);
    }

    public function error(){
        $result = "Somthing went wrong";
        return redirect()->to('payments')->with(['error' => $result]);
    }
    
    public function showPayments(){
        $payments = Payment::query()->orderByDesc('created_at')->get();
        return view('payments', compact('payments'));
    }

}