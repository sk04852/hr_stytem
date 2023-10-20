@component('mail::message')
# Notify User

<p>{{isset($params['reminder'])? 'Yes': 'No'}}

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Team Thanks,<br>
{{ config('app.name') }}
@endcomponent
