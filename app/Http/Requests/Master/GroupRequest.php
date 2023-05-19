<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
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
            'group_name' => 'required|regex:/^[a-zA-Z0-9& ]*$/|min:3|max:100|unique:App\Inv\Repositories\Models\Master\NewGroup,group_name,'.$this->group_id.',group_id',
            'current_group_sanction' => 'nullable|regex:/^\d*(\.\d{1,2})?$/',
            'current_group_outstanding' => 'nullable|regex:/^\d*(\.\d{1,2})?$/',
            'group_field_1' => 'nullable|string|max:255',
            'group_field_2' => 'nullable|string|max:255',
            'group_field_3' => 'nullable|string|max:255',
            'group_field_4' => 'nullable|string|max:255',
            'group_field_5' => 'nullable|string|max:255',
            'group_field_6' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'group_name.regex' => 'Group name should only contain letters, numbers, space and special chars(&) allowed',
            'current_group_sanction.regex' => 'Current Group Sanction should be max two digits after point',
            'current_group_outstanding.regex' => 'Current Group Outstanding should be max two digits after point',
        ];
    }
}
