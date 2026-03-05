import '../css/app.css';
import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add any custom JavaScript here
console.log('Sriwijaya Kids App Loaded');
