@extends('emails.layouts.base')
@section('content')

<p>Dear {{$user->first_name}} {{$user->last_name}},
<p>You recently requested to Enable Email 2FA for your CRM Account. To Enable, Please enter the following code.</p>
<p>Your Email Code is: <b>{{$code}}</b> </p>

@endsection
