<template>
	<div>
		<div class="d-flex flex-row flex-wrap align-items-center justify-content-between mb-4">
			<slot></slot> 
			<span style="font-size: 0.75rem;" class="text-nowrap">{{ minPrice | currency }} - {{ maxPrice | currency }}</span>
		</div>
		<div>
			<div class="range-slider position-relative d-flex flex-row w-100">
				<input type="range" multiple="multiple" @change="slider($event)" v-model.number="minPrice" :min="min" :max="max" step="10" class="multirange d-flex flex-fill w-100 original">
				<input type="range" multiple="multiple" @change="slider($event)" v-model.number="maxPrice" :min="min" :max="max" step="10" class="multirange d-flex flex-fill w-100 ghost" style="--low:0%; --high:100%; --range-color: #006633;">
			</div>
		</div>
	</div>
</template>

<script>

	import 'multirange/multirange.css'

	export default {
		props: {
			min: {
				type: Number,
				default: 0
			},

			max: {
				type: Number,
				default: 1000
			}
		},

		data() {
			return {
				minPrice: 0,
				maxPrice: 0
			}
		},

		mounted() {
			this.minPrice = Vue.prototype.getUrlParam('minPrice') ?? this.min;
			this.maxPrice = Vue.prototype.getUrlParam('maxPrice') ?? this.max;

			// window.multirange(this.$refs.range)

			this.$root.$on('set_price_range', function(response) {
				
				this.minPrice = response.minPrice;
				this.maxPrice = response.maxPrice;
			});

		},

		methods: {
			slider($event) {
		      	if (this.minPrice > this.maxPrice) {
		        	var tmp = this.maxPrice;
		        	this.maxPrice = this.minPrice;
		        	this.minPrice = tmp;
		      	}

	        	this.$emit('range-updated', {
	        		max: this.maxPrice,
					min: this.minPrice
	        	})
		    }
		}
	}
</script>
