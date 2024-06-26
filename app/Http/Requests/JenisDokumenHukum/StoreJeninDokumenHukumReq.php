<?php

namespace App\Http\Requests\JenisDokumenHukum;

use Illuminate\Foundation\Http\FormRequest;

class StoreJeninDokumenHukumReq extends FormRequest
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
            'nama' => ['required', 'unique:jenis_dokumen_hukum,nama']
        ];
    }
}
