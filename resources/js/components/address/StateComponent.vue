<template>
	<div>

		<label class="text-dark font-weight-bold" for="state">
			<span class="text-danger font-weight-bolder">*</span>
			{{ label }}
		</label>

		<select 
			name="ship-state"
			ref="state"
			autocomplete="shipping region"
			:class="cssClass"
			width="100%"
			v-model="state"
			@change="$root.$emit('getStateCities', addressType, state);"
		>
			<option value="">-- Select --</option>
			<option v-for="state in states" :value="state.abv">{{ state.label }}</option>
		</select>

		<div class="d-none">
			<input type="hidden" :name="addressType + '_address_state_id'" v-model="state_id">
		</div>
		
	</div>
</template>

<script>

	export default {

		props: [
			'label',
			'cssClass',
			'addressType',
			'selectedStateId'
		],

		data(){
			return {
				state_id: null,
				states: [],
				state: null
			}
		},

		watch: {
			selectedStateId(newValue) {
				newValue = parseInt(newValue)

				if (isNaN(newValue)) {
					return false
				}

				if (this.states.length > 0) {
					let state = this.states.filter(state => {
						return state.value == this.selectedStateId
					})

					try {
						if (state[0] !== undefined) {
							this.state = state[0].abv
						}
					} catch (e) {

					}
				}
			},

			states(newValue, oldValue) {
				if (newValue.length > 0) {
					let stateId = null

					if (this.state_id == null) {
						stateId = localStorage.getItem(this.addressType + '.state_id')
						stateId = stateId === null || this.state_id == 'null' ? '' : this.state_id;
					}

					if (stateId !== undefined && stateId !== null) {
						this.state_id = stateId
					}

					if (this.state == null) {
						this.state = localStorage.getItem(this.addressType + '.state')
						this.state = this.state === null || this.state == 'null' ? '' : this.state;
					}

					if (this.state != null && (isNaN(parseInt(this.state_id)) || parseInt(this.state_id) === 0)) {
						
						let states = this.states.filter(state => {
							return state.abv == this.state
						})

						try {
							this.state_id = states[0] !== undefined ? states[0].value : null
							localStorage.setItem(this.addressType + '.state_id', this.state_id)
						} catch (e) {
							console.log(e)
						}
					}
				}
			},

			state(value, oldValue){
				try {

					if (value !== undefined && value !== null) {
						let states = this.states.filter(state => {
							return state.abv == value
						})
						try {
							this.state_id = states[0] !== undefined ? states[0].value : null
						} catch (e) {
							console.log(e)
						}
					} else {
						this.state_id = null
					}

					localStorage.setItem(this.addressType + '.state', value)
			   		localStorage.setItem(this.addressType + '.state_id', this.state_id)

				} catch (e) {

				}
			}
		},

		mounted() {
			this.getStates()
		},

		methods: {
			getStates(){

				let self = this

				$.ajax({
					url: '/api/country/1/state',
					type: 'GET',
					dataType: 'json'
				})
				.done(function(response) {
					try {

						self.states = response.data.map(function(state){
							return {
								label: state.name,
								abv: state.abv,
								value: state.id
							}
						})

						if (isNaN(parseInt(self.selectedStateId)) === false) {
					
							let state = self.states.filter(state => {
								return state.value == self.selectedStateId
							})

							try {
								if (state[0] !== undefined) {
									self.state = state[0].abv
								}
							} catch (e) {

							}
						}

					} catch (e) {	
					}
				})
				.fail(function(error) {
					self.states = []
				})
			}
		}
	}
	
</script>