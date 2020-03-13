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
            'min_base_rate' => 'required|numeric|between:0,99.99|regex:/^\d+(\.\d{1,2})?$/|max:' . $this->request->get('max_base_rate'),
            'max_base_rate' => 'required|numeric|between:0,99.99|regex:/^\d+(\.\d{1,2})?$/|max:' . $this->request->get('base_rate') . '|min:' . $this->request->get('min_base_rate'),
            'is_active' => 'required',
        ];
    }

    public function messages() {
        return $messages = [
            
        ];
    }

}
