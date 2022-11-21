<template>
    
    <div class="product-listing flex-column d-flex" :class="onListing ? '' : 'col-xl-4 col-lg-6 col-12 pr-md-0 mb-3'">
        <div class="card-body h-100 d-flex flex-column px-0 pb-3 pt-3">
            <add-to-favorites
                v-if="product.quantity > 0"
                class="position-absolute d-flex justify-content-end h-auto mb-2"
                context="short"
                icon="fa-star"
                defaultFilled="true"
                :product="product">
            </add-to-favorites>
            <div v-if="product.quantity <= 0" class="position-absolute product-container__badge__wrapper product-container__badge--out-of-stock">
                <span class="badge badge-red text-white border-radius-0 py-1 px-2">Out of Stock</span>
            </div>
            
            <a 
                :href="productLink" 
                @click.prevent.stop="showProduct()" 
                :class="'card-img-top px-3 jq-product-click text-center d-flex' + (viewType !== '' && viewType === 'list' ? ' col-4' : '') "
            >
                <img
                    :alt="product.name"
                    :src="product.small_main_image"
                    class="img-fluid img-responsive w-auto d-block m-auto"
                >
            </a>
            <div class="d-flex flex-column flex-grow-1 justify-content-top py-2 px-3 text-left">
                
                <a 
                    v-if="product.brand !== undefined && product.brand !== null" 
                    :href="route('brand.show', product.brand)" 
                    @click.prevent.stop="showProduct()" 
                    class="text-secondary-5"
                >
                    <small class="text-uppercase">{{ product.brand.name }}</small>
                </a>

                <h3 class="card-title monsterrat h3 my-1">
                    <a 
                        :href="productLink" 
                        @click.prevent.stop="showProduct()" 
                        class="text-dark"
                    >
                        {{ product.name }}
                    </a>
                </h3>

                <div class="my-1">
                    <span class="text-highlight h3">

                        <strike 
                            v-if="product.compare_at_price > 0" 
                            class="text-secondary-7 h4"
                        >
                            <small>{{ product.compare_at_price | currency }}</small>
                        </strike>

                        <strike 
                            v-else 
                            class="text-secondary-7 h4"
                        >
                            <small>{{ product.original_price | currency }}</small>
                        </strike>

                        {{ product.price | currency }}
                    </span>
                </div>
                <div class="row">
                    <dl class="col-12 mt-3 px-3 d-flex flex-wrap font-family-alt">
                        <template v-for="tag in categoryTags">
                            <dt v-bind:key="tag.id ? tag.id + '-type' : Math.floor(Math.random() * (10000000)) + 1 + '_type'" v-if="tag.type" class="col-7 px-0 py-1 font-weight-normal border-bottom border-secondary-5">{{ tag.type }}:</dt>
                            <dd v-bind:key="tag.id ? tag.id + '-name' : Math.floor(Math.random() * (10000000)) + 1 + '_name'" v-if="tag.name" class="mb-0 col-5 px-0 py-1 font-weight-normal border-bottom border-secondary-5" :title="tag.name === Object(tag.name) ? tag.name.en : tag.name">{{ tag.name === Object(tag.name) ? tag.name.en : tag.name | truncate(15) }}</dd>
                        </template>
                    </dl>
                </div>
            </div>

            
            <div class="px-3 card-text text-center d-flex flex-lg-row flex-md-column mt-auto mb-0 align-self-end justify-content-between w-100 product__listing--buttons">
                <div class="col-12 px-0">
                    <a 
                        :href="productLink" 
                        @click.prevent.stop="showProduct()" 
                        class=" h2 text-uppercase px-lg-2 d-block px-xl-3 btn btn-highlight border-radius-0 mb-lg-0 mb-md-3 w-100 product__listing--buttons--view-more"
                    >
                        View More
                    </a>
                </div>
            </div>
        </div>
    </div>
    
</template>

<script>
    export default {

        props: {

            product: {
                type: Object,
                default: function() {
                    return {}
                }
            },
            
            onListing: {
                type: Boolean,
                default: function() {
                    return false;
                }
            },
            
            tags: {},

            currentCategory: {
                type: Object,
                default: function() {
                    return {}
                }
            },

            featureFirst: {
                type: Boolean,
                default: function() {
                    return false
                }
            },

            viewType: {
                type: String,
                default: function() {
                    return ''
                }
            },

            assetUrl: {
                type: String,
                default: function() {
                    return ''
                }
            }
        },

        data() {
            return {
                timeout: null
            }
        },

        methods: {
            showProduct() {

                let elm = $(this.$el)
                let url = this.productLink

                try {
                    localStorage.setItem("scrollPosition", $(document).scrollTop());
                } catch (e) {

                }

                $(elm).busyLoad('hide')
                $(elm).busyLoad('show')

                if (this.timeout !== null && this.timeout !== undefined) {
                    clearTimeout(this.timeout)
                }

                try {
                    window.dataLayer.push({
                        'event': 'productClick',
                        'ecommerce': {
                          'click': {
                                'actionField': {'list': 'Product click'},
                                'products': [{
                                    'id': this.product.id,
                                    'name': this.product.name,
                                    'price': this.product.price,
                                    'position': 1
                                }]
                            }
                        },
                        'eventCallback': function() {
                            $(elm).busyLoad('hide')
                            document.location = url
                        }
                    });
                    this.timeout = setTimeout(function(){
                        $(elm).busyLoad('hide')
                    }, 800)
                } catch(e) {
                    $(elm).busyLoad('hide')
                    document.location = url
                }
            },
        },


        computed: {
            categoryTags() {
                if(this.currentCategory.constructor === Object && Object.entries(this.currentCategory).length !== 0){

                    let tags = this.tags.map(function(tag){
                            return {
                                type: tag.type,
                                name: tag.name.en
                            }
                    });
    
                    var tagTypes = [{
                            type: 'Output',
                            name: ''
                        },{
                            type: 'Resolution',
                            name: ''
                        },{
                            type: 'Functions',
                            name: ''
                        },{
                            type: 'Duty Cycle',
                            name: ''
                        },{
                            type: 'Print Speed',
                            name: ''
                        },{
                            type: 'Weight',
                            name: ''
                        },{
                            type: 'Yield',
                            name: ''
                        },{
                            type: 'For Use in',
                            name: ''
                    }];

                    var mappedTags = tagTypes.map(function(item){
                        var tag = tags.find(function(ele){
                            var type = ele.type ?? JSON.parse(ele.name)[0].type;
                            return type.toLowerCase() === item.type.toLowerCase()
                        });
                        if(typeof tag !== 'undefined') {
                            var tagName = tag.name[0] === '{' ? JSON.parse(tag.name)[0].name.en : tag.name
                        } else {
                            var tagName = ''
                        }
    
                        return {
                            type: item.type,
                            name: tagName
                        }
                    });
                    mappedTags = mappedTags.filter(t => t.name !== '')
                    return mappedTags;
                } else {
                    return this.tags;
                }
            },
            productLink() {
                let slug = this.product.slug;

                if (!slug || slug.length === '') {
                    slug = this.product.id;
                }

                if (this.currentCategory !== undefined && this.currentCategory.slug !== undefined) {
                    return route('product.show', {
                        'product': slug,
                        'category': this.currentCategory.slug
                    }).url()
                }

                return route('product.show', slug).url()
            }
        }

    }
</script>