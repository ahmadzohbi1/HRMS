<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class NotificationTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        if (request()->isMethod("POST")) {
            $checkUnique = "unique:notification_templates,type";
        } elseif (request()->isMethod("PUT") || request()->isMethod("PATCH")) {
            $checkUnique = 'unique:notification_templates,type,' . $this->notification->id;
        }

        return [
            "title" => ['required'],
            "type" => ['required', $checkUnique],
            "body" => ['required'],
        ];
    }
}