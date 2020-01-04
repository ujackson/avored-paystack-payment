<?php

namespace Ujackson\AvoredPaystack\Http\Controllers;

use App\User;
use AvoRed\Framework\Database\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Ujackson\AvoredPaystack\Jobs\ProcessPaystackEvents;
use Ujackson\AvoredPaystack\Models\PaystackTransaction;

class PaystackController extends Controller
{

    /**
     * Show the payment response resource.
     * @param Request $request
     * @return Response
     */
    public function callback(Request $request)
    {

        if (!$request->has('reference')) {
            abort(404, 'No transaction reference supplied');
        }

        try {
            $paystack = app()->make('Paystack');
            $tranx = $paystack->transaction->verify([
                'reference' => $request->reference,
            ]);

            $order_id = $tranx->data->metadata->custom_fields[0]->value;
            $user_id = $tranx->data->metadata->custom_fields[1]->value;

            $order = Order::find($order_id);
            $user = User::find($user_id);

            PaystackTransaction::where('transaction_ref', $request->reference)
                ->where('order_id', $order->id)
                ->update(['status' => $tranx->data->status]);

            if ('success' === $tranx->data->status) {
                // Give value
                if ($tranx->data->customer->email === $user->email) {
                   //update order status
                  $order->order_status_id = 2;
                  $order->save();
                }
            }
            return redirect()
                ->route('order.successful', $order->id)
                ->with('success', 'Order Placed Successfuly!');
        } catch (\Throwable $th) {
            //dd($th);
        }
    }

    /**
     * Get payment events.

     * @return Response
     */
    public function webhook(Request $request)
    {
        $event = \Yabacon\Paystack\Event::capture();
        http_response_code(200);
        Log::info('PaystackEvents - ' . $event->raw);

        if (!$event->validFor(config('paystack.secretKey'))) {
            die();
        }

        ProcessPaystackEvents::dispatch($event)->delay(now()->addSeconds(5));
    }
}
