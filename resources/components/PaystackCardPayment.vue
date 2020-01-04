<template>
    <div>
        <a-switch @change="handlePaymentChange($event, 'paystack-card')">
        </a-switch>
        Pay With Card <img src="../img/paystack.png" class="h-60" />
    </div>
</template>

<script>

    export default {
        name: 'paystack-card-payment',
        props: [],
        data () {
            return {
                selectedPaystackPaymentOption: false
            }
        },
        methods: {
            handlePaymentChange(checked, identifier) {
                if (checked) {
                    this.selectedPaystackPaymentOption = true
                } else {
                    this.selectedPaystackPaymentOption = false
                }
                EventBus.$emit('selectedPaymentIdentifier', identifier)
            }
        },
        mounted() {
            var app = this
            var eventBus = EventBus
            eventBus.$on('placeOrderBefore', function() {
                if (app.selectedPaystackPaymentOption) {
                    eventBus.$emit('placeOrderAfter')
                }
            })
        }
    }
</script>
<style scoped>
    .h-60 {
        height: 60px;
        padding-left: 20px;
    }
</style>
