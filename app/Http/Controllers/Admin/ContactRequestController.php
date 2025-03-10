<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContactRequestController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::orderBy('created_at', 'desc')->get();

    // Reset unread status
    Contact::where('status', 'unread')->update(['status' => 'read']);
        if ($request->ajax()) {
            $data = Contact::query()->latest();

            return Datatables::of($data)->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $td = '<td>';
                    $td .= '<div class="d-flex">';
//                    $td .= '<a href="' . route('regions.edit', $row->id) . '" type="button" class="btn btn-sm btn-info waves-effect waves-light me-1">' . __('buttons.edit') . '</a>';
//                    $td .= '<a href="javascript:void(0)" data-id="' . $row->id . '" data-url="' . route('regions.destroy', $row->id) . '"  class="btn btn-sm btn-danger delete-btn">' . __('buttons.delete') . '</a>';
                    $td .= "</div>";
                    $td .= "</td>";
                    return $td;
                })
                ->editColumn('created_at', function ($row) {
                    return formatDate($row->created_at);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.contact-requests.index',compact('contacts'));
    }
    public function getUnreadCount()
{
    $unreadCount = Contact::where('status', 'unread')->count();
    return response()->json(['unread_count' => $unreadCount]);
}

}