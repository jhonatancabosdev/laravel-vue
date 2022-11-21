<template>
	<div>
		<span class="font-weight-bolder text-dark mb-1 d-block">Deliver to:</span>
		<div v-if="editMode === false" class="text-muted-3">
			<span v-if="isValidZipcode">{{ zipcode }} &nbsp; </span>
			<span v-else-if="! isValidZipcode && zipcode !== ''" class="text-danger">Invalid value <strong>({{ zipcode }})</strong> &nbsp; </span>
			<span 
				@click.prevent="editMode = true"
				class="cursor-pointer text-decoration-underline font-family-open-sans"
			>
				<span v-if="isValidZipcode">Edit zip code</span>
				<span v-else>Enter zip code</span>
			</span>
		</div>
		<div v-else>
			
			<div class="input-group rounded-lg flex-nowrap m-0">
	            <input
	                type="text"
	                placeholder="Your Zip code"
	                class="form-control border-right-0 max-w-130px"
	                v-model.number="zipcode"
	            >
	            <div class="input-group-append border border-highlight rounded-right">
	                <button @click.prevent="save()" type="button" class="btn-highlight border border-highlight border-left-0 font-weight-normal px-3">
	                    Save
	                </button>
	            </div>
	        </div>

		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				editMode: false,
				zipcode: ''
			}
		},

		mounted() {
			this.zipcode = localStorage.getItem('zipcode')
			
			if (this.zipcode === null) {
				this.zipcode = ''
			}
		},

		methods: {
			save() {
				this.editMode = false
				localStorage.setItem('zipcode', this.zipcode)
			},
		},

		computed: {
			isValidZipcode() {
				return /^\d{5}(-\d{4})?$/.test(this.zipcode);
			}
		}
	}
</script>