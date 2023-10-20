@component('mail::message')

    Dear  {{ $first_name }} You have Created Your New Account.But <br>
    You need to Verify your account first to use our services CLick Below Verify your Email. Enjoy!!
    @component('mail::button', ['url' => ''])
Verify
@endcomponent

Thanks,<br>
@endcomponent
