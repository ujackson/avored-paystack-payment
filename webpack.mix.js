let mix = require('laravel-mix')

mix.setPublicPath('dist')
    .js('resources/js/paystack-payment.js', 'js/paystack-payment.js')
