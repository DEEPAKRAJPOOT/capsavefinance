<?php

namespace App\Http\Requests;

use Session;
use App\Http\Requests\Request;

class LoginRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        ///dd($this->request);
        return $rules = [
            'email' => 'required|email|max:50',
            'password' => 'required',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages() {
        return $messages = [
            'email.required' => 'Please enter your email address.',
            'password.required' => 'Please enter your password.'
        ];
    }

}
