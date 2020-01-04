@if ($payment->enable() === 'true')
    <paystack-card-payment>
    </paystack-card-payment>
    @push('scripts')
        <script src="{{ asset('avored-admin/js/paystack-payment.js') }}"></script>
    @endpush
@endif
