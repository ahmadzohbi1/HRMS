<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Models\PushNotification;
use App\Models\VerificationCode;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Log;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */

    // protected $redirectTo = RouteServiceProvider::HOME;

    public function redirectTo()
    {
        if (Auth::user()->is_admin) {
            return route('root');
        } else {
            $token = base64_encode(Auth::user()->email);
            Auth::logout();
            return route('auth.email-verify', ['token' => $token]);
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_code' => ['required'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'country_id' => ['required'],
            //            'address' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Create the user
        $user = User::create([
            'is_admin' => 0,
            'name' => $data['name'],
            'username' => $data['email'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
           
            
            "phone" => $data['phone'],
            "address" => null,
            "currency" => "USD",
            "wallet" => 0,
        ]);

        // Send account verification email with OTP
        account_verify_mail($user, business_config('otp_digit', 'business_information')->value ?? 4);
        return $user;
    }


    public function email_verify(Request $request)
    {
        $token = $request->input("token");

        return view('auth.otp-verify', compact('token'));
    }

    // public function verify_otp(Request $request)
    // {
    //     $request->validate([
    //         "token" => "required",
    //         "otp" => "required"
    //     ]);

    //     $email = base64_decode($request->input("token"));
    //     $user = User::where("email", $email)->first();

    //     if ($user) {
    //         $verificationCode = VerificationCode::where('user_id', $user->id)
    //             ->latest()
    //             ->first();

    //         if ($verificationCode && $verificationCode->code == $request->input("otp")) {
    //             $user->email_verified_at = Carbon::now();
    //             $user->save();

    //             on_boarding_mail($user);

    //             $template = NotificationTemplate::where("type", "on_boarding_notification")->first();
    //             if ($template) {
    //                 PushNotification::create([
    //                     "user_id" => $user->id,
    //                     "service_id" => $user->id,
    //                     "title" => $template->title,
    //                     "body" => $template->body,
    //                     "type" => $template->type,
    //                 ]);
    //             }

    //             Auth::login($user);

    //             return redirect()->route('front.bundles');
    //         }

    //         Session::flash('error', 'Invalid OTP!');
    //         return redirect()->back();
    //     }

    //     Session::flash('error', 'Invalid request!');
    //     return redirect()->back();
    // }
}
