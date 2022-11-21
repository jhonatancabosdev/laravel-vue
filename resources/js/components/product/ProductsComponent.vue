<template>
	<div v-if="products" class="row col-12 mx-0 px-0">
		<h1
			v-if="displayCategoryName === true && response !== null && response.category !== undefined && response.category !== null"
			class="h2 mt-2 mt-lg-0 font-size-1-5rem fon-size-md-2rem text-center text-uppercase mb-3 mb-lg-5 d-flex align-items-center justify-content-center">
			{{ currentCategory.name }}
		</h1>

		<div v-if="filters" class="row jq-filters-container">

			<div v-if="false" class="col-12 col-sm-4 col-lg-3 d-none">
				<div class="filter form-group filter--price border-0 mb-3 text-uppercase">
				    <div class="bg-white border-radius-0 px-0 d-flex align-items-start justify-content-between">
			            <label class="h6 font-weight-bold text-capitalize text-black">Filter by price</label>
			            <span v-if="response !== null">{{ response.minPrice | currency }} - {{ response.maxPrice | currency }}</span>
				    </div>
				    <div class="bg-white px-0">
		                <div class="mt-3 mb-5">
		                    <div data-max-value="100" ref="slider"></div>
		                    <input v-model="priceFrom" type="hidden">
		                    <input v-model="priceTo" type="hidden">
		                </div>
				    </div>
				</div>
			</div>

			<div class="col-12 col-sm-8 col-lg-9 order-2 order-sm-1">
				<div class="row">
					<div class="col-6 col-lg-4">
						<div class="form-group d-flex flex-column">
							<label class="h6 font-weight-bold text-capitalize text-nowrap text-black">Products per page</label>
							<select  @change="getResults()" v-model="perPage" class="form-control form-control-sm text-center border-highlight border px-2 py-1 rounded text-highlight bg-white">
					            <option value="50">50 Per Page</option>
					            <option value="24">24 Per Page</option>
					            <option value="16">16 Per Page</option>
					            <option value="8">8 Per Page</option>
					        </select>
						</div>
					</div>

					<div class="col-6 col-lg-4">
						<div class="form-group d-flex flex-column">
							<label class="h6 font-weight-bold text-capitalize text-nowrap text-black">Sort by</label>
							<select @change="getResults()" v-model="sortBy" class="form-control form-control-sm text-center border-highlight border px-2 py-1 rounded text-highlight bg-white">
								<option value="relevance" >Relevance</option>
								<option value="h-t-l">Price Highest to Lowest</option>
								<option value="l-t-h">Price Lowest to Highest</option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="col-12 col-sm-4 col-lg-3 order-1 order-sm-2">
				<div class="form-group d-flex flex-column">
					<label class="h6 font-weight-bold text-capitalize text-black">Display</label>
					<div class="btn-group">
						<button @click="viewType = 'list'" class="btn btn-sm" :class="viewType != 'list' ? 'btn-outline-highlight' : 'btn-highlight'"><i class="fas fa-bars"></i></button>
						<button @click="viewType = 'grid-large'" class="btn btn-sm" :class="viewType != 'grid-large' ? 'btn-outline-highlight' : 'btn-highlight'"><i class="fas fa-th-large"></i></button>
						<button @click="viewType = 'grid'" class="btn btn-sm" :class="viewType != 'grid' ? 'btn-outline-highlight' : 'btn-highlight'"><i class="fas fa-th"></i></button>
					</div>
				</div>
			</div>
		</div>

		<div v-if="products" class="row col-12 px-0 mx-auto">
			<product-listing-component v-if="products.data" v-for="product in products.data" :product="product" :tags="product.tags" :currentCategory="currentCategory === '' ? {} : JSON.parse(currentCategory)" :view-type="viewType" :key="product.id"></product-listing-component>
		</div>
		<div class="row col-12 mt-4" v-if="response !== null && (response.products === undefined || response.products.total === 0)">
			<div class="alert alert-warning alert-dismissible fade show" role="alert">
				No products in this category match your filters.
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		</div>
		<div v-if="response !== null && response.category !== undefined" class="col-12 text-right">
			<small v-if="response.products !== undefined">Total products: {{ response.products.total }}</small>
		</div>
		<div v-else class="col-12 text-right">
			<small>Total products: {{ products.total }}</small>
		</div>
		
		
		<div class="col-12 mt-5 pagination">
			<div class="flex-wrap align-items-center justify-content-center w-100 d-flex">
				<pagination :limit="4" :data="products" align="right" @pagination-change-page="getResults">
					<span slot="prev-nav">PREV</span>
					<span slot="next-nav">NEXT</span>
				</pagination>
			</div>
		</div>
	</div>
</template>

<script>

	export default {

		props: {
			currentPage: {
				type: Number,
				default: 1
			},
			initialLoad: {
		      	type: Boolean,
		      	default: true
		    },
		    displayCategoryName: {
		      	type: Boolean,
		      	default: true
		    },
		    displayBreadcrumb: {
		      	type: Boolean,
		      	default: true
		    },
			filters: {
				type: Boolean,
				default: false
			},
			currentCategory: {
				type: String,
				default: ""
			},
			prods: {
				type: Object,
				default: function(){
					return null;
				}
			}
		},

		data(){
			return {
				response: null,
				products: this.prods,

				page: 1,
				viewType: 'grid',
				sortBy: 'relevance',
				perPage: 24,

				slider: null,
				priceFrom: 0,
				priceTo: 0,
			}
		},

		mounted() {

			let self = this;
			this.page = Vue.prototype.getUrlParam('page', false) ? Vue.prototype.getUrlParam('page', this.currentPage) : this.currentPage;
			
			this.$root.$on('refresh_products', function(response) {
				self.response = response
				self.products = response.products
			});

			if (this.initialLoad === true) {
				this.getResults(this.page);
			}
		},

		methods: {
			getResults(newPage = 1) {
				let self = this

				$('.jq-page-content').busyLoad('show')

				let state = History.getState()
				self.page = newPage

				let vars = location.search
					.split('&')
					.filter(function(e) {
						return e.search('page=') === -1
					})
					.filter(function(e) {
						return e.length > 0;
					});

				let delimiter = vars.length > 0 ? '&' : '?' 
				let link = vars.join('&');

				if (newPage > 1) {
					link = link + delimiter + 'page=' + self.page;
				}

				History.replaceState(
					{
						page: self.page,
						state: 'pagination'
					}, 
					document.title, 
					location.protocol + '//' + location.hostname + location.pathname + link
				);

                let url = this.initialLoad 
                    ? '/category/filter/' + JSON.parse(this.$props.currentCategory).slug + window.location.search
                    : window.location.pathname + window.location.search

				$.ajax({
					type: "GET",
					url: url,
					success: function(res) {
						app.$root.$emit('refresh_products', res);
						app.$root.$emit('set_price_range', res);
					}
				})
				.always(function() {
					$('.jq-page-content').busyLoad('hide')
				});
				
			}
		}
	}

</script>