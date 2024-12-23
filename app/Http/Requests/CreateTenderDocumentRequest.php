<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTenderDocumentRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'title' => [
        'required',
        'string',
        'max:255',
        'unique:tender_documents,title'
      ],
      'description' => [
        'nullable',
        'string',
        'max:5000'
      ],
      'client_name' => [
        'required',
        'string',
        'max:255'
      ],
      'submission_deadline' => [
        'required',
        'date',
        'after:today'
      ],
      'requirements' => [
        'nullable',
        'array'
      ],
      'requirements.*' => [
        'string',
        'max:1000'
      ]
    ];
  }

  public function messages()
  {
    return [
      'title.unique' => 'A tender document with this title already exists.',
      'submission_deadline.after' => 'The submission deadline must be a future date.',
      'requirements.*.max' => 'Each requirement must not exceed 1000 characters.'
    ];
  }
}
