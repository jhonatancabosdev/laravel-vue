window.insertUrlParam = function (key, value) {
    try {
        if (history.pushState) {
            let searchParams = new URLSearchParams(window.location.search);
            searchParams.set(key, value);
            let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
            window.history.pushState({path: newurl}, '', newurl);
        }
    } catch (e) {
        
    }
}

window.formatMoney = function (number, decPlaces, decSep, thouSep) {
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
    decSep = typeof decSep === "undefined" ? "." : decSep;
    thouSep = typeof thouSep === "undefined" ? "," : thouSep;
    var sign = number < 0 ? "-" : "";
    var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
    var j = (j = i.length) > 3 ? j % 3 : 0;

    return sign +
        (j ? i.substr(0, j) + thouSep : "") +
        i.substr(j).replace(/(\decSep{3})(?=\decSep)/g, "$1" + thouSep) +
        (decPlaces ? decSep + Math.abs(number - i).toFixed(decPlaces).slice(2) : "");
}

window.prepareAjaxHeader = function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
}

window.str_slug = function(str){
    str = str.replace(/^\s+|\s+$/g, ''); // trim
    str = str.toLowerCase();
  
    // remove accents, swap ñ for n, etc
    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
    var to   = "aaaaeeeeiiiioooouuuunc------";
    for (var i=0, l=from.length ; i<l ; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-'); // collapse dashes

    return str;
}


$(document).ready(function () {

    var appContainer = $(".app-container"),
        fadedOverlay = $('.fadetoblack'),
        hamburger = $('.hamburger');

    $('body').on('click', '.jq-expend-nested', function(event) {
        event.preventDefault();
        $(this).parent('.dd-item').toggleClass('dd-collapsed')
        console.log(this)
    });

    $('body').on('click', '.jq-expend-all', function(event) {
        event.preventDefault();
        $('.dd').find('.dd-item').removeClass('dd-collapsed')
    });

    $('body').on('click', '.jq-collapse-all', function(event) {
        event.preventDefault();
        $('.dd').find('.dd-item').removeClass('removeClass').addClass('dd-collapsed')
    });

    $(".hamburger, .navbar-expand-toggle").on('click', function () {
        appContainer.toggleClass("expanded");
        $(this).toggleClass('is-active');
        if ($(this).hasClass('is-active')) {
            window.localStorage.setItem('voyager.stickySidebar', true);
            appContainer.removeClass('jq-not-expanded');
        } else {
            window.localStorage.setItem('voyager.stickySidebar', false);
            appContainer.removeClass('jq-not-expanded').addClass('jq-not-expanded');
        }
    });

    if (! appContainer.hasClass('expanded')) {
        appContainer.removeClass('jq-not-expanded').addClass('jq-not-expanded');
    }

    $('select.select2').select2({
        width: '100%'
    });
    $('select.select2-ajax').each(function() {
        $(this).select2({
            width: '100%',
            ajax: {
                url: $(this).data('get-items-route'),
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: $(this).data('get-items-field'),
                        method: $(this).data('method'),
                        id: $(this).data('id'),
                        page: params.page || 1
                    }
                    return query;
                }
            }
        });

        $(this).on('select2:select',function(e){
            var data = e.params.data;
            if (data.id == '') {
                // "None" was selected. Clear all selected options
                $(this).val([]).trigger('change');
            } else {
                $(e.currentTarget).find("option[value='" + data.id + "']").attr('selected','selected');
            }
        });

        $(this).on('select2:unselect',function(e){
            var data = e.params.data;
            $(e.currentTarget).find("option[value='" + data.id + "']").attr('selected',false);
        });
    });

    $('select.select2-taggable').select2({
        width: '100%',
        tags: true,
        createTag: function(params) {
            var term = $.trim(params.term);

            if (term === '') {
                return null;
            }

            return {
                id: term,
                text: term,
                newTag: true
            }
        }
    }).on('select2:selecting', function(e) {
        var $el = $(this);
        var route = $el.data('route');
        var label = $el.data('label');
        var errorMessage = $el.data('error-message');
        var newTag = e.params.args.data.newTag;

        if (!newTag) return;

        $el.select2('close');

        var ajaxData = {
            [label]: e.params.args.data.text,
            _tagging: true,
        }

        if ($el.attr('data-slug') !== undefined) {
            ajaxData.slug = str_slug(e.params.args.data.text)
        }

        $.post(route, ajaxData).done(function(data) {
            var newOption = new Option(e.params.args.data.text, data.data.id, false, true);
            $el.append(newOption).trigger('change');
        }).fail(function(error) {
            toastr.error(errorMessage);
        });

        return false;
    }).on('select2:select', function (e) {
        if (e.params.data.id == '') {
            $(this).val([]).trigger('change');
        }
    });

    $('.match-height').matchHeight();

    $('.datatable').DataTable({
        "dom": '<"top"fl<"clear">>rt<"bottom"ip<"clear">>'
    });

    //Toggle fullscreen
    $(document).on('click', '.panel-heading a.panel-action[data-toggle="panel-fullscreen"]', function (e) {
        e.preventDefault();
        var $this = $(this);
        if (!$this.hasClass('voyager-resize-full')) {
            $this.removeClass('voyager-resize-small').addClass('voyager-resize-full');
        } else {
            $this.removeClass('voyager-resize-full').addClass('voyager-resize-small');
        }
        $this.closest('.panel').toggleClass('is-fullscreen');
    });

    $('.datepicker').datetimepicker();

    $(document).keydown(function (e) {
        if ((e.metaKey || e.ctrlKey) && e.keyCode == 83) { /*ctrl+s or command+s*/
            $(".btn.save").click();
            e.preventDefault();
            return false;
        }
    });

    $('body').on('click', '.jq-toggler', function(event) {
        event.preventDefault();
        let el = $(this)
        let options = el.attr('data-options')
        let values = el.attr('data-values')
        let value = $.trim(el.text())
        let index = 0;
        let newValue = '';
        let route = el.attr('data-route')

        try {
            options = JSON.parse(options);
            values = JSON.parse(values);

            for (var i = options.length - 1; i >= 0; i--) {
                if (options[i].toLowerCase() == value.toLowerCase()) { 
                    index = i;
                    break;
                }
            }

            newValue = options[0];
            let oldValue = options[0];

            if (options[index+1] !== undefined) {
                newValue = options[index+1];
            }

            el.text(newValue);
            el.busyLoad('show')

            $.ajax({
                url: route,
                type: 'POST',
                dataType: 'json',
                data: {
                    value: newValue,
                    options: options,
                    values: values
                },
            })
            .done(function(response) {
                toastr.success(response.message)
            })
            .fail(function(response) {
                toastr.error(response.message)
                el.text(oldValue);
            })
            .always(function() {
                el.busyLoad('hide')
            });
            

        } catch (e) {

        } 

    });

    $('textarea.easymde').each(function () {
        var easymde = new EasyMDE({
            element: this
        });
        easymde.render();
    });

    setTimeout(function() {
        $('.mce-branding').remove()
        $('#jq-loader').fadeOut();
        $('.jq-collapse-all').trigger('click')
    }, 300)

    $('[data-toggle="tooltip"]').tooltip()
    $('.mce-branding').remove()
    setTimeout(function(){
        $('[data-toggle="tooltip"]').tooltip()
        $('.mce-branding').remove()
        setTimeout(function(){
            $('[data-toggle="tooltip"]').tooltip()
            $('.mce-branding').remove()
            $('.mce-path.mce-flow-layout-item.mce-first').remove()
            setTimeout(function(){
                $('.mce-branding').remove()
                $('.mce-path.mce-flow-layout-item.mce-first').remove()
            }, 500)
        }, 500)
    }, 500)
});