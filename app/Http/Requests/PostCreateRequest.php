<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostCreateRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string', 'max:20000'],
            'image' => [
                'required',
                'max:500',
                Rule::dimensions()
                    ->minHeight(400)
                    ->minWidth(400)
                    ->maxWidth(1500)
                    ->maxHeight(1500)
                //     ->ratio(1)
            ]
        ];
    }
}
