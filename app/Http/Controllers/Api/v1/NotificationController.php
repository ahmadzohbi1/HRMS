<?php


namespace App\Http\Controllers\Api\v1;


use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * @OA\Get (
     *     path="/notification/list",
     *     tags={"Notifications"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function index(Request $request)
    {
        $notifications = PushNotification::where("user_id", $request->user()->id)
            ->where("created_at", ">=", Carbon::now()->subDays(7))
            ->orderBy("created_at", "desc")
            ->get();


        return response(['success' => true, 'result' => $notifications], 200);
    }
}