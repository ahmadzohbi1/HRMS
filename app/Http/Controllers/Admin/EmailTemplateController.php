<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\EmailTemplateRequest;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use DataTables;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = EmailTemplate::query()->orderBy("id");
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $td = '<td>';
                    $td .= '<div class="d-flex">';
                    $td .= '<a href="' . route('templates.email.edit', $row->id) . '" type="button" class="btn btn-sm btn-info waves-effect waves-light me-1">' . __('buttons.edit') . '</a>';
                    $td .= '<a href="javascript:void(0)" data-id="' . $row->id . '" data-url="' . route('templates.email.destroy', $row->id) . '"  class="btn btn-sm btn-danger delete-btn">' . __('buttons.delete') . '</a>';
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

        return view('admin.templates.email.index');
    }

    public function create()
    {
        return view('admin.templates.email.create');
    }

    public function store(EmailTemplateRequest $request)
    {
        try {
            $validated = $request->validated();
            EmailTemplate::create($validated);

            email_template_file_create_update($validated);

            return redirect()->route('templates.email.index')->with([
                "message" => __('messages.success'),
                "icon" => "success",
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                "message" => $th->getMessage(),
                "icon" => "error",
            ]);
        }
    }

    public function show(EmailTemplate $email)
    {
        return view('admin.templates.email.show', compact("email"));
    }

    public function edit(EmailTemplate $email)
    {
        return view('admin.templates.email.edit', compact("email"));
    }

    public function update(EmailTemplateRequest $request, EmailTemplate $email)
    {
        try {
            $validated = $request->validated();
            $email->update($validated);

            email_template_file_create_update($validated);

            return redirect()->route('templates.email.index')->with([
                "message" => __('messages.update'),
                "icon" => "success",
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                "message" => $th->getMessage(),
                "icon" => "error",
            ]);
        }
    }

    public function destroy(EmailTemplate $email)
    {
        $email->delete();

        return redirect()->route('templates.email.index');
    }
}