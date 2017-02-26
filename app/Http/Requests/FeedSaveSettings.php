<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class FeedSaveSettings extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            'pages' => 'required|array',
            'user' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'pages.required' => 'You need to select at least 1 page.',
            'pages.array' => 'Pages need to be passed in an array.',
        ];
    }
}
