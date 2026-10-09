require('./bootstrap');

window.Vue = require('vue'); 

import LaravelVuePagination from 'laravel-vue-pagination';
export default {
    components: {
        'Pagination': LaravelVuePagination
    },
    
}