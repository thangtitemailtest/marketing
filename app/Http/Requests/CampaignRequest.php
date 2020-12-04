<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampaignRequest extends FormRequest
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
                'name' => 'unique:campaign',
                'startdate' => 'before:enddate',
                'enddate' => 'after:createdate',
        ];
    }
    public function messages()
    {
        return [
            'name.unique' => 'Tên giải đấu bị trùng',
            'startdate.before' => 'Ngày bắt đầu phải là trươc ngày kết thúc',
            'enddate.after' => 'Ngày kết thuc phải là sau ngày tạo giải đấu',
        ];
    }
}
