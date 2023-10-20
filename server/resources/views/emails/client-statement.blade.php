<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        .statement-detail{
            font-size: 11px;
        }
        .table-style {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            border: 1px solid #dddddd;
            overflow-x:auto;
            overflow-y:auto;
            width: 100%;
        }
        .table-heading {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 4px;
            font-size: 12px;
        }
        .table-data{
            padding-left: 10px;
            font-size: 11px;
            border: 1px solid #dddddd;
        }
        tr:nth-child(even) {
            background-color: #dddddd;
        }
        .left-div{
            width:70%;
            float:left;
        }
        .right-div{
            float:right;
        }
        .header{
            font-size: 20px;
        }

    </style>
</head>
<body>

@component('mail::message')
<center class="header"> Transactions Statement</center> 
<div class="left-div">
    <p class="statement-detail"> <b> Name: </b> {{ $client->first_name. ' '. $client->last_name }} </p> 
    <p class="statement-detail"> <b> Phone : </b> {{ is_null($client->phone) ? '--' : $client->phone }} </p> 
    <p class="statement-detail"> <b> Mobile: </b> {{ is_null($client->mobile) ? '--' : $client->mobile }} </p> 
    <p class="statement-detail"> <b> Fax: </b> {{ is_null($client->fax) ? '--' : $client->fax }} </p> 
    <p class="statement-detail"> <b> Address: </b> {{ is_null($client->address) ? '0' : $client->address }}</p>
</div>

<div class="right-div">
    <p class="statement-detail"> <b> Slip Created: </b> {{ date('d-m-Y')}} </p>
    <p class="statement-detail"> <b> From Date:  </b> {{ empty($request->from) ? '--' : date('d-m-Y', strtotime($request->from)) }} </p>
    <p class="statement-detail"> <b> To Date:  </b> {{ empty($request->to) ? '--' : date('d-m-Y', strtotime($request->to)) }} </p>
    <p class="statement-detail"> <b> Transaction Type:  </b> {{ (!empty($request->transaction_type) ? ($request->transaction_type == 1 ? 'Debit' : 'Credit') : '--') }} </p>
</div>



<table class="table-style">
    <tr>
      <th class="table-heading">id</th>
      <th class="table-heading">Inv.No</th>
      <th class="table-heading">Transaction Type</th>
      <th class="table-heading">Credit</th>
      <th class="table-heading">Debit</th>
      <th class="table-heading">Date & Time</th>
    </tr>
    @foreach ($transactions as $transactions)
      <tr>
          <td class="table-data"> {{$transactions->id}}</td>
          <td class="table-data"> {{$transactions->transaction_id}}</td>
          <td class="table-data"> {{$transactions->transactionType->action}}</td>
          <td class="table-data"> {{$transactions->credit}}</td>
          <td class="table-data"> {{$transactions->debit}}</td>
          <td class="table-data"> {{$transactions->created_at}}</td>
      </tr>
    @endforeach
  </table>

@endcomponent

</body>
</html>




