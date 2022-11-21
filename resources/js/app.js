
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('bootstrap');
require('busy-load');

window.Vue = require('vue');
window.axios = require('axios');
window.$ = require('jquery');
window._ = require('lodash');
let numeral = require('numeral');
let noUiSlider = require('nouislider');
let cookie = require('jquery.cookie');
import 'owl.carousel';

import vSelect from 'vue-select'

require('history.js/history.adapter.ender.js');
require('history.js/history.js');

import route from 'ziggy'
import { Ziggy } from './routes'

window.rateYo = require('rateyo');
window.route = route;
window.Ziggy = Ziggy;

require('./shared');



// Basic JS Helpers

function getUrlVars() {// from https://html-online.com/articles/get-url-parameters-javascript/
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

function getUrlParam(parameter, defaultvalue){// from https://html-online.com/articles/get-url-parameters-javascript/
    var urlparameter = defaultvalue;
    if(window.location.href.indexOf(parameter) > -1){
        urlparameter = getUrlVars()[parameter];
        }
    return urlparameter;
}

function slugify(string) {
    const a = 'àáâäæãåāăąçćčđďèéêëēėęěğǵḧîïíīįìłḿñńǹňôöòóœøōõőṕŕřßśšşșťțûüùúūǘůűųẃẍÿýžźż·/_,:;'
    const b = 'aaaaaaaaaacccddeeeeeeeegghiiiiiilmnnnnoooooooooprrsssssttuuuuuuuuuwxyyzzz------'
    const p = new RegExp(a.split('').join('|'), 'g')
  
    return string.toString().toLowerCase()
      .replace(/\s+/g, '-') // Replace spaces with -
      .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
      .replace(/&/g, '-and-') // Replace & with 'and'
      .replace(/[^\w\-]+/g, '') // Remove all non-word characters
      .replace(/\-\-+/g, '-') // Replace multiple - with single -
      .replace(/^-+/, '') // Trim - from start of text
      .replace(/-+$/, '') // Trim - from end of text
}
// END Basic JS Helpers

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Define some filters that can be used in vue component rendering like this {{ value | filter_you_define_below }}

Vue.filter('currency', function (value) {
	return numeral(value).format('$0,0.00')
})

Vue.filter('truncate', function (value, limit) {
    if (value.length > limit) {
        value = value.substring(0, (limit - 3)) + '...';
    }

    return value
});

Vue.filter('slugify', function (value) {
    return window.slugify(value);
});

Vue.mixin({
    methods: {
        route: route
    }
});


// put some global methods defined above into the vue global context for use in components
Vue.prototype.getUrlParam = getUrlParam;
Vue.prototype.slugify = slugify;

/**
 * Define our Vue custom components, primarily if not completely from vendors
 */

Vue.component('v-select', vSelect);

Vue.component(
    'ups-shipping-options', 
    () => import(
        /* webpackPrefetch: true */
        /* webpackchunkname: "ups-shipping-options" */
        './components/shipping/UpsShippingOptions'
    )
);

Vue.component('pagination', require('laravel-vue-pagination'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

window.app = new Vue({
    el: '#app'
});

$(document).ready(function() {
 
    var brands_owl = $("#brands-home");
    brands_owl.owlCarousel({
        items : 9, //10 items above 1000px browser width
        autoplay: false, 
        margin: 15,
        loop: true,
        nav: true,
        responsiveClass:true,
        navText : ["<i class='fas fa-angle-left'></i>","<i class='fas fa-angle-right'></i>"],
        responsive:{
            0:{
                items:2,
        
            },
            600:{
                items:6,
               
            },
            1000:{
                items:9,
                
            }
        }
    });
    
    var product_owl = $("#products-home");
    // console.log(owl);
    product_owl.owlCarousel({
        items : 4, //10 items above 1000px browser width
        itemsMobile : false, // itemsMobile disabled - inherit from itemsTablet option
        autoplay: false, 
        margin: 15,
        loop: false,
        rewind: true,
        nav: true,
        stageClass: 'owl-stage product-container',
        navText : ["<i class='fas fa-angle-left'></i>","<i class='fas fa-angle-right'></i>"],
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
                nav: true,
            },
            700:{
                items:2,
                nav: true,
            },
            1000:{
                items:3,        
                nav: true,
            },
            1400:{
                items:4,        
                nav: true,
            }
        }
    });
});

$('.rating').rateYo({
    starWidth: "20px",
    spacing: "5px",
    halfStar: true,
}).on("rateyo.change", function (e, data) { 
    var rating = data.rating;
    $('input[name="grade"]').val(rating);
});

// jquery toggle functionality
$('body').on('click', '[data-toggle="collapse"]', function(e){
    var $target = $(e.target);
    if($target.hasClass('collapsed')) {
        $target.find('.fa').toggleClass('fa-plus', 'fa-minus')
    } else {
        $target.find('.fa').toggleClass('fa-minus', 'fa-plus')
    }
});

// Deep-linking Bootstrap tabs
function deepLinkBootstrap() {
    let url = location.href.replace(/\/$/, "");

    if (location.hash) {
        const hash = url.split("#");
        $('.nav-pills a[href="#'+hash[1]+'"]').tab("show");
        url = location.href.replace(/\/#/, "#");
        history.replaceState(null, null, url);
    } 
}

$('body').on('click', 'a[data-toggle="pill"]', function() {
    let url = location.href.replace(/\/$/, "");
    let newUrl;
    const hash = $(this).attr("href");
    if(hash == "#home") {
        newUrl = url.split("#")[0];
    } else {
        newUrl = url.split("#")[0] + hash;
    }
    history.replaceState(null, null, newUrl);
});
// END Deep-linking Bootstrap Tabs


// AJAX Review Submit
$('body').on('submit','.form--add-review', function(e){
    e.preventDefault();
    var action = this.action;
    $.ajax({
        type: 'post',
        url: action,
        data: {
            '_method': 'post',
            'name': $('input[name="name"]').val(),
            'title': $('input[name="title"]').val(),
            'product_id': $('input[name="product_id').val(),
            'email_address': $('input[name="email_address"]').val(),
            'description': $('textarea[name="description"]').val(),
            'grade': $('input[name="grade"]').val(),
            'review_dont_fill': $('input[name="review_dont_fill"]').val(),
        },
        headers: { 'X-CSRF-Token' : $('input[name="_token"]').val() },
        success: function(data) {
            $('.ajax-response-message').remove();
            $('.form--add-review').prepend('<div class="text-success h5 ajax-response-message">Thank you for contributing your review!</div>');
            var $reviewDisplay = $($('.list__review:eq(0)').clone());
            $('.list__review').attr('id','review-' + data.id);
            $('.list__review__title', $reviewDisplay).html(data.title);
            $('.list__review__name', $reviewDisplay).html('reviewed by ' + data.name);
            $('.list__review__email_address', $reviewDisplay).html(data.email_address);
            $('.list__review__date', $reviewDisplay).html('(posted on ' + data.formattedDate + ')');
            $('.list__review__description', $reviewDisplay).html(data.description);
            $('.list__review__grade', $reviewDisplay).attr('id', 'review-' + data.id + '-rating');
            $('.rating', $reviewDisplay).attr('data-rateyo-rating', data.grade);
            $('#review-' + data.id + '-rating', $reviewDisplay).rateYo({
                rating: data.grade,
                starWidth: "20px",
                spacing: "5px",
                halfStar: true,
                readOnly: true
            });
            $('.review-count').html(Number.parseInt($('.review-count')[0].innerText) + 1);
            $('.list--reviews__list').prepend($reviewDisplay);
            var timeout = window.setTimeout(function(){
                $('[data-dismiss="modal"]').click();
                $('input, textarea', '.form--add-review').not('[name="_token"]').val('');
                var $rateYo = $('.form--add-review .rating').rateYo("rating", 0);
                $('.ajax-response-message').remove();
            }, 1000);
        },
        error: function( data, status, error ) {
            $('.ajax-response-message').remove();
            $('.form--add-review').prepend('<div class="text-red h5 ajax-response-message">There was an error processing your request. ' + error + '</div>');
        }
    });
})
// END AJAX Review Submit

$(document).ready(() => {
    deepLinkBootstrap();
});


$("body").on('click','.nav-item--search__link', function(e){
    e.preventDefault();
    let link = $(e.target).closest('a');
    let icon = link.find('.fa, .svg-inline--fa');
    icon.delay(500).toggleClass('fa-search').toggleClass('fa-times');
    $('.nav-item--search__form').toggleClass('form-visible');
    $('.main-navbar').toggleClass('search-visible');
});

$('body').on('click','.home__promo__btn--search-inventory', function(e){
    e.preventDefault();
    $('.nav-item--search__link').click();// open search bar in top nav
    $('.nav-item--search__form #keyword').focus();// set focus in search field
});