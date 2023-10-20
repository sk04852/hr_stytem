@extends('emails.layouts.base')
@section('content')
<h2>New Referral Joined</h2>

<p>Dear {{$data['full_name']}},<br />
<br />
Congratulations! One of your referral has just joined the network. You can find the referral details below.</p>
<br>
<p>For detailed information about your referrals, please login to the platform and find the latest updates on your platform dashboard.</p>
<br>
<table style="border: 0px solid black;">
    <tbody>
        <tr style="border: 1px solid black; width:500px;">
            <td style="border: 1px solid black; width:250px;"><span style="font-family: arial, sans-serif; background-color: rgb(255, 255, 255); padding-left: 10px">Account Email</span></td>
            <td style="border: 1px solid black;"><span style="font-family: arial, sans-serif; background-color: rgb(255, 255, 255); padding-left: 10px">{{$data['email']}}</span></td>
        </tr>
        <tr style="border: 1px solid black; width:500px;">
            <td style="border: 1px solid black; width:250px;"><span style="font-family: arial, sans-serif; background-color: rgb(255, 255, 255); padding-left: 10px">Referral</span></td>
            <td style="border: 1px solid black; width:250px; padding-left: 10px">{{$data['referral']}}</td>
        </tr>
    </tbody>
</table>
@endsection