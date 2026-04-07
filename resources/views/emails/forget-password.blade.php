@component('mail::message')
# Password Reset Token

Please copy the below token to reset your password.

@component('mail::panel')
    {{$token}}
@endcomponent
Thanks,<br>
{{ config('app.name') }}
@endcomponent