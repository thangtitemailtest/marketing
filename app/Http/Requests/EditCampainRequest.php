<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditCampainRequest extends FormRequest
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
            'name' => 'unique:campaign,name,'.$this->segment(2).',id',
            'startdate' => 'before:enddate',
            'enddate' => 'after:createdate',
        ];
    }
    public function messages()
    {
        return [
            'name.unique' => 'Tên giải đấu  đã tồn tại',
            'enddate.after' => 'Ngày kết thúc phải là  ngày sau khi tạo',
            'startdate.before' => 'Ngày bắt đầu phải là trước ngày sau khi kết thúc',
        ];
    }
}
