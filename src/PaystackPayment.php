<?php


namespace Ujackson\AvoredPaystack;
use App\User;
use AvoRed\Framework\Database\Contracts\ConfigurationModelInterface;
use AvoRed\Framework\Database\Models\Order;
use Ujackson\AvoredPaystack\Helpers\TransRef;
use Ujackson\AvoredPaystack\Models\PaystackTransaction;
use Yabacon\Paystack\MetadataBuilder;

/**
 * Class PaystackPayment
 * @package Ujackson\AvoredPaystack
 */
class PaystackPayment
{

    /**
     *
     */
    public const CONFIG_KEY = 'paystack_card_status';

    /**
     * Identifier for this Payment Option.
     *
     * @var string
     */
    protected $identifier = 'paystack-card';

    /**
     * Title for this Payment Option.
     *
     * @var string
     */
    protected $name = 'Paystack Payment Gateway';

    /**
     * Payment options View Path.
     *
     * @var string
     */
    protected $view = 'paystack-card::index';

    /**
     * @var mixed
     */
    protected $configRepo;


    protected $enable;

    /**
     * PaystackPayment constructor.
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct()
    {
        $this->configRepo = app()->make(ConfigurationModelInterface::class);
        $this->enable = $this->configRepo->getValueByCode(self::CONFIG_KEY);
    }

    /**
     * Get Identifier for this Payment Option.
     *
     * return string
     */
    public function identifier()
    {
        return $this->identifier;
    }


    public function enable()
    {
        return $this->enable;
    }



    /**
     * Attempt to initialize paystack payment.
     *
     * return
     * @param Order $order
     * @param User $user
     * @param float $orderAmount
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function process(Order $order, User $user, float $orderAmount)
    {
        if ($orderAmount <= 0) {
            return;
        }

        // default currency code
        $currencyCode = strtoupper(session()->get('default_currency')->code);

        // Paystack API wrapper
        $paystack = app()->make('Paystack');

        $ref = TransRef::getHashedToken();
        $fee = new \Yabacon\Paystack\Fee();
        $amountKobo = $orderAmount * 100; //kobo;
        $gatewayFee = $fee->calculateFor($amountKobo);
        $totalAmount = $fee->addFor($amountKobo);

        $builder = new MetadataBuilder();
        $builder->withCustomField('order_id', $order->id);
        $builder->withCustomField('user_id', $user->id);
        $builder->withCustomField('user_ip', request()->ip());

        // initialize payment gateway
        $gateway = $paystack->transaction->initialize([
            'reference' => $ref,
            'amount' => $totalAmount,
            'email' => $user->email,
            'callback_url' => route('paystack.callback'),
            'metadata' => $builder->build(),
        ]);

        if ($gateway->status === true) {
            //Save transaction
            PaystackTransaction::create([
                'transaction_amount' => $orderAmount,
                'transaction_ref' => $ref,
                'order_id' => $order->id,
            ]);
            return $gateway->data->authorization_url;
        }
    }

    /**
     * Retrieve transaction for a given orderId.
     * @param null $orderId
     * @return
     */
    public function getTransactionByOrderId($orderId = null)
    {
        return PaystackTransaction::where('order_id', $orderId)->firstOrFail();
    }

    /**
     * Get Title for this Payment Option.
     *
     * return boolean
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Payment Option View Path.
     * return String
     */
    public function view()
    {
        return $this->view;
    }

    /**
     * Render Payment Option
     * return String
     */
    public function render()
    {
        return view($this->view())->with($this->with());
    }


    /**
     * Payment Option View Data.
     *
     * return Array
     */
    public function with()
    {
        return ['payment' => $this];
    }
}
