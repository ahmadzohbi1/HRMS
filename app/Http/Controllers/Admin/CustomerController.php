<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::query()
                ->withTrashed()
                ->customer();
            return DataTables::of($data)->addIndexColumn()
                ->setRowClass(fn($row) => 'align-middle')
                ->addColumn('action', function ($row) {
                    $td = '<td>';
                    $td .= '<div class="d-flex">';
                    $td .= '<a href="' . route('customers.show', $row->id) . '" type="button" class="btn btn-sm  btn-primary waves-effect waves-light me-1">' . __('buttons.view') . '</a>';
                    $td .= "</div>";
                    $td .= "</td>";
                    return $td;
                })
                ->addColumn('status', function ($row) {
                    $status = $row->deleted_at ? 'checked' : '';
                    $val = $row->deleted_at ? 1 : 0;

                    $td = '<div class="form-check form-switch">';
                    $td .= '<input class="form-check-input status" data-id="' . $row->id . '" type="checkbox" name="status" value="' . $val . '" role="switch" id="switch-' . $row->id . '" ' . $status . '>';
                    $td .= '<label class="form-check-label" for="switch-' . $row->id . '"></label>';
                    $td .= '</div>';
                    return $td;
                })
                ->editColumn('email_verified_at', fn($row) => $row->email_verified_at ? formatDate($row->email_verified_at) : "")
                ->editColumn('created_at', fn($row) => formatDate($row->created_at))
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('admin.customers.index');
    }

    public function show(Request $request, User $user)
    {
        $otp_list = VerificationCode::where("user_id", $user->id)->latest()->get();
        $notification_list = PushNotification::where("user_id", $user->id)->latest()->get();

        return view('admin.customers.show', compact('user', 'otp_list', 'notification_list'));
    }

    public function change_status(Request $request)
    {
        $user = User::withTrashed()->where("id", $request->input("id"))->customer()->first();
        if ($user) {
            $user->deleted_at = $request->input("status") == "1" ? date("Y-m-d H:i:s") : null;
            $user->save();
        }

        return response()->json([
                "status" => true
            ]
        );
    }

    public function user_hotel(Request $request, User $user)
    {
        $user->load('hotels');

        return DataTables::of($user->hotels)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $td = '<td>';
                $td .= '<div class="d-flex">';
                $td .= '<a href="' . route('hotel-booking') . '" type="button" class="btn btn-sm btn-info waves-effect waves-light me-1">' . __('buttons.view') . '</a>';
                $td .= "</div>";
                $td .= "</td>";
                return $td;
            })
            ->editColumn('user', function ($row) {
                return $row->user->name;
            })
            ->editColumn('check_in_out', function ($row) {
                $td = '<td>';

                $td .= '<div class="d-flex justify-content-between align-items-center">';
                $td .= '<div class="departure">';
                $td .= '<div><small><strong>CHECK-IN</strong></small></div>';
                $td .= '<div><small>' . formatDate($row->pricingInfo->check_in_date) . '</small></div>';
                $td .= '</div>';

                $td .= '<div>-- <i class="fa fa-clock"></i> --</div>';

                $td .= '<div class="arrival">';
                $td .= '<div><small><strong>CHECK-OUT</strong></small></div>';
                $td .= '<div><small>' . formatDate($row->pricingInfo->check_out_date) . '</small></div>';
                $td .= '</div>';

                $td .= '</div>';

                $td .= "</td>";
                return $td;
            })
            ->editColumn('currency_price', function ($row) {
                $td = '<td>';
                $td .= $row->pricingInfo->currency . " " . $row->pricingInfo->total;
                $td .= "</td>";
                return $td;
            })
            ->editColumn('guests', function ($row) {
                return $row->pricingInfo->guests;
            })
            ->editColumn('cancellation_deadline', function ($row) {
                $td = '<td>';
                $td .= date("M d, Y h:i a", strtotime($row->pricingInfo->cancellation_deadline));
                $td .= "</td>";
                return $td;
            })
            ->rawColumns(['action', 'check_in_out', 'currency_price', 'cancellation_deadline'])
            ->make(true);
    }

    public function user_tickets(Request $request, User $user)
    {
        $user->load('tickets');

        return Datatables::of($user->tickets)->addIndexColumn()
            ->setRowClass(fn($row) => 'align-middle')
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-sm btn-danger waves-effect waves-light me-1 cancel-btn" data-bs-toggle="modal" data-bs-target="#chanegStatusModal" data-url="' . route("tickets.changeStatus", $row->id) . '">Cancel</button>';
            })
            ->editColumn('user', function ($row) {
                $td = '<td>';
                $td .= $row->user->name;
                $td .= "</td>";
                return $td;
            })
            ->editColumn('orderId', function ($row) {
                $td = '<td>';
                $td .= '<a href="' . route("tickets.show", ["id" => $row->id]) . '" target="_blank">';
                $td .= $row->orderId;
                $td .= "</a>";
                $td .= "</td>";
                return $td;
            })
            ->editColumn('route', function ($row) {
                $td = '<td>';

                foreach ($row->itinerary as $key => $itinerary) {
                    $td .= '<div class="d-flex justify-content-between align-items-center">';
                    $td .= '<div class="departure">';
                    $td .= isset($itinerary->departure->name) ? $itinerary->departure->name : $itinerary->departure_iataCode;
                    $td .= '<div><small><strong>' . $itinerary->airline->name . ' (' . $itinerary->carrierCode . ' ' . $itinerary->number . ')</strong></small></div>';
                    $td .= '<div><small>' . formatDateWithTimezone($itinerary->departure_at) . '</small></div>';
                    $td .= '</div>';

                    $td .= '<div>-- <i class="fa fa-plane"></i> --</div>';

                    $td .= '<div class="arrival text-right">';
                    $td .= isset($itinerary->arrival->name) ? $itinerary->arrival->name : $itinerary->arrival_iataCode;
                    $td .= '<div><small><strong>' . $itinerary->airline->name . ' (' . $itinerary->carrierCode . ' ' . $itinerary->number . ')</strong></small></div>';
                    $td .= '<div><small>' . formatDateWithTimezone($itinerary->arrival_at) . '</small></div>';
                    $td .= '</div>';

                    $td .= '</div>';
                }

                $td .= "</td>";
                return $td;
            })
            ->editColumn('currency_price', function ($row) {
                $td = '<td>';
                $td .= "$row->currency $row->total_price";
                $td .= "</td>";
                return $td;
            })
            ->editColumn("status", fn($row) => '<span class="badge badge-pill badge-soft-' . getStatusColor($row->status) . ' font-size-14 p-2">' . ucfirst($row->status) . '</span>')
            ->rawColumns(['user', 'orderId', 'route', 'currency_price', 'action', 'status'])
            ->make(true);
    }
}
