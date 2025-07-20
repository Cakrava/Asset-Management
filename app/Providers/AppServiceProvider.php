<?php

namespace App\Providers;

use App\Models\Message; // Pastikan model Message di-import
use App\Models\Ticket;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth; // Import Auth Facade
use Illuminate\Support\Facades\View; // Import View Facade
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log; // Tambahkan di bagian atas
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    
     public function boot(): void
     {
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
        Carbon::setLocale(config('app.locale')); // agar Carbon ikut locale
    setlocale(LC_TIME, 'id_ID.UTF-8');       // agar fungsi date() & strftime() juga ikut
         View::composer(['layout.main', 'layout.sidebar'], function ($view) {
             $showUnreadMessageBadge = false;
             $ticketProcessIsExist = false;
             $ticketPendingIsExist = false;
             $ticketActive = false;
 
             if (Auth::check()) {
                 $user = Auth::user();
                 try {
                     if ($user->role === 'admin') {
                         $showUnreadMessageBadge = Message::whereNull('receiver_id')
                                                          ->where('is_read', 0)
                                                          ->exists();
                         $ticketProcessIsExist = Ticket::where('status', 'process')
                                                          ->exists();
                     } elseif ($user->role === 'user') {
                         $showUnreadMessageBadge = Message::where('receiver_id', $user->id)
                                                          ->where('is_read', 0)
                                                          ->exists();
                       
                                                          $ticketActive = Ticket::whereIn('status', ['pending', 'process']) // <-- PERBAIKAN DI SINI
                                                          ->where('user_id', $user->id)
                                                          ->exists();
                     } elseif ($user->role === 'master') {
                         // Untuk master, mungkin Anda ingin melihat pesan juga? Jika ya:
                         // $showUnreadMessageBadge = Message::whereNull('receiver_id') // Contoh jika master melihat pesan admin
                         //                                  ->where('is_read', 0)
                         //                                  ->exists();
                         $ticketPendingIsExist = Ticket::where('status', 'pending')
                                                     ->exists();
                     }
                 } catch (\Exception $e) {
                     Log::error('Error checking badges in AppServiceProvider: ' . $e->getMessage());
                     $showUnreadMessageBadge = false;
                     $ticketProcessIsExist = false;
                     $ticketActive = false;
                     $ticketPendingIsExist = false;
                 }
             }
 
             $view->with('ticketActive', $ticketActive);
             $view->with('showUnreadMessageBadge', $showUnreadMessageBadge);
             $view->with('ticketProcessIsExist', $ticketProcessIsExist);
             $view->with('ticketPendingIsExist', $ticketPendingIsExist);
         });
     }
 }
 