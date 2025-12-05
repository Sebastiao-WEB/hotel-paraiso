<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FormRequest para validação de check-in direto (walk-in)
 */
class StoreWalkInCheckinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'guest_id' => [
                'required',
                'exists:clientes,id',
            ],
            'room_id' => [
                'required',
                'exists:quartos,id',
            ],
            'check_in_at' => [
                'nullable',
                'date',
                'before_or_equal:now',
            ],
            'expected_check_out_at' => [
                'required',
                'date',
                'after:check_in_at',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'guest_id.required' => 'É necessário selecionar um cliente.',
            'guest_id.exists' => 'O cliente selecionado não existe.',
            'room_id.required' => 'É necessário selecionar um quarto.',
            'room_id.exists' => 'O quarto selecionado não existe.',
            'expected_check_out_at.required' => 'É necessário informar a data prevista de saída.',
            'expected_check_out_at.after' => 'A data de saída deve ser posterior à data de entrada.',
            'check_in_at.before_or_equal' => 'A data de entrada não pode ser futura.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Se check_in_at não foi informado, usa o momento atual
        if (!$this->has('check_in_at')) {
            $this->merge([
                'check_in_at' => now()->toDateTimeString(),
            ]);
        }
    }
}

