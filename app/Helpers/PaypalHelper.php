<?php

namespace App\Helpers;

use File;
use Auth;
use Zip;
use URL;
use Mail;
use Session;
use Redirect;
use Carbon\Carbon;
use Response;
use Exception;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Plan;
use PayPal\Api\Payee;
use PayPal\Api\Payment;
use PayPal\Api\Agreement;
use PayPal\Api\PayerInfo;
use PayPal\Api\Transaction;
use PayPal\Api\Transactions;
use PayPal\Api\RedirectUrls;
use Illuminate\Http\Request;
use PayPal\Rest\ApiContext;
use PayPal\Api\PaymentHistory;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\PaymentExecution;
use Illuminate\Support\Facades\Input;

class PaypalHelper {

    /**
     * Send On Paypall Paymentgateway
     *
     * @param Exception $exception
     * @param string    $exMessage
     * @param boolean   $handler
     */
    public static function PayWithPaypal($MyAmount, $api_context) {

        $MyAmount = Session::get('paypal_amount');
        
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $item_1 = new Item();
        $item_1->setName('Item 1') /** item name * */
                ->setCurrency('USD')
                ->setQuantity(1)
                ->setPrice($MyAmount);/** unit price * */
        $item_list = new ItemList();
        $item_list->setItems(array($item_1));

        $amount = new Amount();
        $amount->setCurrency('USD')
                ->setTotal($MyAmount);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
                ->setItemList($item_list)
                ->setDescription('Your transaction description');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('status')) /** Specify return URL * */
                ->setCancelUrl(URL::route('status'));

        $payment = new Payment();
        $payment->setIntent('Sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($transaction));

        /** dd($payment->create($api_context));exit; * */
        try {

          $paymentdetail =  $payment->create($api_context);
//dd($paymentdetail);
//die('inside try ');
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            dd('incache');
echo '<pre>';print_r(json_decode($ex->getData()));exit;
            if (\Config::get('app.debug')) {

                \Session::put('error', 'Connection timeout');
                return Redirect::route('front_dashboard');
            } else {

                \Session::put('error', 'Some error occur, sorry for inconvenient');
                return Redirect::route('front_dashboard');
            }
        }

        foreach ($payment->getLinks() as $link) {

            if ($link->getRel() == 'approval_url') {

                $redirect_url = $link->getHref();
                break;
            }
        }

        /** add payment ID to session * */
        Session::put('paypal_payment_id', $payment->getId());

        if (isset($redirect_url)) {

            /** redirect to paypal * */
            //return Redirect::away($redirect_url);
            return $redirect_url;
        }

        \Session::put('error', 'Unknown error occurred');
        return Redirect::route('front_dashboard');
    }

}
