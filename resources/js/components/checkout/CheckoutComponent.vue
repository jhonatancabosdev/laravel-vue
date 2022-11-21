<template>
	<form novalidate="" method="POST" class="needs-validation jq-checkout-form">

		<slot></slot>

		<div class="row mb-2">
            <div class="col-12 text-center mt-2">
                <h1 class="text-dark h2 mb-3 line-height-1-1 d-flex flex-row align-items-center justify-content-center">
                    <span class="small mr-2"><i class="fas fa-shield-alt"></i></span>
                    <span>Secure Checkout</span>
                </h1>
            </div>
        </div>

        <div class="row mb-5">
        	<div class="col-12">
        		<div class="d-flex flex-row flex-nowrap align-items-center justify-content-center" style="opacity: 0.8;">
        			<div class="bg-highlight rounded-lg flex-fill text-white" style="height: 8px;"></div>
	    			<div class="text-nowrap bg-highlight mx-n1 text-white rounded-circle d-flex align-items-center justify-content-center text-center small" style="font-size: 50%; height: 36px; width: 36px; z-index: 1;">
	    				Step 1
	    			</div>
	    			<div class="bg-secondary text-muted flex-fill mx-n1" style="height: 8px;">
	    				<div :class="showPaymentOptions ? 'bg-highlight text-white w-100' : 'bg-highlight text-muted w-75'" style="height: 8px;">
	    					
	    				</div>
	    			</div>
	    			<div  :class="showPaymentOptions ? 'bg-highlight text-white' : 'bg-secondary text-muted'" class="text-nowrap rounded-circle d-flex align-items-center justify-content-center text-center small" style="font-size: 50%; height: 36px; width: 36px; z-index: 1;">
	    				Step 2
	    			</div>
	    			<div class="rounded-lg flex-fill mx-n1 bg-secondary" style="height: 8px;">
	    				<div :class="showPaymentOptions ? 'bg-highlight text-white' : 'bg-secondary text-muted'" class="w-75" style="height: 8px;">
	    					
	    				</div>
	    			</div>
        		</div>
        	</div>
        </div>

		<div v-show="showPaymentOptions === false">
			<div class="position-relative">

				<div class="mb-5" v-if="addresses !== undefined && addresses !== null && addresses.length > 0">
					<div class="row">
						<div class="col-12 mb-3">
		        			<h4 class="d-flex justify-content-between align-items-end mb-1">
					            <span class="font-weight-normal text-dark h4 mb-0">Your Addresses</span> 
					            <small>
					            	<small>
						            	<span class="text-danger font-weight-bolder">*</span> Required Fields
						            </small>
					            </small>
					        </h4>
					        <hr class="border-muted-1 mt-0">
					       	<div class="list-group">
					       		<div class="list-group-item list-group-item-action list-group-item-secondary d-flex flex-row align-items-center justify-content-between" v-for="address in addresses" @click.prevent="selectedAddress = address">
					       			<div>
					       				<span v-if="address.address_1 !== null">{{ address.address_1 }} <br></span>
						       			<span v-if="address.address_2 !== null">{{ address.address_2 }} <br></span>
						       			<span v-if="address.city !== null">{{ address.city }}, </span>
						       			<span v-if="address.state !== undefined">
						       				{{ address.state.abv }}, 
						       			</span>
						       			<span v-if="address.zipcode">{{ address.zipcode }}, </span>
						       			<span v-if="address.country !== null && address.country.iso !== undefined">{{ address.country.iso }}</span>
					       			</div>
					       			<div>
					       				<input type="radio" v-model="selectedAddress" :value="address">
					       			</div>
					       		</div>
					       	</div>
					       	<div class="text-right" v-if="selectedAddress !== null">
					       		<button class="btn py-2 text-uppercase btn-lg min-w-180px min-w-sm-210px min-h-44px btn-danger mt-3" @click.prevent="selectedAddress = null">
					       			Clear
					       		</button>
					       	</div>
		        		</div>
					</div>
				</div>

		        <div class="mb-5">
		        	<div class="row">
		        		<div class="col-12 mb-3">
		        			<h4 class="d-flex justify-content-between align-items-end mb-1">
					            <span class="font-weight-normal text-dark h4 mb-0">Personal information</span> 
					            <small>
					            	<small>
						            	<span class="text-danger font-weight-bolder">*</span> Required Fields
						            </small>
					            </small>
					        </h4>
					        <hr class="border-muted-1 mt-0">
		        		</div>
		        		<div class="col-md-12 col-lg-12">
		        			<div class="form-group">
		        				<label class="text-dark font-size-0-9rem mb-2">
									<span class="text-danger font-weight-bolder">*</span>
			        				Full Name
			        			</label>
		                        <input 
		                            type="text" 
		                            :class="'form-control form-control rounded-0 border bg-secondary-1 border-muted-6 ' + getValidationClass('name')"
		                            id="name" 
		                            placeholder="Enter your name" 
		                            v-model="checkout.name"
		                            required
		                            name="name"
		                        >
		            			<div v-if="hasError('name')" class="invalid-feedback d-block">
									{{ getError('name') }}
								</div>
		        			</div>
		        		</div>
		        		<div class="col-md-6 col-lg-6">
		        			<div class="form-group">
		        				<label class="text-dark font-size-0-9rem mb-2">
		        					<span class="text-danger font-weight-bolder">*</span>
	        						Phone
	        					</label>
		                        <input 
		                            type="tel" 
		                            :class="'form-control form-control rounded-0 border bg-secondary-1 border-muted-6 ' + getValidationClass('phone')"
		                            id="phone" 
		                            placeholder="3105551212" 
		                            v-model="checkout.phone" 
		                            required
		                            name="phone"
		                        >

		                        <div v-if="hasError('phone')" class="invalid-feedback d-block">
									{{ getError('phone') }}
								</div>

								<div v-else class="mt-2 small">
		                        	<i class="fas fa-info-circle"></i> Needed for delivery purposes.
		                        </div>
		        			</div>
		                </div>
		                <div class="col-md-6 col-lg-6">
		                	<div class="form-group">
		                		<label class="text-dark font-size-0-9rem mb-2">
		                			<span class="text-danger font-weight-bolder">*</span>
		                			Email
		                		</label>
		                        <input 
		                            type="email" 
		                            :class="'form-control form-control rounded-0 border bg-secondary-1 border-muted-6 ' + getValidationClass('email')"
		                            id="email" 
		                            placeholder="email@example.com" 
		                            v-model="checkout.email"
		                            required
		                            name="email"
		                        >

		                        <div v-if="hasError('email')" class="invalid-feedback d-block">
									{{ getError('email') }}
								</div>

								<div v-else class="mt-2 small">
		                        	<i class="fas fa-info-circle"></i> Needed for delivery purposes.
		                        </div>

		                	</div>
		                </div>
		        	</div>

		        	<address-component
		                :address="checkout.billingAddress"
		                :errors="errorsList"
		                on-checkout="true"
		                address-type="billing"
		            >
		            </address-component>

		        </div>

		        <div class="mb-5" id="shipping-address">

		        	<div class="mb-4">
	        			<h4 class="d-flex justify-content-between align-items-end mb-1">
				            <span class="font-weight-normal text-dark h4 mb-0">Shipping address</span> 
				            <small v-if="checkout.shippingAddressDifferent == 'true'">
				            	<small>
					            	<span class="text-danger font-weight-bolder">*</span> Required Fields
					            </small>
				            </small>
				        </h4>
				        <hr class="border-muted-1 mt-0">
	        		</div>

		        	<div class="mb-4">
		        		<div class="custom-control custom-radio mb-2">
				            <input 
				                type="radio" 
				                value="false"
				                class="custom-control-input" 
				                id="ship-to-billing-address"
				                name="shipping_address_different"
				                v-model="checkout.shippingAddressDifferent"
				            >
				            <label class="custom-control-label text-dark" for="ship-to-billing-address">
				                Ship to My Billing Address
				            </label>
				        </div>
			        	<div class="custom-control custom-radio">
				            <input 
				                type="radio" 
				                value="true"
				                class="custom-control-input" 
				                id="shipping_address_different"
				                name="shipping_address_different"
				                v-model="checkout.shippingAddressDifferent"
				            >
				            <label class="custom-control-label text-dark" for="shipping_address_different">
				                Ship to a Different Address
				            </label>
				        </div>
		        	</div>

		            <div class="mb-4" v-if="checkout.shippingAddressDifferent == 'true'">
		                <address-component 
		                    :address="checkout.shippingAddress"
		                    :errors="errorsList"
		                    on-checkout="true"
		                    address-type="shipping" 
		                >
		                </address-component>
		            </div>
		        </div>

			</div>

			<div v-show="showPaymentOptions === false" class="my-5 text-right">

	    		<button type="button" :disabled="loading" class="btn btn-outline-muted-4 py-2 text-uppercase btn-lg min-w-180px min-w-sm-210px min-h-44px mr-1" @click="resetLocalStorage()">
	        		<small class="py-1 font-weight-border font-size-0-85rem font-size-sm-0-9rem">Reset Form</small>
	        	</button>

	    		<button 
	    			v-if="canCheckout"
	        		class="btn py-2 text-uppercase btn-lg min-w-180px min-w-sm-210px min-h-44px"
	        		:class="hasErrors ? ' btn-danger' : 'btn-highlight'"
	        		:disabled="loading"
	            	@click.prevent="validate()"
	        		type="button"
	        	>
	        		<small class="py-1 d-block font-weight-bold font-size-0-9rem">
	        			<span v-if="! hasErrors">
		            		<span v-if="loading === false">Continue</span>
		            		<span v-else>
							  	<i class="fas fa-spinner fa-pulse mr-2"></i>
		            			Please wait
		            		</span>
		        		</span>
		        		<span v-else>
		        			<i class="fas fa-sync mr-2"></i>
		        			TRY AGAIN
		        		</span>
	        		</small>
	        	</button>

	        	<div v-if="hasErrors" class="mt-3 alert alert-danger text-left" id="error-container">
	        		<div class="d-flex flex-row">
	        			<div class="pr-3">
		        			<i class="fas fa-exclamation-triangle"></i>
		        		</div>
		        		<div>
		        			{{ validationErrors.message }}
		        		</div>
	        		</div>

	        		<div class="mt-2" v-if="validationErrors.errors">
	        			<ul class="mt-0">
	        				<li v-for="error in validationErrors.errors">{{ error[0] !== undefined && error[0] !== null ? error[0] : '' }}</li>
	        			</ul>
	        		</div>
	        	</div>
	        </div>

		</div>

		<div id="shipping-container" v-if="showPaymentOptions === true" class="mb-5">
        	<api-shipping-options
        		@shippingSelected="updateShippingSelected($event)"
				class="mb-4 mt-2"
                title="Choose your shipping method"
            >
            </api-shipping-options>
        </div>

		<div v-if="showPaymentOptions === true">
			<div class="row">

				<div class="col-12 mt-3">
        			<h4 class="d-flex justify-content-between align-items-end mb-1">
			            <span class="font-weight-normal text-dark h4 mb-0">Payment method</span> 
			            <small v-if="checkout.paymentMethod == 'credit_card'">
			            	<small>
				            	<span class="text-danger font-weight-bolder">*</span> Required Fields
				            </small>
			            </small>
			        </h4>
			        <hr class="border-muted-1 mt-0">
        		</div>

				<div class="col-12">

					<div class="btn-group-toggle d-flex flex-row align-items-center" data-toggle="buttons">

						<label 
							@click.prevent="checkout.paymentMethod = 'credit_card'" 
							class="btn font-family-open-sans btn-white mr-2 rounded px-3 d-flex align-items-center flex-nowrap" 
							:class="checkout.paymentMethod == 'credit_card' ? 'active' : ''"
						>
					    	<input 
					    		v-model="checkout.paymentMethod"
					    		name="payment_method" 
					    		type="radio" 
					    		value="credit_card"
					    	> 
					    		<i class="fas fa-2x fa-credit-card"></i>&nbsp;&nbsp;Credit card
					  	</label>

						<label 
							@click.prevent="checkout.paymentMethod = 'paypal'"
							class="btn font-family-open-sans btn-white rounded px-3 d-flex align-items-center flex-nowrap" 
							:class="checkout.paymentMethod == 'paypal' ? 'active' : ''"
						>
					    	<input 
					    		v-model="checkout.paymentMethod"
					    		name="payment_method" 
					    		type="radio" 
					    		value="paypal"
					    	> 
					    		<i class="fab fa-2x fa-cc-paypal"></i>&nbsp;&nbsp;Paypal
					  	</label>

					</div>

				</div>

			</div>
		</div>

		<div class="my-5">

	        <div v-if="showPaymentOptions === true" class="my-5 text-right">

	        	<div v-if="canUsePaypal === false && checkout.paymentMethod === 'paypal'">
					<div class="alert alert-danger mb-5 text-left">
						PayPal supports only one subscription or multiple one-time charge items in cart
					</div>
				</div>

	        	<div v-if="checkout.paymentMethod === 'credit_card' && shippingSelected === true" class="col-12 text-left">
					<credit-card-component class="mt-5 mx-auto"></credit-card-component>
				</div>

	        	<button type="button" :disabled="loading" class="btn btn-outline-muted-4 py-2 text-uppercase btn-lg min-w-180px min-w-sm-210px min-h-44px mr-1" @click="showPaymentOptions = false;">
	        		<small class="py-1 font-weight-border font-size-0-85rem font-size-sm-0-9rem">Return Back</small>
	        	</button>

	        	<button 
	        		v-if="checkout.paymentMethod === 'credit_card' || (checkout.paymentMethod === 'paypal' && canUsePaypal)"
		        	type="button"
	        		:disabled="loading || shippingSelected === false" 
	        		class="btn py-2 text-uppercase btn-lg min-w-180px min-w-sm-210px min-h-44px btn-highlight" 
	        		@click="executeCheckout()"
	        	>
	        		<small class="py-1 d-block font-weight-bold font-size-0-9rem">
	        			<span v-if="! hasErrors">
		            		<span v-if="loading === false">Confirm order</span>
		            		<span v-else>
							  	<i class="fas fa-spinner fa-pulse mr-2"></i>
		            			Please wait
		            		</span>
		        		</span>
		        		<span v-else>
		        			<i class="fas fa-sync mr-2"></i>
		        			TRY AGAIN
		        		</span>
	        		</small>
	        	</button>

	        </div>
		</div>

	</form>
</template>
<script>

	import errorsBag from '../../helpers/errors';

	export default {

		props: [
			'name',
			'email',
			'phone',
			'routeUrl', 
			'validateUrl', 
			'errors',
			'addresses' 
		],

		data() {
			return {

				selectedAddress: null,

				cartItems: [],
				shippingSelected: false,
				showPaymentOptions: false,
				validationErrors: undefined,
				loading: false,
				checkout: {
					name: '',
					email: '',
					phone: '',
					billingAddress: {
						address_1: "",
						address_2: "",
						city: "",
						state_id: "",
						zipcode: ""
					},
					shippingAddress:{
						address_1: "",
						address_2: "",
						city: "",
						state_id: "",
						zipcode: ""
					},
					validatedAddress: undefined,
					shippingAddressDifferent: false,
					paymentMethod: 'credit_card'
				}
			}
		},

		watch: {
			selectedAddress(newValue) {
				if (newValue !== null) {
					this.$root.$emit('selectedAddressUpdated', newValue)
				}
			},

			'checkout.shippingAddressDifferent': {
				handler(newValue){
					localStorage.setItem('checkout.shippingAddressDifferent', newValue)
				}
			},

			'checkout.name': {
				handler(newValue){
					if (newValue != '') {
						localStorage.setItem('checkout.name', newValue)
					}
				}
			},

			'checkout.phone': {
				handler(newValue){
					if (newValue != '') {
						localStorage.setItem('checkout.phone', newValue)
					}
				}
			},

			'checkout.email': {
				handler(newValue){
					if (newValue != '') {
						localStorage.setItem('checkout.email', newValue)
					}
				}
			},
		},

		mounted() {

			let self = this;

			this.$root.$on('validationAddressCompleted', function(selectedAddress) {
				try {
					self.checkout.validatedAddress = selectedAddress
					self.submit($(self.$el).serialize() + '&' + $.param({
						'validatedAddress': self.checkout.validatedAddress
					}))
				} catch (e) {
				}
			})

			this.$root.$on('validationAddressClosed', function(selectedAddress) {
				//
			})

			this.validationErrors = this.errors

			if (this.validationErrors === undefined || this.validationErrors.errors === undefined) {
				this.validationErrors.errors = {}
			}

			this.$root.$on('cartContentLoaded', function(cartItems) {
				self.cartItems = cartItems;
			})

			if (this.name !== undefined) {
				this.checkout.name = this.name
			}

			if (this.email !== undefined) {
				this.checkout.email = this.email
			}

			if (this.phone !== undefined) {
				this.checkout.phone = this.phone
			}

			self.loadLocalStorage()
		},

		methods: {

			loadLocalStorage() {
				this.checkout.name = localStorage.getItem('checkout.name')

				this.checkout.email = localStorage.getItem('checkout.email')
				
				this.checkout.phone = localStorage.getItem('checkout.phone')

				this.checkout.shippingAddressDifferent = localStorage.getItem('checkout.shippingAddressDifferent') == 'true' 
					? 'true' 
					: 'false'

				this.checkout.name = this.checkout.name == null || this.checkout.name == 'null' 
					? '' 
					: this.checkout.name

				this.checkout.email = this.checkout.email == null || this.checkout.email == 'null' 
					? '' 
					: this.checkout.email

				this.checkout.phone = this.checkout.phone == null || this.checkout.phone == 'null' 
					? '' 
					: this.checkout.phone

				this.checkout.shippingAddressDifferent = this.checkout.shippingAddressDifferent == null || this.checkout.shippingAddressDifferent == 'null' 
					? '' 
					: this.checkout.shippingAddressDifferent
			},

			resetLocalStorage() {
				localStorage.setItem('checkout.name', '')
				localStorage.setItem('checkout.email', '')
				localStorage.setItem('checkout.phone', '')
				localStorage.setItem('checkout.shippingAddressDifferent', '')

				localStorage.setItem('shipping.zipcode', '')
				localStorage.setItem('shipping.address_1', '')
				localStorage.setItem('shipping.address_2', '')
				localStorage.setItem('shipping.city', '')
				localStorage.setItem('shipping.state', '')
				localStorage.setItem('shipping.state_id', '')

				localStorage.setItem('zipcode', '')
				localStorage.setItem('billing.address_1', '')
				localStorage.setItem('billing.address_2', '')
				localStorage.setItem('billing.city', '')
				localStorage.setItem('billing.state', '')
				localStorage.setItem('billing.state_id', '')

				location.reload()
			},

			storeCheckoutData() {
				localStorage.setItem('checkout', JSON.stringify(this.checkout))
			},

			updateShippingSelected(shipping) {
				if (typeof(shipping) === 'object' && shipping !== undefined) {
		    		this.shippingSelected = true
		    	} else {
		    		this.shippingSelected = false
		    	}
			},

			executeCheckout() {

				if (this.shippingSelected === false) {
					return;
				}

				let self = this

				this.loading = true
				$('#app').busyLoad('hide')
				$('#app').busyLoad('show')

				$.ajax({
					url: route('checkout.execute'),
					type: 'POST',
					dataType: 'json',
					data: $(self.$el).serialize() + '&' + $.param({
						'validatedAddress': self.checkout.validatedAddress
					}),
				})
				.done(function(response) {
					window.onbeforeunload = undefined
					location.href = response
				})
				.fail(function(response) {
					self.showPaymentOptions = false

					try {
						self.validationErrors = Object.assign({}, self.validationErrors, response.responseJSON)
						self.validationErrors.message = self.$set(self.validationErrors, 'message', response.responseJSON.message)
						self.validationErrors.errors = self.$set(self.validationErrors, 'errors', response.responseJSON.errors)
					} catch (e) {

					}

					setTimeout(function() {
						$([document.documentElement, document.body]).animate({
					        scrollTop: $('#error-container').offset().top
					    }, 20);
					}, 250)
				})
				.always(function(response) {
					self.loading = false
					$('#app').busyLoad('hide')
				});
				
			},

			submit(data) {

				let self = this;
				this.loading = true
				this.validationErrors = undefined

				$('#app').busyLoad('hide')
				$('#app').busyLoad('show')

				$.ajax({
					url: this.routeUrl,
					type: 'POST',
					dataType: 'json',
					data: data
				})
				.done(function(response) {

					self.showPaymentOptions = true

					window.onbeforeunload = function () {
				        return "You have attempted to leave this page. Are you sure?";
				    }

					setTimeout(function() {
						$([document.documentElement, document.body]).animate({
					        scrollTop: $('#shipping-container').offset().top - ($('.sticky-header').height() + 20)
					    }, 20);
					}, 300)

				})
				.fail(function(response) {
					self.validationErrors = Object.assign({}, self.validationErrors, response.responseJSON)
					self.validationErrors.message = self.$set(self.validationErrors, 'message', response.responseJSON.message)
					self.validationErrors.errors = self.$set(self.validationErrors, 'errors', response.responseJSON.errors)
				})
				.always(function() {
					self.loading = false;
					$('#app').busyLoad('hide')
				});
			},

			validate() {

				let self = this;
				this.loading = true
				this.validationErrors = undefined
				$('#app').busyLoad('show')

				$.ajax({
					url: this.validateUrl,
					type: 'POST',
					dataType: 'json',
					data: $(this.$el).serialize()
				})
				.done(function(response) {
					self.$root.$emit('validateAddress', response)
				})
				.fail(function(response) {
					try {
						self.validationErrors = Object.assign({}, self.validationErrors, response.responseJSON)
						self.validationErrors.message = self.$set(self.validationErrors, 'message', response.responseJSON.message)
						self.validationErrors.errors = self.$set(self.validationErrors, 'errors', response.responseJSON.errors)
					} catch (e) {

					}
				})
				.always(function() {
					self.loading = false;
					$('#app').busyLoad('hide')
				});
			},

			hasError(attribute){

				if (this.validationErrors === undefined) {
					return false;
				}

				if (this.validationErrors.length === 0) {
					return false
				}

				if (this.validationErrors.errors === undefined) {
					return false
				}

				return errorsBag.has(this.validationErrors.errors, attribute)
			},

			getError(attribute){
				return errorsBag.get(this.validationErrors.errors, attribute)
			},

			getValidationClass(attribute) {

				if (! this.hasErrors) {
					return '';
				}

				if (this.hasError(attribute)) {
					return 'is-invalid'
				}

				return 'is-valid'
			}
		},

		computed: {

			hasErrors() {
				return this.validationErrors !== undefined && this.validationErrors.message !== undefined;
			},

			errorsList() {
				if (this.validationErrors !== undefined && this.validationErrors.errors !== undefined) {
					return this.validationErrors.errors
				}
				return {}
			},

			canCheckout() {
				return this.cartItems.length > 0
			},

			canUsePaypal() {
				let subscriptionItemsCount = 0;
				let regularItemsCount = 0;

				this.cartItems.map(function(item) {
					try {
						if (item.attributes.is_subscription !== undefined && item.attributes.is_subscription !== null && item.attributes.is_subscription === true) {
							subscriptionItemsCount++
						} else {
							regularItemsCount++
						}
					} catch (e) {
						regularItemsCount++
					}
				})

				if (subscriptionItemsCount > 1) {
					return false;
				}

				if (regularItemsCount > 0 && subscriptionItemsCount > 0) {
					return false;
				}

				if (regularItemsCount === 0 && subscriptionItemsCount === 1) {
					return true;
				}

				return true;
			}
		}

	}
	
</script>

<style scoped>
	form {
		transition: filter 1s ease-in;
	}
</style>