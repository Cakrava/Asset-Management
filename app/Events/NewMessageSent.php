<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User; // Pastikan User diimport
use App\Models\Profile; // Import Profile jika belum
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

class NewMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Properti public akan otomatis disertakan dalam payload event
    public Message $message;
    public array $sender_data; // Data pengirim (termasuk profil)

    public function __construct(Message $message)
    {
        // --- MODIFIKASI DI SINI ---
        // Eager load relasi:
        // - sender (hanya ID)
        // - sender.profile (user_id, name, DAN image) <-- Tambahkan 'image'
        // - ticket (jika ada)
        $this->message = $message->load([
            'sender:id',
            'sender.profile:user_id,name,image', // <--- TAMBAHKAN ',image'
            'ticket'
        ]);
        // --- AKHIR MODIFIKASI ---

        // Siapkan data sender untuk payload (terutama untuk channel admin)
        // Pastikan sender ada sebelum mengakses relasi profile
        if ($this->message->sender) {
             // Konversi sender (yang sudah di-load profile-nya) ke array
            $this->sender_data = $this->message->sender->toArray();
            // Pastikan relasi profile benar-benar ada di hasil toArray
            // Jika tidak, mungkin perlu akses manual:
            // $this->sender_data = [
            //     'id' => $this->message->sender->id,
            //     'profile' => $this->message->sender->profile ? $this->message->sender->profile->toArray() : null
            //     // Tambahkan field user lain jika perlu
            // ];
        } else {
            $this->sender_data = []; // Kosongkan jika sender tidak ada (seharusnya tidak terjadi)
        }
    }

    // Method broadcastOn() TIDAK PERLU DIUBAH dari versi sebelumnya
    public function broadcastOn(): array
{
    $channels = [];
    $broadcastingToChannels = []; // Array untuk mengumpulkan nama channel yang akan di-log

    Log::info("[NewMessageSent broadcastOn] Menganalisis pesan ID: " . $this->message->id . ", Sender ID: " . ($this->message->sender_id ?? 'N/A') . ", Receiver ID: " . ($this->message->receiver_id ?? 'N/A') . ", Sender Role: " . ($this->message->sender->role ?? 'N/A'));

    // Skenario 1: Pesan dari User ke Admin Pool
    if (is_null($this->message->receiver_id) && $this->message->sender && $this->message->sender->role === 'user') {
        Log::info("[NewMessageSent broadcastOn] Skenario 1: User ke Admin Pool.");
        $channels[] = new PrivateChannel('admin-channel');
        $broadcastingToChannels[] = 'private-admin-channel';
        $channels[] = new PrivateChannel('conversation.' . $this->message->sender_id);
        $broadcastingToChannels[] = 'private-conversation.' . $this->message->sender_id;

    }
    // Skenario 2: Pesan dari Admin ke User Spesifik
    elseif (!is_null($this->message->receiver_id) && $this->message->sender && $this->message->sender->role === 'admin') {
        $receiverId = $this->message->receiver_id;
        Log::info("[NewMessageSent broadcastOn] Skenario 2: Admin ke User. Receiver ID: " . $receiverId);

        $channels[] = new PrivateChannel('user-channel.' . $receiverId);
        $broadcastingToChannels[] = 'private-user-channel.' . $receiverId;

        $channels[] = new PrivateChannel('conversation.' . $receiverId);
        $broadcastingToChannels[] = 'private-conversation.' . $receiverId;
    } else {
        Log::warning("[NewMessageSent broadcastOn] Tidak ada skenario broadcast yang cocok untuk pesan ID: " . $this->message->id);
    }

    if (!empty($broadcastingToChannels)) {
        Log::info("[NewMessageSent Event] Akan broadcast ke channels: " . implode(', ', $broadcastingToChannels) . " untuk message ID: " . $this->message->id);
    } else {
        Log::info("[NewMessageSent Event] Tidak ada channel yang ditentukan untuk broadcast pesan ID: " . $this->message->id);
    }
    return array_unique($channels);
}

    // Method broadcastAs() TIDAK PERLU DIUBAH
    public function broadcastAs(): string
    {
        return 'new-message';
    }

    // Method broadcastWith() BISA DIHAPUS
    // karena properti public $message dan $sender_data sudah otomatis dikirim.
    // Jika Anda ingin kontrol penuh payload, uncomment dan sesuaikan:
    // public function broadcastWith(): array
    // {
    //     return [
    //         'message' => $this->message->toArray(),
    //         'sender_data' => $this->sender_data, // Pastikan $sender_data berisi profile image
    //         'formatted_time' => $this->message->created_at->format('h:i A')
    //     ];
    // }
}