@component('mail::message')

<!DOCTYPE html>
<html>
<head>
<style>

</style>
</head>
<body>
<h1 style="font-size: 32px; text-align: center; color: #718096;">Transaction Alert!</h1>
<p>Following transaction has been <b>Update</b> to your company on {{$paymentInformation->payment_date}}</p>
<table>
<tbody>
    <tr>
        <td><h2 style="font-size: 20px;">Payment Details:</h2></td>
    </tr>
  <tr>
        <td style="font-weight: bold">Client Name: </td>
        <td>{{$clientInformation->first_name}} {{$clientInformation->last_name}}</td>
    </tr>
    <tr>
        <td style="font-weight: bold">Reciever Name: </td>
        <td>{{$userInformation->first_name}}</td>
    </tr>
    <tr>
        <td style="font-weight: bold">Reciever Email: </td>
        <td>{{$userInformation->email}}</td>
    </tr>
    <tr>
        <td style="font-weight: bold">Transaction Type: </td>
        <td>{{$paymentInformation->payment_type}}</td>
    </tr>
    <br>
    <tr>
            <td><h2 style="font-size: 25px;">Amount: {{number_format($paymentInformation->amount, 2)}}</h2></td>
    </tr>
</tbody>

</table>

</body>
</html><br>
Thanks,<br>
{{ config('app.name') }}
@endcomponent


