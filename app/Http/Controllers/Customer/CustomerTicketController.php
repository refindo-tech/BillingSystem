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
use App\Jobs\SendWhatsAppMessageJob;
use App\Models\KeyWhatsapp;
use App\Models\WhatsappMessage;
use App\Models\WhatsAppTemplate;
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
        try {
            // Validasi input dari request
            $ticketData = $request->validated();

            // Generate nomor tiket berdasarkan ID terakhir + 1
            $ticketData['ticket_number'] = 'T' . (Ticket::max('id') + 1);
            $ticketData['status'] = TicketStatus::OPEN;
            $ticketData['customer_id'] = auth()->id();

            // Simpan data tiket ke database
            $newTicket = Ticket::create($ticketData);

            // Ambil data tiket yang baru dibuat, termasuk relasi dengan customer
            $ticket = Ticket::with('customer')->where('id', $newTicket->id)->first();

            // Buat pesan WhatsApp berdasarkan template
            $message = $this->generateTicketMessage($ticket);

            // Kirim pesan via WhatsApp jika nomor telepon tersedia
            if (!empty($ticket->customer->phonenumber) && $message) {
                $tokenDevice = KeyWhatsapp::first()->key_device;
                SendWhatsAppMessageJob::dispatch($ticket->customer->phonenumber, $message, $tokenDevice);

                // Simpan log pesan ke database
                WhatsappMessage::create([
                    'phone'   => $ticket->customer->phonenumber,
                    'message' => $message,
                    'date'    => now(),
                    'status'  => 'sent',
                ]);
            }

            return redirect()->route('customer:ticket.index')->with('success', 'Ticket submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function generateTicketMessage(Ticket $ticket)
    {
        // Ambil template pesan dari database berdasarkan tipe 'Tiket Baru'
        $template = WhatsAppTemplate::where('type', 'opentiket')->first();
        if (!$template) return null;

        // Data pengganti untuk template
        $replacements = [
            '#NOTIKET#'    => $ticket->ticket_number,
            '#STATUS#'     => $ticket->status,
            '#NAMAPELANGGAN#' => $ticket->customer->fullname,
            '#SUBJECT#'    => $ticket->subject,
            '#KELUHAN#'    => $ticket->message,
            '#PRIORITAS#'  => $ticket->priority,
        ];

        // Mengganti placeholder dengan nilai dari tiket
        return str_replace(array_keys($replacements), array_values($replacements), $template->message);
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
