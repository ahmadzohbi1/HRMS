@extends('layouts.email')

@section('body')
    <!-- content -->
    <td valign="top" class="bodyContent" mc:edit="body_content">
        <p>Hi {{$user->name}},</p>
        <p>We are sending you this email because you requested a password reset. Your one time otp password:</p>

        <p class="code">{{ $code }}</p>

        <p>if you didn't request a password reset, you can ignore this email.</p>
    </td>
@endsection