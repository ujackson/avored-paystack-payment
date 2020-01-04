<template>
    <div>
        <a-form-item label="Status">
            <a-select name="paystack_card_status" @change="statusChange" :default-value="data.paystack_card_status">
                <a-select-option value="true">Enabled</a-select-option>
                <a-select-option value="false">Disabled</a-select-option>
            </a-select>
        </a-form-item>
        <input type="hidden" name="paystack_card_status" v-model="status" />
    </div>
</template>

<script>
    export default {
        name: 'paystack-payment-config',
        props: ['data'],
        data () {
            return {
                status: false
            }
        },
        methods: {
            statusChange(val) {
                this.status = val
            }
        },
    }
</script>
