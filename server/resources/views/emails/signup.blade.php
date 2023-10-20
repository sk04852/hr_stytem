@extends('emails.layouts.base')
@section('content')
<h2>Your Investment Account</h2>

<p>Dear {{$data['full_name']}},<br />
<br />
Congratulations, Your new Investment Account has been created. The password that you chose during the registration process is also used for accessing the platform.</p>
<br>

<p>Please find your credentials below:</p><br>
<table style="border: 0px solid black;">
    <tbody>
        <tr style="border: 1px solid black; width:500px;">
            <td style="border: 1px solid black; width:250px;"><span style="font-family: arial, sans-serif; background-color: rgb(255, 255, 255); padding-left: 10px">Account Email</span></td>
            <td style="border: 1px solid black;"><span style="font-family: arial, sans-serif; background-color: rgb(255, 255, 255); padding-left: 10px">{{$data['email']}}</span></td>
        </tr>
        <tr style="border: 1px solid black; width:500px;">
            <td style="border: 1px solid black; width:250px;"><span style="font-family: arial, sans-serif; background-color: rgb(255, 255, 255); padding-left: 10px">Password</span></td>
            <td style="border: 1px solid black; width:250px; padding-left: 10px">{{$data['password_confirmation']}}</td>
        </tr>
    </tbody>
</table>
@endsection
