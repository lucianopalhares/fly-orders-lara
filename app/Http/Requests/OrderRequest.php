<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class OrderRequest extends FormRequest
{
    /**
     * Valida os dados de entrada para a criação de um pedido.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'requester_name' => 'required|string',
            'destination_name' => 'required|string',
            'departure_date' => 'required|date',
            'return_date' => 'nullable|date',
            'status' => 'required|in:requested',
        ], [
            'user_id.required' => 'O ID do usuário é obrigatório.',
            'user_id.exists' => 'O usuário fornecido não existe.',
            'requester_name.required' => 'O nome do solicitante é obrigatório.',
            'requester_name.string' => 'O nome do solicitante deve ser um texto válido.',
            'destination_name.required' => 'O nome do destino é obrigatório.',
            'destination_name.string' => 'O nome do destino deve ser um texto válido.',
            'departure_date.required' => 'A data de partida é obrigatória.',
            'departure_date.date' => 'A data de partida deve estar em um formato válido.',
            'return_date.date' => 'A data de retorno deve estar em um formato válido.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser "requested".',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
