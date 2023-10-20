@component('mail::message')
# Introduction

The body of your message.
Email Verification Code : {{ $code }}
@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
