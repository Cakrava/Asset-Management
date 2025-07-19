<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log; // Tambahkan di bagian atas


class MessageController extends Controller
{

    public function index()
    {
        $userId = Auth::id();

        // Ambil semua pesan dimana user adalah pengirim (ke admin, receiver_id null)
        // ATAU user adalah penerima (dari admin)
        $messages = Message::where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->whereNull('receiver_id');
            })
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver', 'ticket']) // Eager load relasi
            ->orderBy('created_at', 'asc')
            ->get();

        // Tandai pesan yang diterima user (dari admin) sebagai sudah dibaca
        // Hanya pesan yang receiver_id nya adalah user ini yang ditandai
        Message::where('receiver_id', $userId)
               ->where('is_read', 0)
               ->update(['is_read' => 1]);

        // Filter tiket hanya untuk status pending atau process
        $userTickets = Ticket::where('user_id', $userId)
                             ->whereIn('status', ['pending', 'process'])
                             ->get();

        return view('page.user.message', compact('messages', 'userTickets', 'userId'));
        // Pastikan path view 'user.chat' sesuai
    }
 public function markMessagesFromAdminAsRead(Request $request)
    {
        $loggedInUserId = Auth::id();

        if (!$loggedInUserId) {
            return response()->json(['error' => 'User tidak terautentikasi.'], 401);
        }

        try {
            // Cari semua ID admin
            $adminIds = User::where('role', 'admin')->pluck('id');

            if ($adminIds->isEmpty()) {
                Log::info("[UserMarkAsRead] Tidak ada admin ditemukan, tidak ada pesan untuk ditandai. User ID: {$loggedInUserId}");
                return response()->json(['success' => true, 'message' => 'Tidak ada admin, tidak ada pesan untuk ditandai.', 'updated_count' => 0]);
            }

            // Update pesan dari admin ke user ini yang belum dibaca
            $updatedCount = Message::where('receiver_id', $loggedInUserId) // Pesan DITUJUKAN ke user ini
                ->whereIn('sender_id', $adminIds)      // Pengirimnya adalah salah satu admin
                ->where('is_read', 0)                 // Yang belum dibaca
                ->update(['is_read' => 1]);

            Log::info("[UserMarkAsRead] Pesan dari admin ke User ID: {$loggedInUserId} ditandai sebagai dibaca. Jumlah diupdate: {$updatedCount}");

            return response()->json([
                'success' => true,
                'message' => "Pesan dari admin telah ditandai sebagai dibaca.",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            Log::error("[UserMarkAsRead] Error saat menandai pesan sebagai dibaca: " . $e->getMessage(), [
                'user_id' => $loggedInUserId
            ]);
            return response()->json(['error' => 'Gagal menandai pesan sebagai dibaca.'], 500);
        }
    }

    /**
     * Menyimpan pesan baru dari user ke admin.
     */
    public function userMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'ticket_id' => 'nullable|integer|exists:tickets,id', // Validasi jika tiket ada
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => null, // Pesan dari user ke admin pool
            'ticket_id' => $request->input('ticket_id'),
            'message' => $request->input('message'),
            'is_read' => 0, // Default belum dibaca admin
        ]);
       

        if ($message) { // Pastikan create berhasil
            event(new \App\Events\NewMessageSent($message));
       }

        
if ($request->ajax()) {
         // Reload message DENGAN relasi yang sama seperti di event
         $message->load(['sender:id','sender.profile:user_id,name', 'ticket']);
         return response()->json([
             'success' => true,
             'message' => $message, // Kirim data yang sudah di load
             'formatted_time' => $message->created_at->format('h:i A')
         ]);
     }
     return back()->with('success', 'Pesan terkirim!');
}
    // ============================================
    //         FUNGSI UNTUK SISI ADMIN
    // ============================================
    public function adminIndex()
    {
        $adminIds = User::where('role', 'admin')->pluck('id');

        // Ambil ID user yang pernah berinteraksi
        $userIdsSentToAdmin = Message::whereNull('receiver_id')->distinct()->pluck('sender_id');
        $userIdsReceivedFromAdmin = Message::whereIn('sender_id', $adminIds)->whereNotNull('receiver_id')->distinct()->pluck('receiver_id');
        $relevantUserIds = $userIdsSentToAdmin->merge($userIdsReceivedFromAdmin)
                                              ->unique()
                                              ->reject(fn ($id) => $adminIds->contains($id)); // Gunakan arrow function (jika PHP >= 7.4)

        // Ambil data user relevan DENGAN jumlah unread DAN gambar profile
// Controller adminIndex
$users = User::query()
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->whereIn('users.id', $relevantUserIds)
    ->withCount('unreadMessagesToAdmin')
    ->orderBy('profiles.name', 'asc')
    ->selectRaw('users.id, profiles.name, profiles.image') // Coba selectRaw
    ->get();
            

        $adminUserId = Auth::id();

        // Variabel $users sekarang berisi id, name, image, dan unread_messages_to_admin_count
        return view('page.admin.message-admin', compact('users', 'adminUserId')); // Ganti 'page.message' jika perlu
    }
     /**
      * Mengambil riwayat percakapan untuk user tertentu (via AJAX).
      */
     public function showConversation($userId)
     {
         // Validasi apakah $userId adalah user valid (bukan admin)
         // Cek juga apakah user punya profile jika diperlukan
         $targetUser = User::where('id', $userId)
                           ->where('role', '!=', 'admin')
                           // ->whereHas('profile') // Opsional: pastikan user punya profile
                           ->first();
 
         if (!$targetUser) {
             return response()->json(['error' => 'User tidak ditemukan atau tidak valid.'], 404);
         }
 
         $adminUserId = Auth::id();
         $adminIds = User::where('role', 'admin')->pluck('id');
 
         
         Message::where('sender_id', $userId) // Pesan dari user yang sedang dilihat
         ->whereNull('receiver_id')      // Yang ditujukan ke admin pool
         ->where('is_read', 0)          // Yang belum dibaca
         ->update(['is_read' => 1]);  
         // Ambil pesan:
         $messages = Message::where(function ($query) use ($userId) {
                 $query->where('sender_id', $userId)
                       ->whereNull('receiver_id');
             })
             ->orWhere(function ($query) use ($userId, $adminIds) {
                 $query->where('receiver_id', $userId)
                       ->whereIn('sender_id', $adminIds);
             })
             // Eager load relasi DENGAN NESTED profile untuk sender dan receiver
             ->with([
                 'sender:id', // Ambil ID sender dari tabel users
                 'sender.profile:user_id,name', // Ambil profile.name terkait sender
                 'receiver:id', // Ambil ID receiver dari tabel users (jika ada)
                 'receiver.profile:user_id,name', // Ambil profile.name terkait receiver
                 'ticket' // Ambil data ticket terkait
             ])
             ->orderBy('created_at', 'asc')
             ->get();
 

 
         // Ambil tiket milik user yang dipilih
         $userTickets = Ticket::where('user_id', $userId)
         ->whereIn('status', ['process', 'pending'])
         ->get();
     
 
         return response()->json([
             // Kirim messages asli (JS akan handle nested profile) atau kirim $formattedMessages
             'messages' => $messages,
             'userTickets' => $userTickets,
             'selectedUserId' => $userId,
             'adminUserId' => $adminUserId,
         ]);
     }
     public function markMessagesAsRead(Request $request)
     {
         // Validasi input
         $validated = $request->validate([
             'user_id' => 'required|integer|exists:users,id',
             // Anda bisa menambahkan validasi message_id jika ingin menandai pesan spesifik,
             // tapi untuk kasus ini, menandai semua dari user_id ke admin pool lebih sederhana.
         ]);
 
         $userIdToMark = $validated['user_id'];
         $adminId = Auth::id(); // Admin yang sedang login
 
         try {
             // Cek apakah user yang pesannya akan ditandai bukan admin
             $targetUser = User::find($userIdToMark);
             if (!$targetUser || $targetUser->role === 'admin') {
                 Log::warning("[MarkAsRead] Percobaan menandai pesan dari admin atau user tidak valid. User ID: {$userIdToMark}, Admin ID: {$adminId}");
                 return response()->json(['error' => 'User tidak valid.'], 400);
             }
 
             // Update pesan dari user_id yang belum dibaca dan ditujukan ke admin pool
             $updatedCount = Message::where('sender_id', $userIdToMark)
                 ->whereNull('receiver_id') // Ke admin pool
                 ->where('is_read', 0)      // Yang belum dibaca
                 ->update(['is_read' => 1]);
 
             Log::info("[MarkAsRead] Pesan dari User ID: {$userIdToMark} ditandai sebagai dibaca oleh Admin ID: {$adminId}. Jumlah diupdate: {$updatedCount}");
 
             return response()->json([
                 'success' => true,
                 'message' => "Pesan dari user {$userIdToMark} telah ditandai sebagai dibaca.",
                 'updated_count' => $updatedCount
             ]);
 
         } catch (\Exception $e) {
             Log::error("[MarkAsRead] Error saat menandai pesan sebagai dibaca: " . $e->getMessage(), [
                 'user_id' => $userIdToMark,
                 'admin_id' => $adminId
             ]);
             return response()->json(['error' => 'Gagal menandai pesan sebagai dibaca.'], 500);
         }
     }
  
     
     /**
      * Mengirim pesan dari admin ke user (via AJAX).
      */
      public function sendMessage(Request $request)
      {
          Log::info('Mengirim pesan dimulai.', $request->all());
      
          $request->validate([
              'message' => 'required|string',
              'receiver_id' => 'required|integer|exists:users,id',
              'ticket_id' => 'nullable|integer|exists:tickets,id',
          ]);
      
          $adminId = Auth::id();
          Log::debug('ID Admin:', ['admin_id' => $adminId]);
      
          $receiverId = $request->input('receiver_id');
          $receiver = User::find($receiverId);
          Log::debug('Receiver ditemukan:', ['receiver' => $receiver]);
      
          if (!$receiver || $receiver->role === 'admin') {
              Log::warning('Penerima tidak valid.', ['receiver_id' => $receiverId]);
              return response()->json(['success' => false, 'error' => 'Penerima tidak valid.'], 400);
          }
      
          if ($request->filled('ticket_id')) {
              $ticket = Ticket::find($request->input('ticket_id'));
              if (!$ticket || $ticket->user_id != $receiverId) {
                  Log::warning('Tiket tidak valid atau tidak milik user.', [
                      'ticket_id' => $request->input('ticket_id'),
                      'receiver_id' => $receiverId,
                  ]);
                  return response()->json(['success' => false, 'error' => 'Tiket tidak valid untuk pengguna ini.'], 400);
              }
              Log::debug('Tiket valid:', ['ticket' => $ticket]);
          }
      
          $message = Message::create([
              'sender_id' => $adminId,
              'receiver_id' => $receiverId,
              'ticket_id' => $request->input('ticket_id'),
              'message' => $request->input('message'),
              'is_read' => 0,
          ]);
      
          if (!$message) {
              Log::error('Gagal membuat pesan.');
              return response()->json(['success' => false, 'error' => 'Gagal menyimpan pesan.'], 500);
          }
      
          Log::info('Pesan berhasil dibuat.', ['message_id' => $message->id]);
      
          
    event(new \App\Events\NewMessageSent($message));
          $message->load([
              'sender:id',
              'sender.profile:user_id,name',
              'ticket'
          ]);
      
          return response()->json([
              'success' => true,
              'message' => $message,
              'formatted_time' => $message->created_at->format('h:i A')
          ]);
      }
      public function deleteChat()
      {
       $userId = auth()->id();

       // Hapus semua pesan yang melibatkan user sebagai pengirim atau penerima
       $deleted = \App\Models\Message::where('sender_id', $userId)
           ->orWhere('receiver_id', $userId)
           ->delete();

       return response()->json([
           'success' => true,
           'deleted_count' => $deleted,
           'message' => 'Semua percakapan berhasil dihapus.'
       ]);
      
       }
    }
