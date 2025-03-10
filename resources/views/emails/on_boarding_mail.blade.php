@component('mail::message')
    Hello {{$user->name}},

    Welcome aboard!

    We're excited to have you join Alzohbi.

    Introducing Alzohbi – the future of mobile connectivity.

    Why Choose Alzohbi?
        > Instant activation, no physical SIM required.
        > Global coverage in 140+ countries.
        > The most competitive eSIM rates in the market.

    Ready to unlock a world of possibilities? Get started now!

    Questions? We're here to help.
    If you have any questions or need assistance, don't hesitate to reach out to our support team.

    Happy exploring!

    Best regards,
    {{ config('app.company_name') }} Team
@endcomponent