@extends('emails.layouts.base')
@section('content')
<h2>Password Reset Request</h2>

<p>Dear {{$data['full_name']}},<br />
<br />
You recently requested a password reset for your account login. To complete the process, click the link below.
<br>
<br>
<a href="{{url(env('APP_URL').'/auth/reset_password?token='.$data['token'])}}">Reset Password</a>
<br>
<br>
If you didn’t make this change or if you believe an unauthorized person has accessed your account please ignore this email.
@endsection
