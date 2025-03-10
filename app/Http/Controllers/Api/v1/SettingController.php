<?php


namespace App\Http\Controllers\Api\v1;


use App\Http\Controllers\Controller;
use App\Models\AiraloBundle;
use App\Models\Country;
use App\Models\MontyesimBundle;
use App\Models\Providers\Airalo;
use App\Models\Providers\MontyeSim;
use App\Models\Region;
use App\Models\Setting;
use Illuminate\Http\Request;
use function GuzzleHttp\json_encode;

class SettingController extends Controller
{
    protected MontyeSim $montyESIM;

    public function __construct()
    {
        $this->montyESIM = new MontyeSim();
    }

//    /**
//     * @OA\Get (
//     *     path="/settings/general",
//     *     tags={"Settings"},
//     *     @OA\Response(
//     *         response="200",
//     *         description="Successful operation",
//     *         @OA\JsonContent()
//     *     )
//     * )
//     */
    public function general()
    {
        $lists = [];

        if ($lists) {
            return response([
                'success' => true,
                'data' => $lists
            ], 200);
        }

        return response([
            'success' => false,
            'message' => 'No result found...',
        ], 400);
    }

//    /**
//     * @OA\Get (
//     *     path="/settings/financial",
//     *     tags={"Settings"},
//     *     @OA\Response(
//     *         response="200",
//     *         description="Successful operation",
//     *         @OA\JsonContent()
//     *     )
//     * )
//     */
    public function financial()
    {
        $financial = Setting::financial_basic();

        if ($financial) {
            return response([
                'success' => true,
                'data' => $financial
            ], 200);
        }

        return response([
            'success' => false,
            'message' => 'No result found...',
        ], 400);
    }

//    /**
//     * @OA\Get (
//     *     path="/settings/support",
//     *     tags={"Settings"},
//     *     @OA\Response(
//     *         response="200",
//     *         description="Successful operation",
//     *         @OA\JsonContent()
//     *     )
//     * )
//     */
    public function support()
    {
        $support = Setting::support_basic();

        if ($support) {
            return response([
                'success' => true,
                'data' => $support
            ], 200);
        }

        return response([
            'success' => false,
            'message' => 'No result found...',
        ], 400);
    }


    /**
     * @OA\Get (
     *     path="/cron-job/country/{provider}/list",
     *     tags={"CronJob"},
     *     @OA\Parameter(
     *         name="provider",
     *         example="montyesim",
     *         required=true,
     *         in="path",
     *      ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function country_list($provider)
    {

        switch ($provider) {
            case 'montyesim':
                $countries = $this->montyESIM->countries();

                if ($countries['success']) {
                    foreach ($countries["data"]->countries as $country) {

                        if (Country::where("iso2_code", $country->iso2_code)->exists()) {
                            Country::where("iso2_code", $country->iso2_code)
                                ->update([
                                    "name" => $country->country_name,
                                    "iso2_code" => $country->iso2_code,
                                    "iso3_code" => $country->iso3_code,
                                ]);
                        } else {
                            Country::create([
                                "name" => $country->country_name,
                                "iso2_code" => $country->iso2_code,
                                "iso3_code" => $country->iso3_code,
                                "flag" => "https://flagcdn.com/" . strtolower($country->iso2_code) . ".svg",
                            ]);
                        }
                    }
                }

                return $countries;
                break;
        }
    }

    /**
     * @OA\Get (
     *     path="/cron-job/region/{provider}/list",
     *     tags={"CronJob"},
     *     @OA\Parameter(
     *         name="provider",
     *         example="montyesim",
     *         required=true,
     *         in="path",
     *      ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function region_list($provider)
    {
        switch ($provider) {
            case 'montyesim':
                $regions = $this->montyESIM->regions();

                if ($regions['success']) {
                    foreach ($regions["data"]->regions as $region) {

                        if (Region::where("region_code", $region->region_code)->exists()) {
                            Region::where("region_code", $region->region_code)
                                ->update([
                                    "name" => $region->region_name,
                                    "region_code" => $region->region_code,
                                ]);
                        } else {
                            Region::create([
                                "name" => $region->region_name,
                                "region_code" => $region->region_code,
                            ]);
                        }
                    }
                }

                return $regions;
                break;
        }
    }


    /**
     * @OA\Get (
     *     path="/cron-job/bundle/{provider}/list",
     *     tags={"CronJob"},
     *     @OA\Parameter(
     *         name="provider",
     *         example="montyesim",
     *         required=true,
     *         in="path",
     *      ),
     *     @OA\Parameter(
     *         name="page_number",
     *         example="1",
     *         in="query",
     *      ),
     *     @OA\Parameter(
     *         name="page_size",
     *         example="100",
     *         in="query",
     *      ),
     *     @OA\Parameter(
     *         name="reseller_admin_view",
     *         example="true",
     *         in="query",
     *      ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function bundle_list(Request $request, $provider)
    {
        switch ($provider) {
            case 'montyesim':
                $bundles = $this->montyESIM->bundles($request->all());

                if ($bundles['success']) {
                    foreach ($bundles["data"]->bundles as $bundle) {

                        if (MontyesimBundle::withTrashed()->where("bundle_code", $bundle->bundle_code)->exists()) {
                            MontyesimBundle::withTrashed()->where("bundle_code", $bundle->bundle_code)
                                ->update([
                                    "data_unit" => $bundle->data_unit,
                                    "gprs_limit" => $bundle->gprs_limit,
                                    "subscriber_price" => $bundle->subscriber_price,
                                    "country_code" => json_encode($bundle->country_code),
                                    "country_name" => json_encode($bundle->country_name),
                                    "validity" => $bundle->validity,
                                    "status" => 1,
                                ]);

                        } else {
                            MontyesimBundle::create([
                                "bundle_category" => $bundle->bundle_category,
                                "bundle_code" => $bundle->bundle_code,
                                "bundle_marketing_name" => $bundle->bundle_marketing_name,
                                "bundle_name" => $bundle->bundle_name,
                                "reseller_bundle_name" => $bundle->bundle_name,
                                "country_code" => json_encode($bundle->country_code),
                                "country_name" => json_encode($bundle->country_name),
                                "currency_code_list" => json_encode($bundle->currency_code_list),
                                "data_unit" => $bundle->data_unit,
                                "gprs_limit" => $bundle->gprs_limit,
                                "region_code" => $bundle->region_code,
                                "region_name" => $bundle->region_name,
                                "subscriber_price" => $bundle->subscriber_price,
                                "reseller_retail_price" => $bundle->reseller_retail_price,
                                "reseller_price" => $bundle->reseller_retail_price,
                                "validity" => $bundle->validity,
                                "status" => 1,
                            ]);
                        }
                    }
                }

                return $bundles;
                break;

            case "airalo":
                $airalo = new Airalo();

                $bundles = $airalo->getAllPackages([
                    "type" => "",
                    "country" => "",
                    "limit" => 10000,
                ]);

                if ($bundles["success"]) {
                    foreach ($bundles["data"]->data as $bundle) {

                        foreach ($bundle->operators as $operator) {
                            foreach ($operator->packages as $package) {

                                $countries = [];
                                foreach ($operator->countries as $country) {
                                    $countries[] = $country->country_code;
                                }

                                $airalo_bundle = AiraloBundle::withTrashed()->where("package_id", $package->id)->first();

                                if ($airalo_bundle) {
                                    $airalo_bundle->bundle_category = $bundle->country_code ? "country" : "region";
                                    $airalo_bundle->region_code = $bundle->country_code ? null : $bundle->slug;
                                    $airalo_bundle->title = $operator->title;
                                    $airalo_bundle->esim_type = $operator->esim_type;
                                    $airalo_bundle->info = $operator->info ? json_encode($operator->info) : null;
                                    $airalo_bundle->plan_type = $operator->plan_type;
                                    $airalo_bundle->is_kyc_verify = $operator->is_kyc_verify;
                                    $airalo_bundle->other_info = $operator->other_info;
                                    $airalo_bundle->coverages = json_encode($operator->coverages);
                                    $airalo_bundle->countries = json_encode($countries);
                                    $airalo_bundle->type = $package->type;
                                    $airalo_bundle->price = $package->price;
                                    $airalo_bundle->amount = $package->amount;
                                    $airalo_bundle->validity = $package->day;
                                    $airalo_bundle->is_unlimited = $package->is_unlimited;
                                    $airalo_bundle->package_title = $package->title;
                                    $airalo_bundle->data = $package->data;
                                    $airalo_bundle->short_info = $package->short_info;
                                    $airalo_bundle->qr_installation = $package->qr_installation;
                                    $airalo_bundle->manual_installation = $package->manual_installation;
                                    $airalo_bundle->voice = $package->voice;
                                    $airalo_bundle->text = $package->text;
                                    $airalo_bundle->net_price = $package->net_price;
                                    $airalo_bundle->save();
                                } else {
                                    AiraloBundle::create([
                                        "bundle_category" => $bundle->country_code ? "country" : "region",
                                        "region_code" => $bundle->country_code ? null : $bundle->slug,
                                        "title" => $operator->title,
                                        "esim_type" => $operator->esim_type,
                                        "info" => $operator->info ? json_encode($operator->info) : null,
                                        "plan_type" => $operator->plan_type,
                                        "is_kyc_verify" => $operator->is_kyc_verify,
                                        "other_info" => $operator->other_info,
                                        "coverages" => json_encode($operator->coverages),
                                        "countries" => json_encode($countries),
                                        "package_id" => $package->id,
                                        "type" => $package->type,
                                        "price" => $package->price,
                                        "amount" => $package->amount,
                                        "validity" => $package->day,
                                        "is_unlimited" => $package->is_unlimited,
                                        "package_title" => $package->title,
                                        "data" => $package->data,
                                        "short_info" => $package->short_info,
                                        "qr_installation" => $package->qr_installation,
                                        "manual_installation" => $package->manual_installation,
                                        "voice" => $package->voice,
                                        "text" => $package->text,
                                        "net_price" => $package->net_price,
                                        "status" => 1,
                                    ]);
                                }
                            }
                        }
                    }
                }


                return $bundles;
                break;
        }
    }


    /**
     * @OA\Get (
     *     path="/cron-job/bundle/region/{provider}/list",
     *     tags={"CronJob"},
     *     @OA\Parameter(
     *         name="provider",
     *         example="montyesim",
     *         required=true,
     *         in="path",
     *      ),
     *     @OA\Parameter(
     *         name="page_number",
     *         example="1",
     *         in="query",
     *      ),
     *     @OA\Parameter(
     *         name="page_size",
     *         example="100",
     *         in="query",
     *      ),
     *     @OA\Parameter(
     *         name="reseller_admin_view",
     *         example="true",
     *         in="query",
     *      ),
     *     @OA\Parameter(
     *         name="region_code",
     *         example="as",
     *         in="query",
     *      ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function region_bundle_list(Request $request, $provider)
    {
        switch ($provider) {
            case 'montyesim':
                $bundles = $this->montyESIM->bundles($request->all());

//                $bundle_code = [];
                if ($bundles['success']) {
                    foreach ($bundles["data"]->bundles as $bundle) {

                        if (MontyesimBundle::withTrashed()->where("bundle_code", $bundle->bundle_code)->exists()) {

                            MontyesimBundle::withTrashed()->where("bundle_code", $bundle->bundle_code)
                                ->update([
                                    "region_code" => $request->input("region_code"),
                                    "data_unit" => $bundle->data_unit,
                                    "gprs_limit" => $bundle->gprs_limit,
                                    "subscriber_price" => $bundle->subscriber_price,
                                    "country_code" => json_encode($bundle->country_code),
                                    "country_name" => json_encode($bundle->country_name),
                                    "validity" => $bundle->validity,
                                    "status" => 1,
                                ]);

                        } else {
                            MontyesimBundle::create([
                                "bundle_category" => $bundle->bundle_category,
                                "bundle_code" => $bundle->bundle_code,
                                "bundle_marketing_name" => $bundle->bundle_marketing_name,
                                "bundle_name" => $bundle->bundle_name,
                                "reseller_bundle_name" => $bundle->bundle_name,
                                "country_code" => json_encode($bundle->country_code),
                                "country_name" => json_encode($bundle->country_name),
                                "currency_code_list" => json_encode($bundle->currency_code_list),
                                "data_unit" => $bundle->data_unit,
                                "gprs_limit" => $bundle->gprs_limit,
                                "region_code" => $request->input("region_code"),
                                "region_name" => $bundle->region_name,
                                "subscriber_price" => $bundle->subscriber_price,
                                "reseller_retail_price" => $bundle->reseller_retail_price,
                                "reseller_price" => $bundle->reseller_retail_price,
                                "validity" => $bundle->validity,
                                "status" => 1,
                            ]);
                        }

//                        array_push($bundle_code, $bundle->bundle_code);
                    }
                }

//                Region::where("region_code", $request->input("region_code"))->update([
//                    "bundle_code" => json_encode($bundle_code)
//                ]);

                return $bundles;
                break;
        }
    }
}
