<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
        return [
            "title" => 'required|string',
            "description" => 'required|string',
            'status' => 'required|string',
            'assigned_user_id' => 'required',
            'assigned_client_id' => 'required',
            'deadline' => 'nullable|date',
        ];
    }
}
