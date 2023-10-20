@extends('emails.layouts.base')
@section('content')

<p>Dear {{$user->first_name}} {{$user->last_name}},

<p>Your CRM Account is protected with Email 2FA. Please You can login to the CRM platform using following OTP</p>
<p>Your One-Time Password is: <b>{{$code}}</b> </p>

@endsection
