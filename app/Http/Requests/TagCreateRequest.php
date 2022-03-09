<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagCreateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:200'],
            'image' => [
                'required',
                'mimes:jpg,bmp,png',
                'max:500',
                Rule::dimensions()
                    ->minHeight(50)
                    ->minWidth(50)
                    ->maxWidth(400)
                    ->maxHeight(400)
            ]
        ];
    }
}
