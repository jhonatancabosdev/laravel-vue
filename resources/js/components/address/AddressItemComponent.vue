<template>

	<label :for="'address-' + address.id" class="list-group-item list-group-item-secondary  list-group-item-action d-flex justify-content-between align-items-center">
		<span class="text-capitalize flex-fill flex-grow-1">

			<span v-if="address.address_1 != ''">
				{{ address.address_1 }} <br>
			</span>

			<span v-if="address.address_2 != ''">
				{{ address.address_2 }} <br>
			</span>

			<span v-if="city != ''">
				{{ city }},
			</span>

			<span v-if="state != ''">
				{{ state.abv }} 
			</span>

			<span v-if="address.zipcode != ''">
				{{ address.zipcode }}
			</span>

		</span> 
		<span class="flex-shrink-1 text-right ml-2">
			<input 
				:checked="parseInt(selectedAddressId) === address.id" 
				v-model="addressId" :id="'address-' + address.id" 
				:name="addressType + '_address'" 
				type="radio" 
				:value="address.id"
				@change="updateTaxAndAddress()"
			>
		</span>
	</label>

	
</template>

<script>

	import request from '../../api/request';

	export default {

		props: [
			'address', 'addressType', 'selectedAddressId', 'city', 'state'
		],

		data(){
			return {
				addressId: 0
			}
		},

		created(){
			if (parseInt(this.selectedAddressId) === this.address.id) {
				this.addressId = this.address.id;
			}
		},

		methods: {

			updateTaxAndAddress()
			{
				request.update(
					route('customer.address.update'), 
					this.jsonRequestParam
				)
				.then(response => {
					this.$root.$emit('cartTaxUpdated', {
		   				zipcode: this.address.zipcode,
		   				addressType: this.addressType,
		   			})
				})
	    		.catch(response => {
	    			alert('Something went wrong!')
	    		})
			}

		},

		computed: {

			jsonRequestParam(){
				if (this.addressType == 'shipping') {
					return {
						shipping_address: this.addressId
					}
				}

				return {
					billing_address: this.addressId
				}
			}

		}

	}
</script>