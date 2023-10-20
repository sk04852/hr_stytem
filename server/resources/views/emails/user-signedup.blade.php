@extends('emails.layouts.base')
@section('content')

<h2 style="text-align: center">Welcome to Stack Terminal</h2>

<p>Dear {{$data['first_name']}} {{$data['last_name']}},<br />
<br />
Good to have you onboard, your account is not activated yet, please click the link below to verify your email and activate your account.
<br>
<br>
Upon your first login you will be propmted to setup your company account, please follow the steps to setup your company.

<div style="text-align: center; width: 100%">
    <a href="{{url('auth/verify', ['token'=> $data['email_verification_token']])}}"><p>Verifiy Email</p></a>
</div>
<br>

@endsection
