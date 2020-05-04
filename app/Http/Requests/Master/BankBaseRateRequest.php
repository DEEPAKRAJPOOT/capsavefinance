<?php

namespace App\Http\Requests\Master;

use Session;
use Carbon\Carbon;
use App\Http\Requests\Request;

class BankBaseRateRequest extends Request {

    public function authorize() {
        return true;
    }

    public function rules() {
//         dd($this->request);
        $rules = [
            'bank_id' => 'required|numeric',
            'base_rate' => 'required|numeric|between:0,99.99|regex:/^\d+(\.\d{1,2})?$/',
            'start_date' => 'required',
            'is_active' => 'required',
        ];

        if ($this->request->get('end_date') != null) {
            $startdate = Carbon::createFromFormat('d/m/Y', $this->request->get('start_date'))->format('Y-m-d');
            $enddate = Carbon::createFromFormat('d/m/Y', $this->request->get('end_date'))->format('Y-m-d');
            if ($startdate > $enddate) {
                $rules = [
                    'end_date' => 'after:start_date'
                ];
            }
        }

        return $rules;
    }

    public function messages() {
        return $messages = [
        ];
    }

}
