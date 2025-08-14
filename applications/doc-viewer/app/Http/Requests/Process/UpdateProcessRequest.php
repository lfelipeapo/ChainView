<?php

namespace App\Http\Requests\Process;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcessRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'required',
                'string',
                'max:1000'
            ],
            'type' => [
                'required',
                'string',
                'in:internal,external'
            ],
            'criticality' => [
                'required',
                'string',
                'in:low,medium,high'
            ],
            'status' => [
                'required',
                'string',
                'in:active,inactive'
            ],
            'tools' => [
                'nullable',
                'string',
                'max:500'
            ],
            'responsible' => [
                'nullable',
                'string',
                'max:255'
            ],
            'documentation' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do processo é obrigatório.',
            'name.string' => 'O nome do processo deve ser um texto.',
            'name.max' => 'O nome do processo não pode ter mais de 255 caracteres.',
            
            'description.required' => 'A descrição do processo é obrigatória.',
            'description.string' => 'A descrição do processo deve ser um texto.',
            'description.max' => 'A descrição do processo não pode ter mais de 1000 caracteres.',
            
            'type.required' => 'O tipo do processo é obrigatório.',
            'type.in' => 'O tipo do processo deve ser interno ou externo.',
            
            'criticality.required' => 'A criticidade do processo é obrigatória.',
            'criticality.in' => 'A criticidade deve ser baixa, média ou alta.',
            
            'status.required' => 'O status do processo é obrigatório.',
            'status.in' => 'O status deve ser ativo ou inativo.',
            
            'tools.string' => 'As ferramentas devem ser um texto.',
            'tools.max' => 'As ferramentas não podem ter mais de 500 caracteres.',
            
            'responsible.string' => 'O responsável deve ser um texto.',
            'responsible.max' => 'O responsável não pode ter mais de 255 caracteres.',
            
            'documentation.string' => 'A documentação deve ser um texto.',
            'documentation.max' => 'A documentação não pode ter mais de 1000 caracteres.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome do processo',
            'description' => 'descrição do processo',
            'type' => 'tipo do processo',
            'criticality' => 'criticidade',
            'status' => 'status',
            'tools' => 'ferramentas',
            'responsible' => 'responsável',
            'documentation' => 'documentação'
        ];
    }
}
