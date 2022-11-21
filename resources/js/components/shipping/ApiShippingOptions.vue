<template>
	
	<div>

		<div v-if="isShippingOptionsAvailable" class="mb-4">
			<h4 class="d-flex justify-content-between align-items-end mb-1">
	            <span class="font-weight-normal text-dark h4 mb-0">{{ title }}</span> 
	        </h4>
	        <hr class="border-muted-1 mt-0">
		</div>

		<div v-if="loading && isShippingOptionsAvailable === false" class="alert alert-primary rounded-lg w-100">
			<div class="d-flex flex-row align-items-center justify-content-between">
				<div class="mr-3">
					<strong class="font-weight-semi-bold">Please wait, Looking for available shipping options</strong>
				</div>
				<div class="fa-2x">
				  	<i class="fas fa-spinner fa-pulse"></i>
				</div>
			</div>
		</div>

		<div v-else-if="! isShippingOptionsAvailable" class="alert alert-danger rounded-lg w-100">
			<p>No valid shipping methods found for your location.</p>
			<p class="mb-0 text-right">
				<a :href="route('guest.checkout.index').url()" class="btn btn-outline-danger">Edit my address</a>
			</p>
		</div>

		<ul v-if="isShippingOptionsAvailable" class="list-group list-unstyled w-100">

			<li v-for="(shipping, slug) in shippingOptions">

				<label 
					:for="'shipping-' + slug"
					class="align-items-center border-secondary list-group-item py-1 rounded-lg mb-1 list-group-item-secondary  list-group-item-action d-flex justify-content-between"
				>
					<span class="text-capitalize d-flex flex-column justify-content-center align-items-start flex-fill text-dark flex-grow-1" style="min-height: 49.5px;">
						<span class="font-weight-bold">{{ shipping.label }}</span>
						<small v-if="shipping.deliveryDate !== undefined" class="text-muted">Delivered on {{ shipping.deliveryDate }}</small>
					</span>
					<code class="flex-shrink-1 flex-fill text-right font-weight-bold py-0 text-dark">			
						<span v-if="shipping.cost > 0">
							<span v-if="loading === false">{{ ( shipping.cost ) | currency }}</span>
							<span v-else class="text-muted">
								Loading.. <i class="fas fa-spinner fa-pulse"></i>
							</span>
						</span>
						<span v-else-if="shipping.cost === 0" class="text-primary">FREE SHIPPING</span>
						<span v-else>
							--
						</span>
					</code>
					<span v-if="loading === false" class="flex-shrink-1 text-right ml-2 d-flex">
						<input 
							:id="'shipping-' + slug"
							name="shipping_id"
							:disabled="loading"
							v-model="shippingMethod"
							:value="shipping"
							type="radio"
							@change="updateShipping()"
						>
					</span>
				</label>
			</li>
		</ul>

	</div>

</template>

<script>
	
	export default {

		props: [
			'title'
		],

		data() {
	        return {
	            shippingMethod: 0,
	            shippingOptions: 0,
	            loading: true,
	            refreshAjaxRequest: null,
	            ajaxRequest: null
	        }
	    },

	    watch: {
	    	shippingMethod(newValue) {
	    		this.$emit('shippingSelected', newValue)
	    		localStorage.setItem('selected.shipping', JSON.stringify(newValue))
	    	}	
	    },

	    mounted(){

	    	let self = this;

	    	this.refresh()

	    	try {
				this.shippingMethod = JSON.parse(localStorage.getItem('selected.shipping'))
	    	} catch (e) {
	    		this.shippingMethod = 0
	    	}

	    	if (this.shippingMethod === null || this.shippingMethod === undefined) {
	    		this.shippingMethod = 0
	    	}

	    	if (! (typeof(this.shippingMethod) === 'object' && this.shippingMethod.serviceCode !== undefined)) {
	    		this.shippingMethod = 0
	    	}

	    	this.$root.$on('cartItemUpdated', function() {
	    		self.refresh()
	    	})
	    },

	    methods: {
	    	updateShipping(){

	    		let self = this;

	    		if (this.shippingMethod === 0) {
	    			return false;
	    		}

	    		if (this.ajaxRequest !== null) {
		    		this.ajaxRequest.abort()
	    		}

	    		$('.jq-confirm-checkout').attr('disabled', true)

	    		this.ajaxRequest = $.ajax({
	    			url: 'shipping',
	    			type: 'PUT',
	    			data: this.shippingMethod,
	    		})
	    		.done(function(response) {
	    			try {
	    				self.$root.$emit('shippingUpdated', self.shippingMethod);
	    			} catch (e) {

	    			}
	    		})
	    		.always(function() {
		    		
	    		});
	    	},

	    	refresh() {
	    		let self = this
	    		this.loading = true

	    		$('.jq-confirm-checkout').attr('disabled', true)

	    		if (this.refreshAjaxRequest !== null) {
	    			this.refreshAjaxRequest.abort()
	    			this.loading = true
	    		}

	    		this.refreshAjaxRequest = $.ajax({
	    			url: '/shipping',
	    			type: 'GET'
	    		})
	    		.done(function(response) {

	    			if (response.length === 0) {
	    				$('#credit-card-container').remove()
	    				self.shippingOptions = Object.assign({}, self.shippingOptions, [])
	    				self.shippingOptions = [];
	    			} else {
		    			try {
			    			self.shippingOptions = Object.assign({}, self.shippingOptions, response)


			    			try {
			    				if (self.shippingMethod !== undefined && self.shippingMethod !== 0 && typeof(self.shippingMethod) === 'object') {
				    				if (self.shippingOptions[self.shippingMethod.slug] !== undefined) {
				    					self.shippingMethod = Object.assign({}, self.shippingMethod, self.shippingOptions[self.shippingMethod.slug])
				    				}
				    			}
			    			} catch (e) {

			    			}

			    			self.updateShipping()
		    			} catch (e) {

		    			}
	    			}
	    		})
	    		.fail(function() {
	    			self.shippingOptions = []
	    		})
	    		.always(function(response) {
	    			self.loading = false
	    		})
	    	},
	    },

	    computed: {
	    	isShippingOptionsAvailable() {

	    		if (this.shippingOptions === 0) {
	    			window.onbeforeunload = undefined
	    			return false;
	    		}

	    		if (Object.keys(this.shippingOptions).length === 0 && this.shippingOptions.constructor === Object) {
	    			window.onbeforeunload = undefined
	    			return false;
	    		}

	    		if (this.shippingOptions.length === 0) {
	    			window.onbeforeunload = undefined
	    			return false;
	    		}

	    		return true
	    	}
	    }
	}

</script>