<?php
use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use Illuminate\Support\Facades\Log;

// Channel untuk percakapan user
// Nama 'conversation.{targetUserId}' harus cocok dengan return di broadcastOn()
Broadcast::channel('conversation.{targetUserId}', function (User $user, int $targetUserId) {
    // $user = user yg sedang login
    // $targetUserId = ID dari nama channel

    // User pemilik channel boleh listen
    if ((int) $user->id === $targetUserId) {
        return true; // atau return $user;
    }

    // Admin boleh listen ke channel user manapun
    if ($user->role === 'admin') {
        return true; // atau return $user;
    }

    return false; // Tolak akses
});
Broadcast::channel('admin-channel', function ($user) {
    return $user->role === 'admin'; // Sesuaikan cek role admin
});
Broadcast::channel('user-channel.{id}', function (User $loggedInUser, int $id) { // Perhatikan parameter $id
    Log::info("[Channel Auth] Attempting to authorize 'user-channel.{$id}'. Logged in User ID: {$loggedInUser->id}, Target Channel ID: {$id}");
    // Otorisasi: User yang sedang login HARUS sama dengan ID di placeholder channel
    $isAuthorized = (int) $loggedInUser->id === $id;
    Log::info("[Channel Auth] Authorization for 'user-channel.{$id}': " . ($isAuthorized ? 'GRANTED' : 'DENIED'));
    return $isAuthorized;
});