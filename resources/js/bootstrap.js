import axios from 'axios';
import  '@ruffle-rs/ruffle';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.RufflePlayer = window.RufflePlayer || {};
window.RufflePlayer.config = {
    publicPath: "/build/ruffle/",
    backgroundColor: "#282a36",
    quality: "high",
    autoplay: "on",
    unmuteOverlay: "hidden",
    splashScreen: false,
    showSwfDownload: true
};

import('@ruffle-rs/ruffle');