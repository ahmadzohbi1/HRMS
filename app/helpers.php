<?php

use App\Models\OrderItem;
use App\Models\Providers\MontyeSim;
use App\Models\ResellerOrderItem;
use App\Notifications\FcmNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\EmailTemplate;
use App\Models\VerificationCode;
use App\Models\Setting;
use App\Mail\AccountVerifyMail;
use App\Mail\OnBoardingMail;
use App\Mail\OrderPlaceMail;
use App\Mail\ResellerRequestApprovedMail;

if (!function_exists("singular")) {
    //make title singuler
    function singular(string $title)
    {
        return substr($title, 0, -1);
    }
}

if (!function_exists("getFile")) {
    function getFile($model)
    {
        Log::info($model->getFirstMedia());
        return $model->getFirstMedia() ? asset($model->getFirstMedia()->getUrl()) : URL::zasset('assets/images/placeholder.png');
    }
}

if (!function_exists("getAvatar")) {
    function getAvatar($user)
    {
        return $user->getFirstMedia() ? asset($user->getFirstMedia()->getUrl()) : 'https://ui-avatars.com/api/?background=random&name=' . $user->name;
    }
}

//return error message with file name and line number
if (!function_exists("showErrorMessage")) {
    function showErrorMessage($e)
    {
        // check env if its not in production, then show full message
        if (config('app.env') != 'production') {
            return $e->getMessage() . " in " . $e->getFile() . " at line " . $e->getLine();
        } else {
            return $e->getMessage();
        }
    }
}

if (!function_exists("getAvatar")) {
    function getAvatar($user)
    {
        return $user->getFirstMedia() ? asset($user->getFirstMedia()->getUrl()) : 'https://ui-avatars.com/api/?background=random&name=' . $user->name;
    }
}

if (!function_exists("getDayDiff")) {
    function getDayDiff($data)
    {
        $expire_at = Carbon::parse($data);
        $now = Carbon::now();
        $diff = $now->diffInDays($expire_at, false);
        return $diff;
    }
}

if (!function_exists("formatPrice")) {
    function formatPrice($price)
    {
        return number_format($price, 0, '') . ' $';
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date)
    {
        return $date ? Carbon::parse($date)->format('Y-m-d H:i:s') : ''; // Customize the format as needed
    }
}

function date_to_days($start_date, $end_date)
{
    $datetime1 = new DateTime($start_date);
    $datetime2 = new DateTime($end_date ?: date("Y-m-d", strtotime($start_date . " +1 day")));
    $interval = $datetime1->diff($datetime2);
    return (int)$interval->format('%a');
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

if (!function_exists('generate_referer_code')) {
    function generate_referer_code()
    {
        $ref_code = strtoupper(Str::random(10));

        if (\App\Models\User::where('ref_code', '=', $ref_code)->exists()) {
            return generate_referer_code();
        }

        return $ref_code;
    }
}

if (!function_exists('module_config')) {
    function module_config($module, $key)
    {
        try {
            $config = \App\Models\ModuleItem::where('module_id', $module->id)
                ->where('key', $key)
                ->first();
        } catch (Exception $exception) {
            return null;
        }

        return isset($config) ? $config : null;
    }
}

if (!function_exists('convertToMB')) {
    function convertToMB($size, $unit)
    {
        $size = (float)$size; // Ensure the size is a float value
        switch (strtoupper($unit)) {
            case 'GB':
                return $size * 1024; // 1 GB = 1024 MB
            case 'TB':
                return $size * 1024 * 1024; // 1 TB = 1024 * 1024 MB
            case 'MB':
            default:
                return $size; // Assume MB by default
        }
    }
}

function business_config($key, $settings_type)
{
    try {
        $config = Setting::where('name', $key)->where('page', $settings_type)->first();
    } catch (Exception $exception) {
        return null;
    }

    return (isset($config)) ? $config : null;
}

function email_template_file_create_update($validated)
{
    $mail_file = fopen(base_path("resources/views/emails/$validated[type].blade.php"), "w") or die("Unable to open file!");
    $txt = str_replace("{{url('/')}}", url('/'), $validated['body']);
    fwrite($mail_file, $txt);
    fclose($mail_file);
}

function device_notification($fcm_token, $title, $description, $id = null, $type = 'status', $image = null)
{
    FcmNotification::send($fcm_token, $title, $description, [
        "id" => $id,
        "type" => $type,
        "image" => $image,
    ]);
}


function account_verify_mail($user, $digits)
{
    $template = EmailTemplate::where("type", "account_verify_mail")->first();
    $code = str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);

    try {
        Mail::to($user->email)->send(new AccountVerifyMail($user, $code, $template));
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }

    VerificationCode::create([
        'user_id' => $user->id,
        'code' => $code,
        'expire_at' => Carbon::now()->addMinutes(config("app.email.account_verify.valid_code"))
    ]);

    return [
        'success' => true,
    ];
}

function on_boarding_mail($user)
{
    $template = EmailTemplate::where("type", "on_boarding_mail")->first();

    try {
        Mail::to($user->email)->send(new OnBoardingMail($user, $template));
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }

    return [
        'success' => true,
    ];
}

function order_place_mail($user, $order)
{
    $template = EmailTemplate::where("type", "order_place_mail")->first();

    try {
        Mail::to($user->email)->send(new OrderPlaceMail($user, $order, $template));
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }

    return [
        'success' => true,
    ];
}

function reseller_request_approved_mail($user)
{
    $template = EmailTemplate::where("type", "reseller_request_approved_mail")->first();

    try {
        Mail::to($user->email)->send(new ResellerRequestApprovedMail($user, $template));
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }

    return [
        'success' => true,
    ];
}

function transaction_create($ref_trx_id = null, $trx_type = null, $debit = null, $credit = null, $from_user_id = null, $to_user_id = null)
{
    if (is_null($to_user_id)) {
        $to_user_id = \App\Models\User::where("is_admin", "1")->first()->id;
    }

    \App\Models\Transaction::create([
        "ref_trx_id" => $ref_trx_id,
        "trx_type" => $trx_type,
        "debit" => $debit,
        "credit" => $credit,
        "from_user_id" => $from_user_id,
        "to_user_id" => $to_user_id,
    ]);
}

function check_bundle_is_expired()
{
    $orderItems = OrderItem::with(["order", "esim", "topup", "bundle"])
        ->whereHas("order", function ($query) {
            $query->where("status", 1);
        })
        ->whereHas("esim", function ($query) {
            $query->where("status", 1);
        })
        ->whereHas("topup", function ($query) {
            $query->where("status", 1)->latest();
        })
        ->whereHas("bundle", function ($query) {
            $query->where("status", 1)->latest();
        })
        ->where("user_id", auth()->user()->id)
        ->where("status", 1)
        ->latest()
        ->get();

    foreach ($orderItems as $order_item) {
        if ($order_item->module == "montyesim") {
            $montyESIM = new MontyeSim();
            $response = $montyESIM->bundle_consumption($order_item->esim->esim_order_id);

            if ($response['success']) {
                $order_item->topup->expired_at = date("Y-m-d H:i:s", strtotime($response['data']->profile_expiry_date));
                $order_item->topup->save();

//                    return response(['success' => true, 'result' => $order_item], 200);
            }

//                return response([
//                    'success' => false,
//                    'message' => $response['message']->detail,
//                ], 400);
        }
    }


    $resellerOrderItems = ResellerOrderItem::with(["order", "esim", "topup", "bundle"])
        ->whereHas("order", function ($query) {
            $query->where("status", 1);
        })
        ->whereHas("esim", function ($query) {
            $query->where("status", 1);
        })
        ->whereHas("topup", function ($query) {
            $query->where("status", 1)->latest();
        })
        ->whereHas("bundle", function ($query) {
            $query->where("status", 1)->latest();
        })
        ->where("user_id", auth()->user()->id)
        ->where("status", 1)
        ->latest()
        ->get();

    foreach ($resellerOrderItems as $resellerOrder_item) {
        if ($resellerOrder_item->module == "montyesim") {
            $montyESIM = new MontyeSim();
            $response = $montyESIM->bundle_consumption($resellerOrder_item->esim->esim_order_id);

            if ($response['success']) {
                $resellerOrder_item->topup->expired_at = date("Y-m-d H:i:s", strtotime($response['data']->profile_expiry_date));
                $resellerOrder_item->topup->save();

//                    return response(['success' => true, 'result' => $order_item], 200);
            }

//                return response([
//                    'success' => false,
//                    'message' => $response['message']->detail,
//                ], 400);
        }
    }
}