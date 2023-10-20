@component('mail::message')

Welcome to Our Company Use {{$employee_number}} To Make your New Account<br>
  CLick Below To Create Your New Account. Enjoy!!
    @component('mail::button', ['url' => ''])
Verify
@endcomponent

Thanks,<br>
@endcomponent
