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
use App\Jobs\SendWhatsAppMessageJob;
use App\Models\KeyWhatsapp;
use App\Models\WhatsappMessage;
use App\Models\WhatsAppTemplate;

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

        try {

            $ticketData = $request->validated();
            $ticketData['ticket_number'] = 'T' . (Ticket::max('id') + 1);
            $ticketData['status'] = TicketStatus::OPEN;

            $newTicket = Ticket::create($ticketData);

            $ticket = Ticket::with('customer')->where('id', $newTicket->id)->first();

            // Buat pesan berdasarkan template
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

            return redirect()->route('admin:ticket.index')->with('success', 'Ticket submitted successfully.');
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

    private function generateDoneTicketMessage(Ticket $ticket)
    {
        // Ambil template pesan dari database berdasarkan tipe 'Tiket Baru'
        $template = WhatsAppTemplate::where('type', 'closedtiket')->first();
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


    public function update(AdminTicketRequest $request, Ticket $ticket)
    {
        try {
            // Update tiket dengan data yang divalidasi
            $ticket->update($request->validated());

            // Jika tiket ditutup, kirim notifikasi ke pelanggan
            if ($ticket->status === 'closed') {
                $message = $this->generateDoneTicketMessage($ticket);

                // Kirim pesan via WhatsApp jika nomor telepon tersedia
                $customerPhone = $ticket->customer->phonenumber;
                if (!empty($customerPhone) && $message) {
                    $tokenDevice = KeyWhatsapp::first()->key_device;
                    SendWhatsAppMessageJob::dispatch($customerPhone, $message, $tokenDevice);

                    // Simpan log pesan ke database
                    WhatsappMessage::create([
                        'phone'   => $customerPhone,
                        'message' => $message,
                        'date'    => now(),
                        'status'  => 'sent',
                    ]);
                }
            }

            return redirect()->route('admin:ticket.index')->with('success', 'Tiket berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function close(Ticket $ticket) {}
}
