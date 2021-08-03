<?php


namespace App\Http\Controllers;


use App\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addTicket(Request $request) {

        // all ticket add fields
        $validator = Validator::make($request->all(),
            [
                'title' => 'required',
                'summary' => '',
                'image' => ''
            ]);

        if ($validator->fails()) {

            return response()->json(['error'=>$validator->errors()], 401);

        }

        $order = DB::table('tickets')
            ->select('order')
            ->where('tickets.state', '=', 0)
            ->orderBy('tickets.order','DESC')
            ->first();

        $ticket = new Ticket();
        $ticket->title = $request->title;
        $ticket->summary = $request->summary;
        $ticket->image = $request->image;
        $ticket->state = 0;
        $ticket->order = $order->order + 1;
        $ticket->save();


        return response()->json([
            'success' => true,
            'ticket' => $ticket,
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateTicket($ticketId, Request $request) {

        $whereClause = [
            ['tickets.id', '=', $ticketId],
        ];

        $validator = Validator::make($request->all(),
            [
                'state' => 'required',
            ]);

        if ($validator->fails()) {

            return response()->json(['error'=>$validator->errors()], 401);
        }

        $query = DB::table('tickets')
            ->where($whereClause);

        $updateValues = array(
            'state' => $request->state,
            'updated_at' => now()
        );

        $query->update($updateValues);
        $ticket = $query->first();


        return response()->json([
            'success' => true,
            'ticket' => $ticket,
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateOrder($ticketId, Request $request) {

        $whereClause = [
            ['tickets.id', '=', $ticketId],
        ];

        $validator = Validator::make($request->all(),
            [
                'order' => 'required',
            ]);

        if ($validator->fails()) {

            return response()->json(['error'=>$validator->errors()], 401);
        }

        $query = DB::table('tickets')
            ->where($whereClause);

        $updateValues = array(
            'order' => $request->order,
            'updated_at' => now()
        );

        $query->update($updateValues);
        $ticket = $query->first();


        return response()->json([
            'success' => true,
            'ticket' => $ticket,
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getTodo(Request $request) {

        $tickets = DB::table('tickets')
           // ->select('')
            ->where('tickets.state', '=', 0)
            ->orderBy('tickets.order','ASC')
            ->get();


        return response()->json([
            'success' => true,
            'tickets' => $tickets,
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getDone(Request $request) {

        $tickets = DB::table('tickets')
           // ->select('')
            ->where('tickets.state', '=', 1)
            ->orderBy('tickets.order','ASC')
            ->get();


        return response()->json([
            'success' => true,
            'tickets' => $tickets,
        ], Response::HTTP_OK);
    }

}
