<template>
	<div>
		<!-- <div class="row" v-if="product.children"> -->
			<div v-if="product.quantity > 0" class="col-md-12 px-0">	
				<h4 class="mx-0 h5 mb-3">
					Quantity
					<span class="text-highlight pl-lg-5 pl-md-3 d-md-inline d-block pt-2 pt-md-0">{{ product.quantity }} In Stock, order today</span>
				</h4>
			</div>
			<div class="col-12 px-0 d-flex flex-md-row flex-column flex-wrap justify-content-between">
				<div class="form-group col-6 px-0 ">
					<div class="quantity--number d-flex flex-row">
						<a v-on:click="reduceQuantity" :class="quantity > 1 ? 'text-white' : 'text-secondary'" class="border-radius-0 input--quantity--change input--quantity--change--minus text-center px-3 font-weight-light bg-black align-items-baseline"><span class="sr-only">reduce quantity</span></a>
						<input type="number" value="1" id="quantity" class="border-black border-thin input--quantity d-inline" min="1" v-model="quantity">
						<a v-on:click="raiseQuantity" :class="quantity < product.quantity ? 'text-white' : 'text-secondary'" class="border-radius-0 input--quantity--change input--quantity--change--plus px-3 font-weight-light bg-black"><span class="sr-only">increase quantity</span></a>
					</div>
				</div>

				<div class="form-group col-6 pl-md-3 pr-md-2 px-0 pt-3 pt-md-0">
					<div v-if="product.quantity > 0">
						<add-to-cart-component :quantity="quantity" :product-id="product.id">
							<button
								type="button"
								class="btn btn-highlight btn--add-to-cart text-white btn-block rounded-0 text-uppercase"
							>
								Add To Cart
							</button>
						</add-to-cart-component>
					</div>
					<div v-if="product.quantity == 0">
						<p class="text-danger">
							Out of Stock
						</p>
					</div>
				</div>
			</div>

		<!-- </div>
		<div v-else>
			Please pick an option above
		</div> -->
	</div>
</template>

<script>
	export default{
		props: [
			'product'
		],
		methods: {
			reduceQuantity() {
				if(this.quantity > 1) {
					this.quantity -= 1;
				}
			},
			raiseQuantity() {
				if(Number.parseInt(this.quantity) < this.product.quantity) { // Never go above amount in inventory
					this.quantity = Number.parseInt(this.quantity) + 1;
				}
			}
		},
		data(){
			return {
				quantity: 1
			}
		},
	}
</script>
<style lang="scss" scoped>
    .input--quantity {
		border-width: 1px;
        &::-webkit-inner-spin-button, 
        &::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        width: 40px;
        text-align: center;
        &--change {
            font-weight: 100;
            height: 40px;
			line-height: 40px;
			padding: 0 !important;
			width: 40px;
			text-align: center;
            &:hover {
                text-decoration: none;
            }
            &::before {
                font-size: 2rem;
            }
            &--minus {
                border-top-left-radius: 2px;
                border-bottom-left-radius: 2px;
                &::before {
                    content: '-';
				}
				line-height: 40px !important;
            }
            &--plus {
				border-top-right-radius: 2px;
                border-bottom-right-radius: 2px;
				&::before {
					content: '+';
				}
			}
        }
	}
	.btn--add-to-cart {
		font-size: 1.2rem;
		min-height: 40px;
	}
</style>