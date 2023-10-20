<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{is_null($invoice->company_id) ? 'Invoice' : $invoice->company->name}}</title>
    <style>
        #line{
            color: #1d1d1d;

        }
        .left-div p{
  		    font-family: sans-serif;
            color: #5B6165;
            font-size: 11px;

        }
        .note{
            font-family: sans-serif;
            color: #5B6165;
            font-size: 12px;
        }
        .table-style {
            margin-top: 150px;
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            table-layout: auto;
        }
        .table-heading {
            border: 1px solid #1d1d1d;
            text-align: left;
            padding: 6px;
            font-size: 10px;
            background: #1d1d1d;
            color: white;
        }
        .table-data{
            padding: 4px;
            font-size: 10px;
            border: 1px solid #dddddd;
            color: #1d1d11;
        }

        .left-div{
            width:70%;
            float:left;
        }
        .right-div{
            width:30%;
            float:right;
            font-family: sans-serif;
        }
        .right-div p{
  		    font-family: sans-serif;
            color: #5B6165;
            font-size: 11px;

        }
        .footer-right-div{
            width:40%;
            float:right;
        }
        .footer-right-div p{
            font-family: sans-serif;
            color: #5B6165;
            font-size: 12px;
        }
        .footer-left-div{
            width:40%;
            float:left;
            height: 100px;
            word-wrap: break-word;

        }
        .footer-left-div p{
            font-family: sans-serif;
            color: #5B6165;
            font-size: 11px;
            float:left;
        }
        .invoice{
            font-family:sans-serif;
            font-size: 16px;
            background: #1d1d11;
            color: white;
            text-transform:uppercase;
            width: 100%;
            padding-left:10px;
        }
        .total{
            font-family: sans-serif;
            font-size: 20px;
            color: #1d1d1d;

        }
        .invoice-number{
            font-family: sans-serif;
            color: #7a59ad;
        }

        #customer-note{
            border: 1px solid #dddddd;
            padding:5px;
            margin-top:7px;
        }
        .company{
            font-family:sans-serif;
            font-size: 12px;
            color: #5B6165;
            margin-top: 40px;
        }
        .company-info{
            font-weight: bold;
            font-size: 22px;
            color: #1d1d11;
            font-family:sans-serif;
        }
        .line{
            color: #1d1d11;
        }

    </style>
</head>
<body>


    {{-- <img src="{{ asset('uploads/logo/images.png') }}" alt="" style="width:150px;height150px;margin-top: -10px;"> --}}
    <div class="company">
        <h2 class="company-info">{{ is_null($invoice->company_id) ? '--' : $invoice->company->name}}</h2>
        <Address class="">{{ is_null($invoice->company_id) ? '--' : $invoice->company->address}}</Address>
        <span>@if($invoice->company_id == null) '--' @else {{$invoice->company->city}},{{$invoice->company->state}},{{$invoice->company->country}} @endif</span>
        <p>{{is_null($invoice->company_id) ? '--' : $invoice->company->website_link}}</p>
        <span>{{is_null($invoice->company_id) ? '--' : $invoice->company->primary_phone}}</span>
    </div>
    <hr class="line">
    <h3 class="invoice"> Invoice #{{$invoice->invoice_number}}</h3>


<div class="left-div">
    @if($invoice->order_no !== null)
        <p> <b> Order No: </b> {{ $invoice->order_no }} </p>
    @endif
    <p> <b> Invoice Date: </b> {{ $invoice->invoice_date }} </p>
    @if(!empty($invoice->delivery_date))
    <p> <b> Delivery Date:  </b> {{ date('d-m-Y', strtotime($invoice->delivery_date)) }} </p>
    @endif
    <p> <b> Payment Type:  </b> {{ $invoice->payment_type }} </p>

</div>

<div class="right-div">
    @if($invoice->client_id !== null)
    @if($client->type == 'Company') <p> {{  $client->company_name }} </p> @endif
    @endif
    <p> <b> Name: </b>  @if($invoice->client_id == null) -- @else  {{  $client->first_name. ' '. $client->last_name }} @endif</p>
    <p> <b> Phone : </b>  @if($invoice->client_id == null) -- @else  {{  is_null($client->phone) ? '--' : $client->phone }} @endif</p>
    <p> <b> Mobile: </b>  @if($invoice->client_id == null) -- @else  {{  is_null($client->mobile) ? '--' : $client->mobile }} @endif</p>
    {{-- <p> <b> Fax: </b>  @if($invoice->client_id == null) -- @else  {{  is_null($client->fax) ? '--' : $client->fax }} @endif</p>  --}}
    <p> <b> Address: </b>  @if($invoice->client_id == null) -- @else  {{ $client->addresses[0]->address_1 }} ,{{ $client->addresses[0]->zip_code }}, {{ $client->addresses[0]->city }}, {{ $client->addresses[0]->country}} @endif</p>
</div>

<table class="table-style">
    <tr>
      <th class="table-heading">No.</th>
      <th class="table-heading">Quantity</th>
      <th class="table-heading">Item</th>
      <th class="table-heading">Price</th>
      <th class="table-heading">Disc.</th>
      <th class="table-heading">Tax</th>
      <th class="table-heading">Sub Total</th>
      <th class="table-heading">Total</th>
    </tr>
    @foreach ($products as $key =>$product)
      <tr>
          <td class="table-data"> {{++$key}}</td>
          <td class="table-data"> {{$product->quantity}}</td>
          <td class="table-data"> {{is_null($product->product_name) ? $product->product->name : $product->product_name}}</td>
          <td class="table-data"> {{$product->price}}</td>
          <td class="table-data"> {{$product->discount}}</td>
          <td class="table-data"> {{$product->tax}}</td>
          <td class="table-data"> {{$product->sub_total}}</td>
          <td class="table-data"> {{$product->total}}</td>
      </tr>
    @endforeach
  </table>
  <br>

 <div class="footer-right-div">
    <p> <b>Subtotal:</b> <b class="right-div"> {{ is_null($invoice->sub_total) ? '--' :  $invoice->sub_total }}</b> </p>
    <p> <b>Discount:</b> <b class="right-div"> {{ $invoice->discount }} </b></p>
    <p> <b>Tax:</b> <b class="right-div"> {{ $invoice->tax }} </b></p>
    <p><b>Shipping Charges:</b> <b class="right-div"> {{ $invoice->shipping_charges}} </b></p>
    <hr id="line">
    <p> <b class="total">Total:</b> <b class="right-div"> {{$invoice->total}} ({{ is_null($invoice->company_id) ? '--' : $invoice->company->generalSettings->default_currency}})</b></p>
</div>

<div class="footer-left-div" id="customer-note">
    <h3 class="note">Customer Note</h3>
    <p>{{ is_null($invoice->customer_note) ? '--' :  substr($invoice->customer_note, 0, 230) }}</p>
 </div>
 <br><br><br><br><br><br><br>
 <div class="footer-left-div" id="customer-note">
    <h3 class="note">Terms & Conditions</h3>
    <p>{{ is_null($invoice->terms_n_conditions) ? '--' :  substr($invoice->terms_n_conditions, 0, 230) }}</p>
 </div>


</body>
</html>




