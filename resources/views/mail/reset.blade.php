@component('mail::message')
# Welcome to Chatwork Forwarder Application

You are receiving this email because we received a password reset request for your account.

Reset your password for account {{ $notifiable->name }}!

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

This password reset link will expire in 60 minutes.

If you did not request a password reset, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
