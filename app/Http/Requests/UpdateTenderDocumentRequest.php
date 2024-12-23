<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenderDocumentRequest extends FormRequest
{
  public function authorize()
  {
    // Ensure the user can update this specific tender document
    return $this->user()->can('edit', $this->route('tenderDocument'));
  }

  public function rules()
  {
    $tenderDocument = $this->route('tenderDocument');

    return [
      'title' => [
        'sometimes',
        'required',
        'string',
        'max:255',
        // Ensure uniqueness, except for the current document's title
        Rule::unique('tender_documents', 'title')->ignore($tenderDocument->id)
      ],
      'description' => [
        'nullable',
        'string',
        'max:5000'
      ],
      'client_name' => [
        'sometimes',
        'required',
        'string',
        'max:255'
      ],
      'submission_deadline' => [
        'sometimes',
        'required',
        'date',
        function ($attribute, $value, $fail) {
          // Ensure deadline is in the future
          if (Carbon::parse($value)->isPast()) {
            $fail('The submission deadline must be a future date.');
          }
        }
      ],
      'requirements' => [
        'nullable',
        'array'
      ],
      'requirements.*' => [
        'string',
        'max:1000'
      ],
      'status' => [
        'sometimes',
        'in:draft,published,closed'
      ],
      'reanalyze' => [
        'sometimes',
        'boolean'
      ]
    ];
  }

  public function messages()
  {
    return [
      'title.unique' => 'A tender document with this title already exists.',
      'title.max' => 'The title must not exceed 255 characters.',
      'description.max' => 'The description must not exceed 5000 characters.',
      'client_name.max' => 'The client name must not exceed 255 characters.',
      'requirements.*.max' => 'Each requirement must not exceed 1000 characters.',
      'status.in' => 'Invalid status. Must be one of: draft, published, closed.'
    ];
  }

  public function validated($key = null, $default = null)
  {
    $validatedData = parent::validated($key, $default);

    // Remove reanalyze from validated data as it's not a model attribute
    unset($validatedData['reanalyze']);

    return $validatedData;
  }

  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      // Additional custom validation logic
      $this->validateRequirements($validator);
    });
  }

  protected function validateRequirements($validator)
  {
    $requirements = $this->input('requirements', []);

    // Optional: Add more complex requirement validation
    if (!empty($requirements)) {
      if (count($requirements) > 20) {
        $validator->errors()->add(
          'requirements',
          'You cannot specify more than 20 requirements.'
        );
      }

      // Ensure no duplicate requirements
      $uniqueRequirements = array_unique($requirements);
      if (count($uniqueRequirements) !== count($requirements)) {
        $validator->errors()->add(
          'requirements',
          'Duplicate requirements are not allowed.'
        );
      }
    }
  }
}
