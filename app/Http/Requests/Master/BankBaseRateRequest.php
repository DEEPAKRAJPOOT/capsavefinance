<?php

namespace App\Http\Requests\Master;

use Session;
use App\Http\Requests\Request;

class BankBaseRateRequest extends Request {

    public function authorize() {
        return true;
    }

    public function rules() {
//         dd($this->request);
        return $rules = [
            'bank_id' => 'required|numeric',
            'base_rate' => 'required|numeric|between:0,99.99|regex:/^\d+(\.\d{1,2})?$/',
            'is_active' => 'required',
        ];
    }

    public function messages() {
        return $messages = [
            
        ];
    }

}
