@component('mail::message')
# Introduction

The body of your message.

Password Reset Code : {{ $code }}
@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
