import Echo from 'laravel-echo'

import Pusher from 'pusher-js'
import axios from 'axios'
window.Pusher = Pusher
window.axios = axios

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo'
window.Echo = new Echo({
  broadcaster: 'pusher', // <-- Pastikan ini 'pusher'
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  // wsHost: import.meta.env.VITE_PUSHER_HOST, // Biasanya tidak perlu jika pakai cluster
  // wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
  // wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
  forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
  // encrypted: true, // Default true jika forceTLS true
  // enabledTransports: ['ws', 'wss'], // Defaultnya sudah ini
  // --- Konfigurasi Otorisasi ---
  authEndpoint: '/broadcasting/auth', // Endpoint default Laravel
  auth: {
    headers: {
      // Ambil CSRF token dari meta tag
      'X-CSRF-TOKEN':
        document
          .querySelector('meta[name="csrf-token"]')
          ?.getAttribute('content') || '',
    },
  },
})
