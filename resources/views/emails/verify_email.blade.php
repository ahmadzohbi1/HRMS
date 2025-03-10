@extends('layouts.email')

@section('body')
    <!-- content -->
    <td valign="top" class="bodyContent" mc:edit="body_content">
        <p>Dear {{$user->name}},</p>

        <p>Welcome to Alzohbi!</p>

        <p>To ensure the security of your account and provide you with uninterrupted access to our platform, we need to
            verify your email address.
        </p>

        <p class="mb-0">Please find the verification code below:</p>
        <p class="mt-0">{{$code}}</p>

        <p>
            Once your email address is verified, you'll be all set to explore everything Alzohbi has to offer, including
            offers and benefits.
        </p>

        <p>
            If you did not register for an account with Alzohbi, please disregard this email.
        </p>

        <p>
            If you have any questions or need assistance, feel free to contact our support team at
            <a href="mailto:support@Alzohbi.net">support@Alzohbi.net.</a>
        </p>

        <p>
            Thank you for choosing Alzohbi. We're excited to embark on this journey with you!
        </p>

        <p>
            Best regards,<br>
            {{config("app.company_name")}} Team
        </p>
    </td>
@endsection
