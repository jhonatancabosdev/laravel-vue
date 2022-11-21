<template>
	<div>
		<label for="state" class="text-dark font-size-0-9rem mb-2">
			<span class="text-danger font-weight-bolder">*</span>
			{{ label }}
		</label>
		<input type="text" :name="addressType + '_address_city'" :class="cssClass" v-model="city" required>
		<slot></slot>
	</div>
</template>

<script>
	export default {

		props: [
			'label',
			'cssClass',
			'addressType',
			'selectedCity'
		],

		data(){
			return {
				city: ''
			}
		},

		watch: {
			city(val) {
				localStorage.setItem(this.addressType + '.city', val)
			},

			selectedCity(val) {
				this.city = val
			},
		},

		created(){
			if (this.selectedCity !== '' && this.selectedCity !== undefined) {
				this.city = this.selectedCity;
			}

			if (this.city == '') {
				this.city = localStorage.getItem(this.addressType + '.city')
				this.city = this.city === null || this.city == 'null' ? '' : this.city;
			}
		}
	}
</script>