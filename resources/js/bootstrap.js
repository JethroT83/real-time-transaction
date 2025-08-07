import axios from 'axios';
import Echo from 'laravel-echo';
import './bootstrap';
import Pusher from 'pusher-js';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: 8080,
    wssPort: 8080,
    cluster: 'mt1',
    forceTLS: false,
    encrypted: false,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});

