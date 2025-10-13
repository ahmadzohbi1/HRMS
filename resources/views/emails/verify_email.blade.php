@extends('layouts.email')

@section('body')
    <!-- content -->
    <td valign="top" class="bodyContent" mc:edit="body_content">
        <p>Dear {{$user->name}},</p>

        <p>Welcome to Genesis-lb!</p>

        <p>To ensure the security of your account and provide you with uninterrupted access to our platform, we need to
            verify your email address.
        </p>

        <p class="mb-0">Please find the verification code below:</p>
        <p class="mt-0">{{$code}}</p>

        <p>
            Once your email address is verified, you'll be all set to explore everything Genesis-lb has to offer, including
            offers and benefits.
        </p>

        <p>
            If you did not register for an account with Genesis-lb, please disregard this email.
        </p>

        <p>
            If you have any questions or need assistance, feel free to contact our support team at
            <a href="mailto:support@Genesis-lb.net">support@Genesis-lb.net.</a>
        </p>

        <p>
            Thank you for choosing Genesis-lb. We're excited to embark on this journey with you!
        </p>

        <p>
            Best regards,<br>
            {{config("app.company_name")}} Team
        </p>
    </td>
@endsection
