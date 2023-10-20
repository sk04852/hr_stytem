@component('mail::message')
# Payment Has Been Send

Transaction of amount {{$paymentInformation->amount}} has been made on {{$paymentInformation->payment_date}}. Thank you!

Thanks,<br>
{{ config('app.name') }}
@endcomponent
