<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function index (Request $request)
    {
        $perPage = $request->has('perPage') ? $request->perPage : 50;
        $page = $request->has('page') ? $request->page : 1;

        $tickets = Ticket::with('attachments');

        if ($request->has('subject')) {
            $tickets->where('subject', 'like', '%'. $request->subject .'%');
        }

        return response()->json(
            $tickets->paginate($perPage, '*', null, $page)
        );
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $user = auth()->guard('api')->user();
        $validator = Validator::make($data, [
            'description' => 'required|min:3',
            'subject' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível gravar ticket.'
            ], 400);
        }

        $attachments = isset($data['attachment']) ? $data['attachment'] : null;
        unset($data['attachment']);

        try {
            $ticket = Ticket::create(array_merge($data,['user_id' => $user->id]));
            if ($attachments) {
                $path = $request->file('attachment')->store('public/tickets/'.$ticket->id);
                $ticketAttachment = TicketAttachment::create([
                    'file' => $path,
                    'ticket_id' => $ticket->id
                ]);
            }
            return response()->json($ticket);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    public function update($ticketId, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'description' => 'required|min:3',
            'subject' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível gravar ticket.'
            ], 400);
        }

        $attachments = isset($data['attachment']) ? $data['attachment'] : null;
        unset($data['attachment']);
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $ticket->update($data);
            if ($attachments) {
                $path = $request->file('attachment')->store('public/tickets/'.$ticket->id);
                $ticketAttachment = TicketAttachment::create([
                    'file' => $path,
                    'ticket_id' => $ticket->id
                ]);
            }
            return response()->json($ticket);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $attachments = $ticket->attachemnts->delete();
            $ticket->delete();
            return response()->json(['message' => 'Deletado com sucesso']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
