import axios from 'axios';
import '@ruffle-rs/ruffle';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
