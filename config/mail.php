<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send any email
    | messages sent by your application. Alternative mailers may be setup
    | and used as needed; however, this mailer will be used by default.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers to be used while
    | sending an e-mail. You will specify which one you are using for your
    | mailers below. You are free to add additional mailers as required.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses",
    |            "postmark", "log", "array", "failover"
    |
    */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.gmail.com'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME', 'ahmadzohby1999@gmail.com'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'https://office.utopialebanon.org'), PHP_URL_HOST)),
            'auth_mode' => null,
        ],

        'ses' => [
            'transport' => 'ses',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        ],

        'mailgun' => [
            'transport' => 'mailgun',
            'client' => [
                'timeout' => 60,
            ],
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'ahmadzohby1999@gmail.com'),
        'name' => env('MAIL_FROM_NAME', 'Alzohbi HR System'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Reply-To Address
    |--------------------------------------------------------------------------
    |
    | Configure a global reply-to address for better email management.
    | This helps ensure replies go to the right department.
    |
    */

    'reply_to' => [
        'address' => env('MAIL_REPLY_TO_ADDRESS', 'ahmadzohby1999@gmail.com'),
        'name' => env('MAIL_REPLY_TO_NAME', 'Ahmad Zohby - Alzohbi HR'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Email Configuration
    |--------------------------------------------------------------------------
    |
    | This is the email address where admin notifications will be sent,
    | such as new vacation requests, system alerts, and other administrative
    | notifications that require management attention.
    |
    */

    'admin_email' => env('MAIL_ADMIN_EMAIL', 'ahmadzohby1999@gmail.com'),

    /*
    |--------------------------------------------------------------------------
    | HR Department Email Configuration
    |--------------------------------------------------------------------------
    |
    | Email addresses for different HR functions. You can expand this
    | section to include multiple HR email addresses for different purposes.
    |
    */

    'hr_emails' => [
        'general' => env('MAIL_HR_GENERAL', 'ahmadzohby1999@gmail.com'),
        'vacation' => env('MAIL_HR_VACATION', 'ahmadzohby1999@gmail.com'),
        'payroll' => env('MAIL_HR_PAYROLL', 'ahmadzohby1999@gmail.com'),
        'recruitment' => env('MAIL_HR_RECRUITMENT', 'ahmadzohby1999@gmail.com'),
        'training' => env('MAIL_HR_TRAINING', 'ahmadzohby1999@gmail.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Management Email Configuration
    |--------------------------------------------------------------------------
    |
    | Email addresses for management and executive notifications.
    |
    */

    'management_emails' => [
        'ceo' => env('MAIL_MANAGEMENT_CEO', 'ahmadzohby1999@gmail.com'),
        'hr_manager' => env('MAIL_MANAGEMENT_HR_MANAGER', 'ahmadzohby1999@gmail.com'),
        'operations' => env('MAIL_MANAGEMENT_OPERATIONS', 'ahmadzohby1999@gmail.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for email sending to prevent abuse and
    | ensure compliance with email service provider limits.
    |
    */

    'rate_limits' => [
        'per_minute' => env('MAIL_RATE_LIMIT_PER_MINUTE', 60),
        'per_hour' => env('MAIL_RATE_LIMIT_PER_HOUR', 1000),
        'per_day' => env('MAIL_RATE_LIMIT_PER_DAY', 10000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configure email queue settings for better performance and reliability.
    |
    */

    'queue' => [
        'enabled' => env('MAIL_QUEUE_ENABLED', true),
        'connection' => env('MAIL_QUEUE_CONNECTION', 'database'),
        'queue' => env('MAIL_QUEUE_NAME', 'emails'),
        'delay' => env('MAIL_QUEUE_DELAY', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Logging
    |--------------------------------------------------------------------------
    |
    | Configure email logging for monitoring and debugging.
    |
    */

    'logging' => [
        'enabled' => env('MAIL_LOGGING_ENABLED', true),
        'channel' => env('MAIL_LOG_CHANNEL', 'mail'),
        'level' => env('MAIL_LOG_LEVEL', 'info'),
        'include_body' => env('MAIL_LOG_INCLUDE_BODY', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Testing Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for email testing in different environments.
    |
    */

    'testing' => [
        'to' => env('MAIL_TEST_TO', 'ahmadzohby1999@gmail.com'),
        'enabled' => env('MAIL_TESTING_ENABLED', false),
        'log_only' => env('MAIL_TEST_LOG_ONLY', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */

    'markdown' => [
        'theme' => env('MAIL_MARKDOWN_THEME', 'default'),
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Templates Configuration
    |--------------------------------------------------------------------------
    |
    | Configure default email template settings and branding.
    |
    */

    'templates' => [
        'logo_url' => env('MAIL_LOGO_URL', asset('images/alzohbi-logo.png')),
        'company_name' => env('COMPANY_NAME', 'Alzohbi'),
        'company_address' => env('COMPANY_ADDRESS', 'Lebanon'),
        'company_phone' => env('COMPANY_PHONE', '+961-XX-XXXXXX'),
        'company_website' => env('APP_URL', 'https://office.utopialebanon.org'),
        'social_media' => [
            'facebook' => env('COMPANY_FACEBOOK'),
            'twitter' => env('COMPANY_TWITTER'),
            'linkedin' => env('COMPANY_LINKEDIN'),
            'instagram' => env('COMPANY_INSTAGRAM'),
        ],
    ],

];