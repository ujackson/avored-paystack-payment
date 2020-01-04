<?php
namespace Ujackson\AvoredPaystack;

use Illuminate\Support\ServiceProvider;
use AvoRed\Framework\Support\Facades\Payment;
use AvoRed\Framework\Support\Facades\Tab;
use AvoRed\Framework\Tab\TabItem;
use Yabacon\Paystack;

class Module extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfigData();
        $this->registerResources();
        $this->registerPaymentOption();
        $this->registerTab();
        $this->publishFiles();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Paystack', function ($app) {
            return new Paystack($app['config']['paystack']['secretKey']);
        });
    }

    /**
     * Registering Ujackson AvoredPaystack Resource
     * e.g. Route, View, Database  & Translation Path
     *
     * @return void
     */
    protected function registerResources(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'paystack-card');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'paystack-card');
    }

    /**
     * @return void
     */
    public function registerConfigData(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/paystack.php', 'paystack');
    }

    /**
     * Register Payment Option for App.
     *
     * @return void
     */
    protected function registerPaymentOption(): void
    {
        $payment = new PaystackPayment();
        Payment::put($payment);
    }

    /**
     * Register Menu Tab.
     *
     * @return void
     */
    public function registerTab()
    {
        Tab::put('system.configuration', function (TabItem $tab) {
            $tab->key('system.configuration.paystack-payment')
                ->label('paystack-card::paystack-payment.config-title')
                ->view('paystack-card::system.configuration.payment-card');
        });
    }
    /**
     * Publish Files for this module.
     * @return void
     */
    public function publishFiles()
    {
        $this->publishes([
            __DIR__ . '/../dist/js' => public_path('avored-admin/js'),
        ]);
    }
}
