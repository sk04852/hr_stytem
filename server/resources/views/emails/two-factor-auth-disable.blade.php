@extends('emails.layouts.base')
@section('content')

<p>Dear {{$user->first_name}} {{$user->last_name}},

<p>Google Two Factor Authentication has been <b>Disabled</b> successfully</p>

@endsection
