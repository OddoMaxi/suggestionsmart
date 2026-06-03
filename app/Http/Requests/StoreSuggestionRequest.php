<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'max:100'],
            'prenom'      => ['required', 'string', 'max:100'],
            'telephone'   => ['required', 'string', 'max:30', 'regex:/^[0-9\+\s\-\(\)]{6,30}$/'],
            'email'       => ['nullable', 'email', 'max:150'],
            'service_id'  => ['required', 'uuid', 'exists:services,id'],
            'type'        => ['required', 'in:suggestion,critique,reclamation,felicitation'],
            'message'     => ['required', 'string', 'min:10', 'max:3000'],
            'satisfaction'=> ['nullable', 'integer', 'min:1', 'max:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'        => 'Votre nom est obligatoire.',
            'prenom.required'     => 'Votre prénom est obligatoire.',
            'telephone.required'  => 'Votre numéro de téléphone est obligatoire.',
            'telephone.regex'     => 'Le numéro de téléphone est invalide.',
            'email.email'         => 'L\'adresse email est invalide.',
            'service_id.required' => 'Veuillez sélectionner un service.',
            'service_id.exists'   => 'Le service sélectionné est invalide.',
            'type.required'       => 'Veuillez choisir le type de message.',
            'type.in'             => 'Le type de message est invalide.',
            'message.required'    => 'Votre message est obligatoire.',
            'message.min'         => 'Votre message doit contenir au moins 10 caractères.',
            'message.max'         => 'Votre message ne peut pas dépasser 3000 caractères.',
            'satisfaction.min'    => 'La note doit être entre 1 et 5.',
            'satisfaction.max'    => 'La note doit être entre 1 et 5.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nom'     => strip_tags(trim($this->nom ?? '')),
            'prenom'  => strip_tags(trim($this->prenom ?? '')),
            'message' => strip_tags(trim($this->message ?? '')),
        ]);
    }
}
