<?php

namespace App\Http\Requests;

class CustomAssetReportRequest extends Request
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
            'purchase_start'        => 'date|date_format:Y-m-d|nullable',
            'purchase_end'          => 'date|date_format:Y-m-d|nullable',
            'created_start'         => 'date|date_format:Y-m-d|nullable',
            'created_end'           => 'date|date_format:Y-m-d|nullable',
            'checkout_date_start'   => 'date|date_format:Y-m-d|nullable',
            'checkout_date_end'     => 'date|date_format:Y-m-d|nullable',
            'expected_checkin_start'      => 'date|date_format:Y-m-d|nullable',
            'expected_checkin_end'        => 'date|date_format:Y-m-d|nullable',
            'checkin_date_start'      => 'date|date_format:Y-m-d|nullable',
            'checkin_date_end'        => 'date|date_format:Y-m-d|nullable',
            'last_patch_start'      => 'date|date_format:Y-m-d|nullable',
            'last_patch_end'        => 'date|date_format:Y-m-d|nullable',
            'next_patch_start'      => 'date|date_format:Y-m-d|nullable',
            'next_patch_end'        => 'date|date_format:Y-m-d|nullable',
        ];
    }

    public function response(array $errors)
    {
        return $this->redirector->back()->withInput()->withErrors($errors, $this->errorBag);
    }
}
