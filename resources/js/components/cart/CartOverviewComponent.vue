<template>
	
	<div :style="shouldStick ? myStyle : ''">

		<h1 class="font-weight-normal text-dark mb-4 line-height-1-1 h3">Order Summary</h1>

		<div v-if="totalItems > 0">

			<h2 class="font-weight-light text-dark mb-3 line-height-1-1 h5">Items To Ship</h2>

			<div class="p-2 bg-white border-muted-6">
				<table class="table-sm table-hovered table-borderless mb-3 w-100">
					<tbody>
						<tr class="border-bottom border-secondary" v-for="cartItem in availabeCartItems">
							<td class="align-top">
								<code class="text-dark font-weight-bold">({{ cartItem.quantity }})</code>
								<a class="text-dark" :href="route('product.show', cartItem.attributes.slug !== undefined && cartItem.attributes.slug !== null ? cartItem.attributes.slug : cartItem.attributes.id).url()">
									{{ cartItem.name }}
								</a>
							</td>
							<td class="align-middle">
								<a href="#" class="text-default small" @click.prevent="openCart()">Edit</a>
							</td>
							<td class="align-middle text-right">
								<code class="text-dark font-weight-bold text-nowrap">{{ cartItem.price * cartItem.quantity | currency }}</code>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="w-100">
					<div class="d-flex flex-column align-items-center justify-content-between px-1">
						<div class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between">
							<div class="align-top py-0 font-weight-bold" colspan="2">Subtotal</div>
							<div>
								<code class="font-weight-bold py-0 text-dark">{{ cartSubtotal | currency }}</code>
							</div>
						</div>
						<div v-if="discount > 0" class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between">
							<div class="align-top py-0 font-weight-bold" colspan="2">Discount</div>
							<div>
								<code class="font-weight-bold py-0 text-dark">{{ discount | currency }}</code>
							</div>
						</div>
						<div v-if="taxValue > 0" class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between">
							<div class="align-top py-0 font-weight-bold" colspan="2">Tax</div>
							<div>
								<code class="font-weight-bold py-0 text-dark">{{ taxValue | currency }}</code>
							</div>
						</div>
						<div v-if="shippingPrice > 0" class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between">
							<div class="align-top py-0 font-weight-bold" colspan="2">Shipping</div>
							<div>
								<code class="font-weight-bold py-0 text-dark">{{ shippingPrice | currency }}</code>
							</div>
						</div>
						<div class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between">
							<div class="align-top py-0 font-weight-bolder text-darker" colspan="2">Total</div>
							<div>
								<code class="font-weight-bolder py-0 text-darker">{{ cartTotal | currency }}</code>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="mt-5">
	            <coupon-code-component>
	            	<h2 class="h5 mb-2 font-weight-bold">Have A Promo Code</h2>
	            </coupon-code-component>
			</div>
		</div>

		<div v-else>

			<div class="alert alert-danger mb-0" v-if="loaded">
				Your cart is empty.
			</div>

		</div>

	</div>

</template>

<script>

	export default {

		data() {
	        return {
	            loaded: false,
	            taxRate: 0,
	            discount: 0,
	            currentZipcode: '',
	            shipping: [],
	            zipcodes: [],
	            cartItems : [],

	            originalTop: 0,
                scrollY: null,
              	myStyle: {},
              	top: 110,
              	newTop: 0,
              	containerWidth: 0,
              	shouldStick: false,
	        }
	    },

	    watch: {
		   	'currentZipcode': function(val, oldVal){

		   		let self = this
		
		   		$.ajax({
		   			url: '/tax/' + val,
		   			method: 'PUT',
		   			dataType: 'json',
		   			data: {
			   			zipcode: val,
						_token: $('meta[name="_token"]').attr('content'),
			   		}
		   		})
		   		.done(function(response) {
		   			self.taxRate = response
		   		})
		   		.fail(function() {
		   			self.taxRate = 0
		   		})	
			},

			cartItems(newValue) {
	        	this.$root.$emit('cartContentLoaded', newValue)
	        },

			scrollY(newValue) {
	          	let newTop = this.scrollY + this.top - this.originalTop;
	          	let newSecondaryTop = newTop

	          	if (newTop > 0) {
	          		this.newTop = newTop;
	            	this.shouldStick = true
	          	} else {
	            	this.shouldStick = false
	          	}

	          	if (this.shouldStick) {
	          		// 200px footer height
	          		newSecondaryTop = $(window).height() - newTop + 110
	          		
	          		if (newSecondaryTop < 0) {
	          			this.myStyle = `width: ${this.containerWidth}px; top: ${newSecondaryTop}px; position: fixed; z-index: 100;`
	          		}
	          	}
	        },
	    },

	    mounted(){

	    	if (! isMobile()) {
	    		window.addEventListener('scroll', (event) => {
			      	this.scrollY = Math.round(window.scrollY);
			    });
	    	}

	        this.refresh()

	    	this.$root.$on('cartItemUpdated', cartItem => {
	    		this.cartItems = this.cartItems.map(item => {
	    			if( item.id === cartItem.id ){
	    				return cartItem;
	    			}
	    			return item
	    		})
	    	})

	    	this.$root.$on('cartItemDeleted', cartItem => {
	    		this.cartItems = this.cartItems.filter(item => {
	    			return item.id !== cartItem.id
	    		})
	    	})

	    	this.$root.$on('shippingUpdated', shipping => {
	    		this.shipping = shipping
	    		$('#credit-card-container').find('.jq-overlay').remove()
	        	$('.jq-confirm-checkout').attr('disabled', false)
	    	})
	    	
	    	this.$root.$on('cartTaxUpdated', zipcode => {
	    		this.zipcodes.push(zipcode)
	    		this.currentZipcode = this.zipcode
	    	})
	    	
	    	this.$root.$on('couponCodeAdded', discount => {
				this.refresh()
	    	})

	    	if (! isMobile()) {
	    		this.originalTop = this.$el.getBoundingClientRect().top;
		        this.containerWidth = $(this.$el).parent().width()
		        this.myStyle = `width: ${this.containerWidth}px; top: ${this.top}px; position: fixed; z-index: 100;`
	    	}
	    },

	    methods: {

	    	openCart() {
	    		this.$root.$emit('openCart');
	    	},

	    	refresh() {

	    		let self = this

	    		$.ajax({
	    			url: '/cart',
	    			type: 'GET',
	    			dataType: 'json',
	    		})
	    		.done(function(response) {
		          	self.loaded = true;
		            self.cartItems = response.cartItems;
		            self.taxRate = response.taxRate
		            self.discount = response.discount
	    		})
	    	}
	    },

	    computed: {

	        totalItems(){
	            return this.cartItems.filter(item => {
	                return item.deleted === false
	            }).length
	        },

	        availabeCartItems(){
	            if (this.cartItems.length === 0) {
	                return []
	            }
	            return this.cartItems.filter(item => {
	                return item.deleted === false
	            })
	        },

	        shipStationShippingPrice(){
	        	try {

		        	let cost = 0;
		        	let otherCost = 0;
		        	
		        	if ( typeof(this.shipping) === 'object') {
		        		cost = parseFloat(this.shipping.shipmentCost)
		        		cost = isNaN(cost) ? 0 : cost;

		        		otherCost = parseFloat(this.shipping.otherCost)
		        		otherCost = isNaN(otherCost) ? 0 : otherCost;
		        	}

		        	return cost + otherCost;

	        	} catch (e) {
	        		return 0;
	        	}
	        },

			shippingPrice(){
	        	try {

	        		let cost = 0;

		        	if ( typeof(this.shipping) === 'object') {
		        		cost = parseFloat(this.shipping.cost)
		        		cost = isNaN(cost) ? 0 : cost;
		        	}

		        	return cost;

	        	} catch (e) {
	        		return 0;
	        	}
	        },

	        taxValue(){
	        	return ( this.cartSubtotal * this.taxRate/100 )
	        },

	        zipcode(){

	        	if (this.zipcodes.length === 0) {
	        		return false
	        	}

	        	let shippingZipcode = this.zipcodes.filter(zipcode => {
	        		return zipcode.addressType == 'shipping'
	        	})

	        	if (shippingZipcode.length) {
	        		return shippingZipcode[ shippingZipcode.length - 1 ].zipcode
	        	}

	        	return this.zipcodes[ this.zipcodes.length - 1 ].zipcode
			},

			cartSubtotal() {
				let subtotal = 0

				this.cartItems.map(function(cartItem) {
					subtotal += (cartItem.price * cartItem.quantity)
					return cartItem
				})

				return subtotal;
			},

	        cartTotal(){
	        	return (this.cartSubtotal + this.shippingPrice + this.taxValue) - this.discount
	        },
	    }

	}
	
</script>