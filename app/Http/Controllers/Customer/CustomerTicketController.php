<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Enum\TicketPriority;
use App\Enum\TicketStatus;
use App\Http\Requests\TicketRequest;

use App\DataTables\CustomerTicketDataTable;

use App\Support\Facades\Log;

class CustomerTicketController extends Controller
{
    /**
     * Display a listing of the customer's tickets.
     */
    public function index(CustomerTicketDataTable $datatable)
    {
        
        // $tickets = Ticket::where('customer_id', auth()->id())->latest()->get();
        // dd($tickets);
        // return view('customer.ticket.index', compact('tickets'));
        // Pass the TicketPriority enum cases to the view for the priority select.

        $mode = 'add';
        $priorities = array_column(TicketPriority::cases(), 'name', 'value');
        // return view('customer.ticket.form', compact('priorities', 'mode'));
        return $datatable->render('customer.ticket.form', compact('priorities', 'mode'));
        // return $datatable->render('customer.ticket.form', compact('priorities', 'mode'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        // Pass the TicketPriority enum cases to the view for the priority select.
        $priorities = TicketPriority::cases();
        return view('customer.ticket.form', compact('priorities'));
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(TicketRequest $request)
    {

        $ticketData = $request->validated();
        $ticketData['ticket_number'] = 'T' . (Ticket::max('id') + 1);
        $ticketData['status'] = TicketStatus::OPEN;
        $ticketData['customer_id'] = auth()->id();

        Ticket::create($ticketData);
        return redirect()->route('customer:ticket.index')->with('success', 'Ticket submitted successfully.');
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        // Make sure the ticket belongs to the authenticated customer.
        if ($ticket->customer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('customer.ticket.show', compact('ticket'));
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin:ticket.index')->with('success', 'Ticket deleted successfully.');
    }


}
