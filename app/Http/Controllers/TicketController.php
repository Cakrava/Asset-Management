<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Letters;
use App\Models\StoredDevice;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Log; // Tambahkan di bagian atas


class TicketController extends Controller
{
  
    public function masterTicketIndex()
    {
        $usersForList = User::whereHas('tickets', function ($query) {
                                // Filter tiket yang dimiliki user
                                // agar hanya user dengan tiket 'pending' atau 'process' yang muncul
                                $query->whereIn('status', ['pending']);
                                // Anda bisa juga menggunakan ->where('status', 'pending')->orWhere('status', 'process');
                            })
                            ->with('profile')     // Eager load profil
                            ->select('users.*')   // Pilih semua kolom dari 'users' untuk menghindari ambiguitas
                            ->join('profiles', 'users.id', '=', 'profiles.user_id') // Join untuk sorting nama profil
                            ->orderBy('profiles.name', 'asc') // Urutkan berdasarkan nama di tabel profiles
                            // ->where('users.role', '!=', 'admin') // Opsional: filter user berdasarkan role
                            ->get();

        return view('page.master.ticket-master', compact('usersForList')); // Pastikan nama view benar

    }

// Controller Anda sudah OK
public function accept($id)
{
    $ticket = Ticket::findOrFail($id);
    $subject = $ticket->subject;
    
    $ticket->status = 'process'; // Atau 'processed' atau status lain yang Anda gunakan
    $ticket->save();

    session()->flash('success', 'Ticket succeffuly accepted');
    $history = new History();
   
    $history->user_id = auth()->id();
    $history->history_data = 'Ticket dengan ID ' . $id . ' dan subject "' . $subject . '" berhasil di-accept';
    $history->save();
    return response()->json(['message' => 'Ticket accepted successfully']);
}

public function reject($id)
{
    $ticket = Ticket::findOrFail($id);
    $subject = $ticket->subject;
    $ticket->status = 'rejected';
    $ticket->save();

    session()->flash('success', 'Ticket succeffuly rejected');
    $history = new History();
    $history->user_id = auth()->id();
    $history->history_data = 'Ticket dengan ID ' . $id . ' dan subject "' . $subject . '" berhasil di-reject';
    $history->save();
    return response()->json(['message' => 'Ticket rejected successfully']);
}
    public function adminTicketIndex()
    {
        $inventories = StoredDevice::with('device')
                            ->whereIn('condition', ['baru', 'bekas'])
                            ->get();

        $usersForList = User::whereHas('tickets', function ($query) {
                                // Filter tiket yang dimiliki user
                                // agar hanya user dengan tiket 'pending' atau 'process' yang muncul
                                $query->whereIn('status', ['process', 'pending']);
                                // Anda bisa juga menggunakan ->where('status', 'pending')->orWhere('status', 'process');
                            })
                            ->with('profile')     // Eager load profil
                            ->select('users.*')   // Pilih semua kolom dari 'users' untuk menghindari ambiguitas
                            ->join('profiles', 'users.id', '=', 'profiles.user_id') // Join untuk sorting nama profil
                            ->orderBy('profiles.name', 'asc') // Urutkan berdasarkan nama di tabel profiles
                            // ->where('users.role', '!=', 'admin') // Opsional: filter user berdasarkan role
                            ->get();

        return view('page.admin.ticket-admin', compact('usersForList','inventories')); // Pastikan nama view benar
    }

    public function showUserTickets(User $user) // Menggunakan Route Model Binding
    {
        Log::info('Mengambil tiket untuk user dengan ID: ' . $user->id);

        $role = session()->get('role');
        $allowed_statuses = [];

    if ($role === 'admin') {
        $allowed_statuses = ['pending', 'process'];
    } elseif ($role === 'master') {
        $allowed_statuses = ['pending'];
    }
        $tickets = Ticket::where('user_id', $user->id)
                        ->whereIn('status', $allowed_statuses) // Ambil tiket berdasarkan status yang sesuai dengan role
                        ->orderBy('created_at', 'desc')
                        ->get();

        Log::info('Tiket untuk user dengan ID: ' . $user->id . ' berhasil diambil. Total tiket: ' . $tickets->count());

        return response()->json($tickets);
    }


    public function userTicket(){
        $tickets = Ticket::where('user_id', auth()->id())->get();
        $countPending = Ticket::where(['user_id' => auth()->id(), 'status' => 'pending'])->count();
        $countProcess = Ticket::where(['user_id' => auth()->id(), 'status' => 'process'])->count();
        return view('page.user.ticket-user', compact('tickets', 'countPending', 'countProcess'));
    }

    public function store(Request $request)
    {
        // 1. CEK TIKET AKTIF (logika yang sudah ada)
        // Hitung total tiket dengan status 'pending' ATAU 'process' milik user yang login
        $activeTicketCount = Ticket::where('user_id', auth()->id())
                                   ->whereIn('status', ['pending', 'process'])
                                   ->count();
    
        // Cek jika jumlah tiket aktif (pending/process) sudah 1 atau lebih
        if ($activeTicketCount >= 1) {
            return redirect()->route('panel.ticket.user-ticket')
                           ->with('error', 'Anda sudah memiliki 1 tiket yang berstatus pending atau sedang diproses. Silakan tunggu hingga tiket selesai sebelum membuat tiket baru.');
        }
    
        // 2. LOGIKA BARU: Cek model Letters
        // Cek apakah user memiliki surat dengan status 'open'.
        // Asumsi: kolom di tabel 'letters' yang menunjuk ke user adalah 'client_id'.
        $openLetterExists = Letters::where('client_id', auth()->id())
                                  ->where('status', 'open')
                                  ->exists(); // 'exists()' lebih efisien daripada 'count()' jika hanya butuh tahu ada/tidaknya data
    
        // Jika ditemukan ada surat dengan status 'open', tolak request.
        if ($openLetterExists) {
            return redirect()->route('panel.ticket.user-ticket')
                           ->with('error', 'Tiket Anda telah memasuki tahap Penyuratan dengan status open. Silakan ajukan tiket baru setelah surat dinyatakan closed');
        }
    
        // 3. JIKA SEMUA PENGECEKAN LOLOS, LANJUTKAN PROSES (logika yang sudah ada)
        // Validasi input
        $validatedData = $request->validate([
            'subject' => 'required|max:255',
            'ticket_type' => 'required',
            'description' => 'required',
        ]);
    
        // Buat tiket baru
        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'ticket_type' => $validatedData['ticket_type'],
            'subject' => $validatedData['subject'],
            'notes' => $validatedData['description'],
            'status' => 'pending',
        ]);
    
        // Simpan history setiap kali tiket berhasil dibuat
        History::create([
            'user_id' => auth()->id(),
            'history_data' => 'Membuat tiket baru dengan subjek: ' . $validatedData['subject'],
        ]);
    
        // Redirect dengan pesan sukses
        return redirect()->route('panel.ticket.user-ticket')
                       ->with('success', 'Tiket berhasil dibuat. Kami akan segera menghubungi Anda.');
    }
    public function cancel(Request $request): RedirectResponse
    {
        $request->validate(['ticket_id' => 'required|integer|exists:tickets,id']);
        $ticketId = $request->input('ticket_id');
        

        try {
            $ticket = Ticket::findOrFail($ticketId);

            if ($ticket->status === 'pending' && $ticket->request_to_cancel === 0) {
                $ticket->status = 'canceled';
                $subject = $ticket->subject;
                $ticket->save();
                $history = new History();
                $history->user_id = auth()->id();
                $history->history_data = 'Tiket dengan ID ' . $ticketId . ' dan subject "' . $subject . '" berhasil dibatalkan.';
                $history->save();
                Log::info('Tiket dengan ID: ' . $ticketId . ' berhasil dibatalkan.');
                return redirect()->back()->with('success', 'Tiket berhasil dibatalkan.');
            } else if  ($ticket->status === 'process' && $ticket->request_to_cancel === 0) {
                $ticket->request_to_cancel = 1;
                $subject = $ticket->subject;
                $ticket->save();
                $history = new History();
                $history->user_id = auth()->id();
                $history->history_data = 'Permintaan pembatalan untuk tiket dengan ID: ' . $ticketId . ' dan subject "' . $subject . '" telah dikirim.';
                $history->save();
                Log::info('Permintaan pembatalan untuk tiket dengan ID: ' . $ticketId . ' telah dikirim.');
                return redirect()->back()->with('success', 'Permintaan pembatalan tiket telah dikirim.');
            }
            else if ($ticket->status === 'process' && $ticket->request_to_cancel === 1) {
                $ticket->request_to_cancel = 0;
                $subject = $ticket->subject;
                $ticket->status = 'canceled';
                $ticket->save();
                $history = new History();
                $history->user_id = auth()->id();
                $history->history_data = 'Permintaan pembatalan untuk tiket dengan ID: ' . $ticketId . ' dan subject "' . $subject . '" diterima.';
                $history->save();
                Log::info('Permintaan pembatalan untuk tiket dengan ID: ' . $ticketId . ' diterima.');
                return redirect()->back()->with('success', 'Permintaan pembatalan diterima.');
            }
             else {
                Log::warning('Tiket dengan ID: ' . $ticketId . ' tidak dapat dibatalkan karena statusnya bukan pending.');
                return redirect()->back()->with('warning', 'Tiket tidak dapat dibatalkan (status bukan pending).');
            }

        } catch (\Exception $e) {
            Log::error('Gagal membatalkan tiket dengan ID: ' . $ticketId . '. Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membatalkan tiket.');
        }
    }
}
