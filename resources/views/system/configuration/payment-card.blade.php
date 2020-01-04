@php
    $data = collect();
    $data->put('paystack_card_status', $repository->getValueByCode('paystack_card_status'));
@endphp

<paystack-payment-config :data="{{ $data }}"></paystack-payment-config>

@push('scripts')
<script src="{{ asset('avored-admin/js/paystack-payment.js') }}"></script>
@endpush
