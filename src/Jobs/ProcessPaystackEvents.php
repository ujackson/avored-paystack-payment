<?php

namespace Ujackson\AvoredPaystack\Jobs;

use App\User;
use AvoRed\Framework\Database\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ujackson\AvoredPaystack\Models\PaystackTransaction;

class ProcessPaystackEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event;

    /**
     * Create a new job instance.
     *
     * @param $event
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->event->obj->event) {
            // charge.success
            case 'charge.success':
                if ('success' === $this->event->obj->data->status) {
                    $order_id = $this->event->obj->data->metadata->custom_fields[0]->value;
                    $user_id = $this->event->obj->data->metadata->custom_fields[1]->value;
                    $reference = $this->event->obj->data->reference;

                    $order = Order::find($order_id)->first();
                    $user = User::find($user_id)->first();

                    PaystackTransaction::where('transaction_ref', $reference)
                        ->where('order_id', $order->id)
                        ->update(['status' => $this->event->obj->data->status]);

                    if ($this->event->obj->data->customer->email === $user->email) {
                        $order->order_status_id = 2;
                        $order->save();
                    }
                }
                break;
        }
    }
}
