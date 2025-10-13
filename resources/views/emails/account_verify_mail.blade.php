@component('mail::message')
Dear {{$user->name}},

Welcome to Energica!

To ensure the security of your account and provide you with uninterrupted access to our platform, we need to verify your
email address.

Please find the verification code below:
<div style="text-align:center;">
    <strong style="font-size:20px">{{$code}}</strong>

</div>
Once your email address is verified, you'll be all set to explore everything Genesis-lb has to offer, including offers and
benefits.

If you did not register for an account with Genesis-lb, please disregard this email.

If you have any questions or need assistance, feel free to contact our support team at support@Genesis-lb.net

Thank you for choosing Genesis-lb. We're excited to embark on this journey with you!

Best regards,
{{ config('app.company_name') }} Team
@endcomponent