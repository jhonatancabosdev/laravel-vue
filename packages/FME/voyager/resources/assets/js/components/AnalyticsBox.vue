<template>
	<div class="card border-0 bg-dark rounded-lg shadow-none position-relative d-flex flex-row">
        <div class="d-flex flex-fill p-2 text-white position-relative align-items-center justify-content-between">
            <div class="text-uppercase pl-2">
                <h1 class="h6 small mb-0 font-weight-light text-nowrap">
					<slot></slot>
                </h1>
                <h2 class="mb-0 py-0 h5 mt-1">
            		<code v-if="type === 'money'" class="text-white font-weight-light text-nowrap">{{ today | currency }}</code>
            		<code v-else class="text-white font-weight-light text-nowrap">{{ today }}</code>
            	</h2>
            </div>
        </div>
        <div class="bg-dark-2 p-0 rounded-lg d-flex flex-row align-items-center justify-content-center" style="height: 60px; width: 60px; margin: auto; text-align: center;">

        	<div class="d-flex flex-column">

        		<span v-if="progress === 0">
					<span class="text-secondary-2 py-0">{{ progress }}%</span>        			
        		</span>
	        	<span v-else>

		        	<i v-if="progress > 0" class="text-success fas fa-arrow-up"></i>
		        	<i v-else class="text-danger fas fa-arrow-down"></i>

	        		<span class="text-secondary-2 py-0">{{ progress }}%</span>
	        		
	        	</span>

        	</div>

        </div>
    </div>
</template>
<script>
	export default {

		props: {
			today: {
				type: Number,
				default: 0,
			},
			yesterday: {
				type: Number,
				default: 0,
			},
			type: {
				type: String,
				default: 'money',
			},
		},

		mounted() {
			if (this.type != 'money' && this.type !== 'number') {
				console.log('Only money and number types are allowed')
			}
		},

		computed: {

			progress() {
				try {

					if (this.today === 0) {
						return 0;
					}

					let total = ((this.today - this.yesterday) / this.today) * 100;
					return Math.round(total, 2)
				} catch (e) {
					return 0;
				}
			}
		}
	}
</script>