require('es6-promise').polyfill();
window.Popper = require('popper.js').default;
window.$ = window.jQuery = require('jquery');

import 'jquery-ui-bundle';

window.Vue = window.vue = require('vue');
require('bootstrap');
require('busy-load');
require('./shared-script.js')
window.Cropper = require('cropperjs');
window.Cropper = 'default' in window.Cropper ? window.Cropper['default'] : window.Cropper;
window.toastr = require('toastr');
window.DataTable = require('datatables.net');
require('datatables.net-bs4/js/dataTables.bootstrap4.js');
window.EasyMDE = require('easymde');
require('dropzone');
require('jquery-match-height');
require('bootstrap4-toggle');
require('nestable2');
require('select2');
var brace = require('brace');
require('brace/mode/json');
require('brace/theme/github');
require('./slugify');
window.TinyMCE = window.tinymce = require('tinymce');
require('./multilingual');
require('./voyager_tinymce');
window.voyagerTinyMCE = require('./voyager_tinymce_config');
require('./voyager_ace_editor');

import moment from 'moment';
Vue.prototype.moment = moment;
window.moment = moment;

require('tempusdominus-bootstrap-4');
window.helpers = require('./helpers.js');

window.toastr.options.closeDuration = 1000;

Vue.filter('usDate', function (value){
	try {
		return moment(value).format('MM/DD/YY hh:mm A');
	} catch (e) {
		return value
	}
})

Vue.filter('currency', function(value) {
    return '$' + formatMoney(value);
})

Vue.filter('decimal', function(value) {
    var x = parseFloat(value);

    if (! isNaN(x)) {
    	return x.toFixed(2)
    }

    return value
})

window.truncate = function (text, stop, clamp) {
	if (text !== null && text !== undefined) {
	    return text.slice(0, stop) + (stop < text.length ? clamp || '...' : '')
	}
	
	return '';
};

Vue.filter('ucfirst', function (s) {
  	if (typeof s !== 'string') return ''
  	return s.charAt(0).toUpperCase() + s.slice(1)
})

Vue.filter('phone', function(phoneNumberString) {
	try {
		var cleaned = ('' + phoneNumberString).replace(/\D/g, '')
		var match = cleaned.match(/^(1|)?(\d{3})(\d{3})(\d{4})$/)
		if (match) {
			var intlCode = (match[1] ? '+1 ' : '')
			return [intlCode, '(', match[2], ') ', match[3], '-', match[4]].join('')
		}
		return ''
	} catch (e) {
		return ''
	}
})

Vue.filter('truncate', truncate)
Vue.component('v-select', require('vue-select').default)
Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component('admin-menu', require('./components/admin_menu.vue').default);
Vue.component('product-options', require('./components/ProductOptions.vue').default);
Vue.component('products-table', require('./components/ProductsTable.vue').default);
Vue.component('product-categories', require('./components/ProductCategories.vue').default);
Vue.component('product-categories-modal', require('./components/ProductCategoriesModal.vue').default);
Vue.component('orders-table', require('./components/OrdersTable.vue').default);
Vue.component('order-details-component', require('./components/OrderDetailsComponent.vue').default);
Vue.component('order-tracking-table', require('./components/OrderTrackingTable.vue').default);
Vue.component('tracking-numbers-manager', require('./components/TrackingNumbersManager.vue').default);
Vue.component('analytics-box', require('./components/AnalyticsBox.vue').default);

var admin_menu = new Vue({
    el: '#adminmenu'
});
