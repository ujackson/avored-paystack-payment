AvoRed.initialize((Vue) => {
    Vue.component('paystack-card-payment', require('../components/PaystackCardPayment.vue').default)
    Vue.component('paystack-payment-config', require('../components/PaystackPaymentConfig.vue').default)
})
