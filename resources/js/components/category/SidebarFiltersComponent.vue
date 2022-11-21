<template>
<div class="sidebar-filters">
    <div class="order-3 order-sm-1 col-12 px-0">
        <div class="filter form-group filter--price border-0 mb-3 text-uppercase px-sm-0">
            <slider-component :min="priceRange[0] - 10" :max="priceRange[1] + 10" @range-updated="updatePriceRange($event)">
                <div class="bg-white border-radius-0 px-0 d-flex align-items-start text-nowrap justify-content-between">
                    <label class="h6 font-weight-bold text-capitalize text-black mb-0">Filter by price</label>
                </div>
            </slider-component>
        </div>
    </div>

    <template v-for="(values, setName) in properFilterSets">
        <div
            v-if="setName.length > 0"
            class="card-header bg-secondary-4 text-black border-0 border-radius-0 pt-2 pb-3">
            <a 
                data-toggle="collapse" 
                :href="'#' + slugify(setName) + '-filter-container'" 
                role="button" 
                aria-expanded="true" 
                aria-controls="categories-filter-container"
                class="text-black d-flex flex-row justify-content-between align-items-center p-0 m-0"
            >
                <span>{{ setName }}</span> <i class="fas fa-caret-down"></i>
            </a>
        </div>
        <div
            v-if="setName.length > 0" 
            class="sidebar-filter--list card-body bg-secondary-4 p-0 pb-4 collapse show filter"
            :class="'filter-' + slugify(setName)"
            :id="slugify(setName) + '-filter-container'">
            <ul class="list-unstyled px-3 mb-0">
                <li v-for="value in values" class="form-check">
                        <input
                            v-on:change="alterFilters(this)"
                            class="form-check-input"
                            type="checkbox"
                            :name="'filter_' + slugify(setName)"
                            :id="slugify(setName) + '_' + value.slug.en"
                            :value="value.slug.en"
                            :data-val="value.slug.en"
                            :data-type="'tag-' + slugify(setName)">
                    <label
                        :for="slugify(setName) + '_' + value.slug.en"
                        class="form-check-label">
                        {{ value.name.en }}
                    </label>
                </li>
            </ul>
        </div>
    </template>
</div>
</template>

<script>

export default {
    props: {
        filterSets: {},
        selected: {},
        category: '',
        priceFrom: {
            type: Number,
            default: 0
        },
        priceTo: {
            type: Number,
            default: 1000
        },
    },
    data(){
        return {
				priceRange: [this.priceFrom, this.priceTo]
        }
    },
    computed: {
        properFilterSets() {
            
            var self = this,
             catName = this.category.toLowerCase();
             allowedFilters = [
                'Output',
                'Resolution',
                'Functions',
                'Duty Cycle',
            ];
            if(catName == 'printers' || catName == 'copiers') {
                allowedFilters.push('Print Speed');
            } else if (catName != 'scanners') {// We are in parts
                var allowedFilters = [
                    'Weight',
                    'Yield',
                    'For use in',
                ];
            }
            var properFilterSets = allowedFilters.reduce(function(acc, filter) {
                if(filter == 'Weight'){
                    acc[filter] = self.filterSets[filter];
                } else {
                    acc[filter] = self.filterSets[filter];
                }
                return acc;
            }, {});

            console.log(properFilterSets);

            return properFilterSets;
        }
    },
    methods: {
        alterFilters: function (target, prices) {
            // Filter functionality
            var filters = {},
            filter,
            component = this,
            searchQuery = Vue.prototype.getUrlParam('keyword'),// Check if this is a search.
            $target = target ? target : null;

            // Operate on all elements in filters with a "data-type" attribute that defines the filter type
            // The value of that attribute will determine the filter grouping
            $('.filter [data-type]').each(function(i, el){
                var $el = $(el),
                    inputType = $el.data('type'),
                    isSelect = $el.is('select'),
                    isPrice = inputType.indexOf('price') !== -1,
                    inputVal = isSelect || isPrice ? $el.val() : $el.data('val'),
                    checked = $el.is(':checked');

                // create an associative array of grouped filter values 
                if (!filters.hasOwnProperty(inputType)) {
                    filters[inputType] = [];
                }

                // Populate the grouped filters array
                if(!filters[inputType].includes(inputVal)) {
                    if(checked || isSelect || isPrice) {
                        // if the filter element is a checkbox, a select element, or part of a price filter,
                        // simply inject the input value into the array
                        filters[inputType].push(inputVal);
                    } else if($target && $target.is('a') && filters[inputType].length === 0) {
                        // in the case that the filter is a textual link,
                        // look for a data-val attribute on the link and pass that in as the value
                        // This allows for a panel of textual filters
                        filters[inputType].push($target.data('val'));
                    }
                }
            });

            var filterQuery = this.generateFilterQuery(filters, $target, searchQuery);

            if(prices) {
                filterQuery += '&minPrice=' + prices[0] + '&maxPrice=' + prices[1];
            }

            // Update the page url without navigating
            history.pushState(null, '', window.location.pathname + filterQuery);

            // Complete the async request
            $.ajax({
                type: "GET",
                url: '/category/filter/' + this.$props.category + filterQuery,
                success: function(res) {
                    app.$root.$emit('refresh_products', res);
                    component.setPriceRange(this);
                }
            });
        },
        generateFilterQuery(filters, $target, searchQuery) {
            // Generate the updated URL
            var filterQuery = Object.keys(filters).reduce(function(acc, el) {
                var queryString = '';// Initialize query string
                if(
                    el !== 'availability' &&
                    el !== 'pagination_count' &&
                    el !== 'page' &&
                    el !== 'sort_by' &&
                    (!$target || !$target.is('a')) &&
                    el.indexOf('price') == -1) {// first focus on tag filters
                        if(filters[el].length > 0) {// Only add to the query string if there are any values to add
                            queryString += el + '=';
                            filters[el].forEach(function(elem, ind){
                                if(typeof elem === 'string') {
                                    queryString += slugify(elem, null, true);
                                    if(ind < filters[el].length - 1) {
                                        queryString += '**';//separate values with a plus sign; may need to change depending upon types of values in filters
                                    }
                                }
                            });
                        } else {
                            return acc;
                        }
                } else {
                    queryString += slugify(filters[el], null, true);
                }
                
                return acc +=  queryString + '&';
            }, '?');//Start with a questionmark to create the filter query

            // re-include whatever search query is currently active
            if(searchQuery) {
                filterQuery += 'keyword=' + searchQuery;
            }

            return filterQuery;
        },
        updatePriceRange($event) {
            this.priceRange[0] = $event.min;
            this.priceRange[1] = $event.max;
            this.alterFilters(null, this.priceRange);
        },
        // Price Range Functionality
        setPriceRange(data) {
            var maxValue = $('#price_range').data('max-value');
            if(typeof maxValue === 'undefined') {
                maxValue = data.maxPrice;
            }

            var slider = document.getElementById('price_range');

            if(slider) {
                var from = Vue.prototype.getUrParam('price_from', false) ? Vue.prototype.getUrlParam('price_from', false) : 0,
                to = Vue.prototype.getUrlParam('price_to', false) ? Vue.prototype.getUrlParam('price_to', false) : maxValue;
                if(from === 0 || from) {
                    this.updatePriceRange({
                        min: from/100,
                        max: to/100 || 300
                    });
                }                
            }
        }
    },
    mounted () {
        // Pre-check the right checkboxes
        // parse the url search into filter groups
        // loop through those groups and check the right boxes
        var sets = this.filterSets;
        var component = this;
        for(const setName in sets) {
            var set = sets[setName];
            var tagVal = Vue.prototype.getUrlParam('tag-' + slugify(setName));
            if(tagVal) {
                var vals = tagVal.split('**');
                vals.forEach(function(el){
                    $('.filter-' + slugify(setName) + ' [data-val=' + el + ']').prop('checked', true);
                });
            }
        }

        // Figure out which accordion to expand
        if($('body').width() < 992) {
            var $lists = $('.sidebar-filter--list');
        } else {
            var $lists = $('.sidebar-filter--list').slice(1);// default first filter open on larger browsers
        }

        $lists.each(function(ind, el){
            if($('input[type="checkbox"]:checked', el).length === 0) {
                el.classList.remove('show');
            }
        });// default all filters to closed on small browsers

        // Filter to start
        $.ajax({
            type: "GET",
            url: '/category/filter/' + this.$props.category + window.location.search,
            success: function(res) {
                app.$root.$emit('refresh_products', res);
                component.setPriceRange(this);
            }
        });

    }
}
</script>