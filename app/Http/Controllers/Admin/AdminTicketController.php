<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ActivationHistoryDataTable;
use App\DataTables\TicketDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminCustomerRequest;
use App\Http\Requests\Admin\AdminTicketRequest;
use App\Models\Customer;
use App\Models\Ticket;
use App\Support\Facades\Log;
use App\Support\Lang;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Enum\TicketPriority;
use App\Enum\TicketStatus;

class AdminTicketController extends Controller
{
    public function index(TicketDataTable $datatable)
    {
        return $datatable->render('admin.ticket.list');
    }

    public function show(Request $request, Ticket $customer)
    {
        return view('admin.ticket.detail', compact('customer'));
    }

    public function edit(Ticket $ticket)
    {
        $mode = 'edit';
        $priorities = array_column(TicketPriority::cases(), 'name', 'value');
        $status = array_column(TicketStatus::cases(), 'name', 'value');
        $customers = Customer::pluck('fullname', 'id');
        return view('admin.ticket.form', compact('priorities', 'mode', 'ticket', 'customers', 'status'));
    }

    public function create()
    {
        $mode = 'add';
        $priorities = array_column(TicketPriority::cases(), 'name', 'value');
        $customers = Customer::pluck('fullname', 'id');
        return view('admin.ticket.form', compact('priorities', 'mode', 'customers'));
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin:ticket.index')->with('success', 'Ticket deleted successfully.');
    }

    public function store(AdminTicketRequest $request)
    {

        //ticket number is T + primary key of last ticket + 1


        $ticketData = $request->validated();
        $ticketData['ticket_number'] = 'T' . (Ticket::max('id') + 1);
        $ticketData['status'] = TicketStatus::OPEN;

        Ticket::create($ticketData);
        return redirect()->route('admin:ticket.index')->with('success', 'Ticket submitted successfully.');
    }

    public function update(Ticket $ticket, AdminTicketRequest $request)
    {
        $ticket->update($request->validated());
        return redirect()->route('admin:ticket.index')->with('success', 'Ticket updated successfully.');
    }

    public function close(Ticket $ticket)
    {
        
    }
}
