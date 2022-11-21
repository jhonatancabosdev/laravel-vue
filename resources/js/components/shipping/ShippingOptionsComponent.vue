<template>
	
	<div>

		<h4 class="d-flex justify-content-between align-items-center mb-3">
			<span class="text-muted">{{ title }}</span>
		</h4>

		<ul class="list-group mb-3">

			<label v-for="shipping in shippingOptions" :for="'shipping-' + shipping.id" class="mb-0 list-group-item list-group-item-secondary list-group-item-action d-flex justify-content-between">

				<span class="text-capitalize flex-fill flex-grow-1">{{ shipping.name }}</span>


				<span class="flex-shrink-1 flex-fill text-right">					
					{{ shipping.base_cost | currency }}
				</span>

				<span class="flex-shrink-1 text-right ml-2">
					<input 
						:id="'shipping-' + shipping.id"
						name="shipping_id"
						v-model="shippingMethod"
						:value="shipping.id"
						type="radio"
						@change="updateShipping()"
					>
				</span>

			</label>

		</ul>
		
	</div>

</template>

<script>
	
	import request from '../../api/request';

	export default {

		props: [
			'title',
			'selected',
			'shippingOptions'
		],

		data() {
	        return {
	            shippingMethod : 0,
	        }
	    },

	    created(){
	    	this.shippingMethod = this.selected
	    },

	    mounted(){
	    	this.updateShipping()
	    },

	    methods: {
	    	updateShipping(){
	    		request.update('shipping', {
	    			shipping: this.shippingMethod
	    		})
	    		.then(response => {
		    		this.$root.$emit('shippingUpdated', this.selectedShipping);
	    		})
	    		.catch(response => {
	    			alert('Something went wrong!')
	    		})
	    	}
	    },

	    computed: {

	    	selectedShipping(){
	    		let selected = this.shippingOptions.filter(option => {
	    			return option.id === this.shippingMethod
	    		})

	    		if (selected.length) {
	    			return selected.pop();
	    		}

	    		return []
	    	}

	    }
	}

</script>