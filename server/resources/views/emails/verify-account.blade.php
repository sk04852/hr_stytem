@extends('emails.layouts.base')
@section('content')
<h2>Verify your email</h2>

<p>Dear {{$data['full_name']}},<br />
<br />
You have successfully signed up for Digibits CRM Account, please click the link below to activate your account.
<br>
<br>
<a href="{{url(env('APP_URL').'/auth/activate?token='.$data['token'])}}">Reset Password</a>
<br>
<br>
@endsection
