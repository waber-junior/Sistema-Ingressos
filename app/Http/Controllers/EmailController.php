<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    public function sendEmailTo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'subject' => 'required',
            'to' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(), 
                'message' => 'Desculpe, não foi possível enviar mensagem'
            ], 400);
        }

        try {
            Mail::to($request->to)->send(new SendMail($request->message, $request->subject, $request->to));
            return response()->json(['message' => 'Email enviado com sucesso']);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }
}
