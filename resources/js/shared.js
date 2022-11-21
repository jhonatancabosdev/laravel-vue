let numeral = require('numeral');

$(function() {

    prepareAjaxHeader()

    let mainDocumentTitle = document.title

    if ($('#categories-filter-container').find('ul').length > 1) {
        $('#categories-filter-container').find('ul').last().find('a').addClass('text-default')
    }

    try {

        History.Adapter.bind(window, 'statechange', function(){ 
            
            var state = History.getState()

            try {

                if (state.data.state === "pagination") {

                    $("html, body").animate({ scrollTop: 0 }, "slow");

                } else {

                    var item = $('.nav-item[data-slug="'+ state.data.slug +'"]')

                    document.title = mainDocumentTitle

                    if (state.data.slug === '') {

                        browseCategory(item, state)
                        return true;

                    } else if (item.length == 0) {
                        item = $('[data-slug="'+ state.data.slug +'"]')
                        if( item.length == 0 ){
                            location.reload()
                            return false
                        }
                    }

                    browseCategory(item, state)
                }
            } catch (e) {
                
            }


        })

        $('body').on('change', '.jq-filter-updated', function(event) {
            var formData = $(this).parents('form').serialize()
            var state = History.getState();
            var categorySlug = state.data.slug

            if (categorySlug === undefined || categorySlug === null) {
                categorySlug = $(this).attr('data-slug')
            }

            if (categorySlug === undefined || categorySlug === null) {
                categorySlug = ''
            }

            var requestedUrl = categorySlug !== '' ? categoryBrowserUrl + '/' + categorySlug : categoryBrowserUrl;
            
            requestedUrl = requestedUrl + '?' + formData

            var routeUrl = requestedUrl.replace(location.origin, '')

            History.pushState({
                    slug: categorySlug,
                    formData: formData
                }, 
                categorySlug,
                routeUrl
            )
        });

        $('body').on('click', '.jq-category-click', function(event) {

            event.preventDefault();

            try {
                var currentItem = $(this)
                var categorySlug = currentItem.data('slug')
                var requestedUrl = categoryBrowserUrl + '/' + categorySlug
                var routeUrl = requestedUrl.replace(location.origin, '')

                History.pushState({
                        slug: categorySlug
                    }, 
                    categorySlug, 
                    routeUrl
                )
            } catch (e) {
                console.log(e)   
            }

            if (isMobile()) {
                setTimeout(function() {
                    $('#navbar-toggler').trigger('click')
                }, 500)
            }

            var state = History.getState()

            if (Object.keys(state.data).length === 0 && state.data.constructor === Object) {
                browseCategory(currentItem, state)
            }
        });

    } catch (e) {
        console.log(e)
    }
})


window.isMobile = function () { 
    if( 
        navigator.userAgent.match(/Android/i)
        || navigator.userAgent.match(/webOS/i)
        || navigator.userAgent.match(/iPhone/i)
        || navigator.userAgent.match(/iPad/i)
        || navigator.userAgent.match(/iPod/i)
        || navigator.userAgent.match(/BlackBerry/i)
        || navigator.userAgent.match(/Windows Phone/i)
    )
    {
        return true;
    } else {
            return false;
    }
}

window.checkoutEcommerceEvent = function (availabeCartItems, step = 1) {
    try {
        let productsList = availabeCartItems.map(function(item){
            return {
                'id': item.id,
                'name': item.name,
                'quantity': item.quantity,
                'price': item.price,
            }
        })
        window.dataLayer.push({
            'event': 'checkout',
            'ecommerce': {
                'checkout': {
                    'actionField': {'step': step},
                    'products': productsList
                }
            }
        });
    } catch (e) {
        
    }
}

function browseCategory(currentItem, state)
{
    var navbarTemplate = '<li class="mb-1 pl-{depth}">\
                                <a data-slug="{slug}" href="#" data-depth="{depth}" class="nav-item text-default jq-category-click d-block position-relative">\
                                    {name}\
                                </a>\
                          </li>'

    var parentItem = currentItem.parent()
    var categorySlug = currentItem.data('slug')

    if (categorySlug === undefined || categorySlug === null) {
        categorySlug = '';
    }

    var requestedUrl = categorySlug !== '' ? categoryBrowserUrl + '/' + categorySlug : categoryBrowserUrl;

    try {
        if (state.data.formData !== undefined) {
            requestedUrl = requestedUrl + '?' + state.data.formData;
        }
    } catch (e) {

    }

    $('#main-navbar .nav-item').removeClass('font-weight-bold')

    // show loading
    currentItem.removeClass('text-default').addClass('font-weight-bold')

    let icon = currentItem.parent('li').find('i.fas');

    if (icon.length !== 0) {
        currentItem.parents('#main-navbar')
            .find('.fa-minus')
            .removeClass('fa-minus')
            .addClass('fa-plus')
            
        icon.removeClass('fa-plus').addClass('fa-minus')
    }

    prepareAjaxHeader()

    var links = parentItem.siblings('li').find('a')
    links.removeClass('font-weight-bold')

    let container = '#main-navbar'

    $(container).parent().busyLoad('show')

    $.ajax({
        method: "POST",
        url: requestedUrl
    })
    .done(function(data) {

        // remove other expended subnav
        parentItem.siblings('ul').remove()

        if (data.navItems !== undefined && data.navItems.length === 0) {
            parentItem.siblings('li').find('a').addClass('text-default')
            currentItem.addClass('text-default')
        } else {
            parentItem.siblings('li').find('a').removeClass('text-default')
        }

        parentItem.next('ul').remove()
        parentItem.after('<ul class="list-unstyled jq-ajax-navbar '+parentItem.attr('class')+'">')

        $.each(data.navItems, function(index, child) {
            child.depth = child.depth + 3
            $( parentItem.next('ul') ).append( nano(navbarTemplate, child) )
        })
        
        if ($('.jq-categories-page').length === 0) {
            location.reload()
        } else {
            app.$emit('refresh_products', data)
        }
    })
    .always(function() {
        $(container).parent().busyLoad('hide')
    })
    .fail(function(ret) {
        setTimeout(function(){
            location.reload()
        }, 500)
    })
}

window.prepareAjaxHeader = function (){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
}

window.toast = function toast(html){
    $('.toast').toast('hide')
    $('body').prepend('<div aria-live="polite" id="toast" aria-atomic="true" style="position: fixed; top: 5px; right: 5px; z-index: 1000000;">\
        <div style="position: relative;" class="px-1">\
            <div class="jq-toast toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">\
                <div class="toast-body"></div>\
            </div>\
        </div>\
    </div>')
    $('.jq-toast .toast-body').html(html);
    $('.toast').toast('show')
    setTimeout(function(){
        $('#toast').remove()
    }, 3001)
}

/**
 * Nano template engine
 * @param  {[type]} template
 * @param  {[type]} data 
 * @return {[type]}    
 */
function nano(template, data) {
  return template.replace(/\{([\w\.]*)\}/g, function(str, key) {
    var keys = key.split("."), v = data[keys.shift()];
    for (var i = 0, l = keys.length; i < l; i++) v = v[keys[i]]
    return (typeof v !== "undefined" && v !== null) ? v : ""
  })
}

window.slugify = function(string) {
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

window.isVisible  =function (element) { 
    if (element.offsetWidth ||  
       element.offsetHeight ||  
       element.getClientRects().length) 
        return true; 
    else 
        return false; 
}

window.currency = function (value) {
    return numeral(value).format('$0,0.00')
};

window.currencyInt = function (value) {
    return numeral(value).format('$0,0')
};

window.truncate = function (text, stop, clamp) {
    if (text !== null && text !== undefined) {
        return text.slice(0, stop) + (stop < text.length ? clamp || '...' : '')
    }
    
    return '';
};