<template>
	<div>
		<div v-if="hasSubmittedForm === true" class="text-highlight mb-2">
			Thank you!	
		</div>
		<div v-else>
			<p class="text-uppercase mb-0 font-weight-normal pb-1">&nbsp;Stay in touch</p>
			<div class="form-group mb-2">
	            <input type="text" name="name" placeholder="Name" class="form-control border-radius-0" v-model="subscriber.name">
	        </div>
	        <div class="form-group mb-2">
	            <input type="email" name="email" placeholder="Email" class="form-control border-radius-0" v-model="subscriber.email">
	        </div>
	        <div class="form-group mb-2">
	            <button @click.stop="subscribe()" class="btn bg-black btn-block text-white border-radius-0">Submit</button>
	        </div>
	        <div class="alert alert-danger mb-2" v-if="hasErrors">
	        	<ul class="list-unstyled mb-0">
		        	
	        	</ul>
	        </div>
		</div>
		
	</div>
</template>

<script>
	import request from '../../api/request';

	export default {
		data(){
			return {
				subscriber: {
					name: '',
					email: ''
				},
				
				hasSubmittedForm: false,

				errors: []
			}
		},

		methods: {
			subscribe(){

				if ( !this.hasValidData) {
					alert('Name and email fields are required');
					return false;
				}

				request.add(route('subscribe.store'), this.subscriber)
				.then(response => {
					this.errors = []
					this.hasSubmittedForm = true
				}).catch(error => {
					this.errors = error.response.data.errors
					alert(error.response.data.message)
				})
			}
		},

		computed: {
			hasErrors(){
	            return this.errors.length !== 0
	        },

	        hasValidData(){
	        	var emailRegix = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	        	return this.subscriber.name.split('').length > 2 
	        			&& this.subscriber.email.split('').length > 3 
	        			&& emailRegix.test(this.subscriber.email)
	        }
		}
	}
</script>