<template>

	<div class="modal fade" tabindex="-1" data-keyboard="true" role="dialog" aria-hidden="true" id="product-modal-component" data-backdrop="static">
		<div class="modal-dialog" role="document">
			<div class="modal-content container">
				<div class="modal-header border-0">
					<h5 class="modal-title">
						{{ mainTitle }}
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<div class="px-3 row">

						<div class="col-md-6 mx-auto mt-1 mb-4 w-100 text-center">
							<img :src="currentProduct.main_image" class="d-block h-auto mx-auto img-fluid" >
						</div>

						<div class="col-md-6 mb-5">
							<div v-if="currentProduct.compare_at_price > 0" class="position-absolute" style="right: 0; margin-bottom: -18px; z-index: 1;">
								<span class="badge badge-orange monsterrat text-uppercase text-white border-radius-0 py-2 px-3">Sale</span>
							</div>
							<h2 class="h2 mt-5 mb-2">
								{{ currentProduct.name }}
							</h2>
							<p class="h5" v-if="currentProduct.brand">{{ currentProduct.brand.name }}</p>
							<p class="mb-3 font-weight-light h4">
								<span class="text-decoration-line-through">{{ currentProduct.original_price | currency }}</span>
								<span class="text-highlight h3">{{ currentProduct.price | currency }}</span>
							</p>
							
							<table class="table">
								<!-- <tr>
									<th class="py-2">
										Brand
									</th>
									<td class="py-2 text-right" >
										<span v-if="currentProduct.brand">{{ currentProduct.brand.name }}</span>
									</td>
								</tr> -->
								<tr>
									<th class="py-2">Product Code</th>
									<td class="py-2 text-right">{{ currentProduct.sku }}</td>
								</tr>
								<tr>
									<th class="py-2">Availability</th>
									<td class="py-2 text-right">
										<span class="text-success" v-if="currentProduct.quantity > 0">{{ currentProduct.quantity }} in stock</span>

										<span class="text-danger" v-if="currentProduct.quantity <= 0">Out of Stock</span>
									</td>
								</tr>
							</table>

							<div class="form-group" v-if="products.length > 0">
								<label for="product-options" class="h5 font-weight-normal">Options</label>
								<select v-model="selectedProduct" class="form-control" id="product-options">
									<option :value="product" selected>{{ product.name }}</option>
									<option v-for="productItem in products" :value="productItem">{{ productItem.name }}</option>
								</select>
							</div>

							<div v-if="currentProduct.quantity > 0" class="row">
								<div class="form-group col-md-4">
									<div class="quantity--number d-flex flex-row">
										<a href="#" v-on:click="reduceQuantity" class="input--quantity--change input--quantity--change--minus px-2 font-weight-light bg-highlight text-white"><span class="sr-only">reduce quantity</span></a>
										<input type="number" value="1" id="quantity" class="input--quantity d-inline" min="1" v-model="quantity">
										<a href="#" v-on:click="raiseQuantity" class="input--quantity--change input--quantity--change--plus px-2 font-weight-light bg-highlight text-white"><span class="sr-only">increase quantity</span></a>
									</div>
								</div>

								<add-to-cart-component 
									css-class="col-md-5" 
									:quantity="quantity" 
									:product-id="currentProduct.id"
								>
									<button
										type="button"
										class="btn btn-highlight rounded-0 text-white px-2 px-sm-2 px-md-3 w-100 flex-fill"
									>
										Add to Cart
									</button>
								</add-to-cart-component>
							</div>

						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

</template>
<script>
	export default {
		methods: {
			reduceQuantity() {
				if(this.quantity > 1) {
					this.quantity -= 1;
				}
			},
			raiseQuantity() {
				if(this.quantity < this.selectedProduct.quantity) { // Never go above amount in inventory
					this.quantity += 1;
				}
			}
		},
		data(){
			return {
				quantity: 1,
				product: [],
				selectedProduct: [],
				products: []
			}
		},

		mounted(){

			this.$root.$on('showProductChildren', (product, products) => {
				$('#product-modal-component').modal('show')
				this.product = product
				this.products = products
				this.selectedProduct = this.product
			});

			this.$root.$on('cartItemAdded', () => {
				$('#product-modal-component').modal('hide')
				this.product = []
				this.products = []
				this.selectedProduct = []
			});
		},

		computed:{
			currentProduct(){
				if (this.selectedProduct === undefined || this.selectedProduct.id === this.product.id || this.selectedProduct.length === 0) {
					return this.product
				}

				return this.selectedProduct;
			},

			mainTitle(){
				return (this.product.main_name != '' && this.product.main_name != null) ? this.product.main_name : this.currentProduct.title;
			}
		}
	}
</script>
<style lang="scss" scoped>
	.modal-dialog {
		max-width: none;		
	}
    .input--quantity {
        &::-webkit-inner-spin-button, 
        &::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        width: 40px;
        text-align: center;
        &--change {
            font-weight: 100;
            height: 34px;
            line-height: 34px;
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
</style>