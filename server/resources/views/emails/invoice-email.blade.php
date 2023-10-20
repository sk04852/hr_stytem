<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        #line{
            background: #7a59ad;
        }
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
            padding-left: 7px;
            font-size: 11px;
            border: 1px solid #dddddd;
        }
        tr:nth-child(even) {
            background-color: #dddddd;
        }
        .left-div{
            width:40%;
            float:left;
        }
        .right-div{
            float:right;
        }
        .footer-right-div{
            width:150px;
            float:right;
        }
        .header{
            font-size: 20px;
            color: #7a59ad;
            font-weight: 600;
        }
        .total{
            font-size: 20px;
            color: #7a59ad;
            font-weight: 600;
        }
        .invoice-number{
            color: #7a59ad;
        }

    </style>
</head>
<body>

@component('mail::message')
<h3 class="header"> Invoice </h3> 
<h3 class="invoice-number"> #{{$invoice->invoice_number}} </h3>
<div class="left-div">
    <p class="statement-detail"> <b> Order No: </b> {{ $invoice->order_no }} </p>
    <p class="statement-detail"> <b> Invoice Date: </b> {{ $invoice->invoice_date }} </p>
    <p class="statement-detail"> <b> Delivery Date:  </b> {{ empty($invoice->delivery_date) ? '--' : date('d-m-Y', strtotime($invoice->delivery_date)) }} </p>
    <p class="statement-detail"> <b> Payment Type:  </b> {{ $invoice->payment_type }} </p>
    <p class="statement-detail"> <b> Status:  </b> {{ $invoice->status }} </p>
</div>

<div class="right-div">
    <p class="statement-detail"> <b> Name: </b> {{ $client->first_name. ' '. $client->last_name }} </p> 
    <p class="statement-detail"> <b> Phone : </b> {{ is_null($client->phone) ? '--' : $client->phone }} </p> 
    <p class="statement-detail"> <b> Mobile: </b> {{ is_null($client->mobile) ? '--' : $client->mobile }} </p> 
    <p class="statement-detail"> <b> Fax: </b> {{ is_null($client->fax) ? '--' : $client->fax }} </p> 
    <p class="statement-detail"> <b> Address: </b> {{ is_null($client->address) ? '--' : substr($client->address, 0, 15) }} </p> 
</div>

<table class="table-style">
    <tr>
      <th class="table-heading">id</th>
      <th class="table-heading">Product</th>
      <th class="table-heading">Price</th>
      <th class="table-heading">Qty</th>
      <th class="table-heading">Dis.</th>
      <th class="table-heading">Total</th>
      <th class="table-heading">Sub Total</th>
    </tr>
    @foreach ($products as $product)
      <tr>
          <td class="table-data"> {{$product->id}}</td>
          <td class="table-data"> {{is_null($product->product_name) ? $product->product->name : $product->product_name}}</td>
          <td class="table-data"> {{$product->price}}</td>
          <td class="table-data"> {{$product->quantity}}</td>
          <td class="table-data"> {{$product->discount}}</td>
          <td class="table-data"> {{$product->total}}</td>
          <td class="table-data"> {{$product->sub_total}}</td>
      </tr>
    @endforeach
  </table>
  <br>

<div class="footer-right-div">
    <p class="statement-detail"> <b>Subtotal:</b> <b class="right-div"> {{ is_null($invoice->sub_total) ? '--' :  $invoice->sub_total }}</b> </p>
    <p class="statement-detail"> <b>Discount:</b> <b class="right-div"> {{ $invoice->discount }} </b></p>
    <p class="statement-detail"><b>Ship Charges:</b> <b class="right-div"> {{ $invoice->shipping_charges}} </b></p>
    <hr id="line">
    <p> <b class="total">Total:</b> <b class="right-div"> 3000 </b></p>
</div>
<div class="left-div" style="border: 1px solid #dddddd;padding:5px;margin-top:7px;">
   <h3>Customer Note</h3>
   <p class="statement-detail">{{ is_null($invoice->customer_note) ? '--' :  substr($invoice->customer_note, 0, 100) }}</p>
</div>

@endcomponent

</body>
</html>




