<template>
    <div>
        <a
            href="#"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            role="button"
            id="cart-dropdown"
            :class="cssClasses">

            <span class="d-lg-inline d-none">Shopping Cart</span><span class="d-lg-none d-inline"><i class="fa fa-shopping-cart" aria-label="Shopping Cart"></i></span> ({{ totalItems }})
            <br />
            <span class="font-weight-bold">{{ cartSubtotal | currency }}</span>
        </a>

        <div class="dropdown-menu cart-dropdown border-highlight dropdown-menu-right rounded" aria-labelledby="cart-dropdown">



            <div v-if="isEmpty" class="col-12 pt-3 pb-1" @click.stop.prevent>
                <div class="alert alert-danger mb-1">
                    Your cart is empty.
                </div>
            </div>

            <div v-else>

                <div class="col-12">
                                <div class="col-12 pt-3 pb-0" v-if="showSuccessAlert">
                <!-- <div class="alert alert-success mb-1">
                    New item added to your cart.
                </div> -->
            </div>
                    <div class="list-group py-3">
                        <div v-for="cartItem of availabeCartItems" class="list-group-item list-group-item-action p-0 pb-3 border-0 rounded">
                            <cart-item-component :item="cartItem"></cart-item-component>
                        </div>
                    </div>
                    
                    <div class="py-3" @click.stop.prevent>
                        <div class="text-right h5 mb-0">
                            Subtotal : <span class="font-weight-bold">{{ cartSubtotal | currency }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 text-right">
                    <a class="border-radius-0 btn btn-highlight text-white" :href="checkoutUrl">Checkout</a>
                </div>
                 <p class="px-3 mt-3 mb-0">
                    <a class="text-highlight" href="#">Continue shopping</a>
                </p>
            </div>

            <!-- <p class="px-3 mt-3 mb-0 text-olivedrab">
                <a class="text-highlight" href="#">Continue shopping</a>
            </p> -->

        </div>
    </div>
</template>

<script>

import cart from '../../api/cart';

export default {
    
    props: [
        'cssClasses',
        'cartIcon',
        'checkoutUrl'
    ],

    data() {
        return {
            loaded: false,
            showSuccessAlert: false,
            cartItems: [],
            totalWeight: 0,
        }
    },

    created(){
        cart.all().then((response) => {
            this.loaded = true;
            this.cartItems = response.data.cartItems;
            this.totalWeight = response.data.totalWeight;
        });
    },

    mounted(){

        this.$root.$on('cartItemAdded', cartItem => {

            this.showSuccessAlert = true

            // if item doesnt exist in cart
            // push it to cart items list
            // else return new car item 

            let itemExists = this.cartItems.filter(item => {
                return item.id === cartItem.id
            }).length !== 0

            if (itemExists) {

                this.cartItems = this.cartItems.map(item => {
                    if (item.id === cartItem.id) {
                        return cartItem
                    }
                    return item;
                })
            } else {
                this.cartItems.push(cartItem)
            }

            checkoutEcommerceEvent(this.availabeCartItems, 1);

            $('#cart-dropdown').trigger('click')

            setTimeout(this.hideSuccessAlert, 3000)
        })
    },

    methods: {
        hideSuccessAlert(){
            this.showSuccessAlert = false
        }
    },
    computed: {

        isEmpty(){
            return this.cartItems.filter(item => {
                return item.deleted === false
            }).length === 0
        },

        totalItems(){
            return this.cartItems.filter(item => {
                return item.deleted === false
            }).length
        },

        availabeCartItems(){
            if (this.cartItems.length === 0) {
                return []
            }
            return this.cartItems.filter(item => {
                return item.deleted === false
            })
        },

        cartWeight(){
            if (this.cartItems.length === 0) {
                return 0
            }

            return this.totalWeight;
        },

        cartSubtotal(){

            if (this.cartItems.length === 0) {
                return 0
            }

            return this.cartItems.map(item => {
                if (item.deleted === true) {
                    return 0;
                }

                return item.price * item.quantity
            })
            .reduce((accumulator, currentValue) => accumulator + currentValue)
        }
    }
};
</script>
<style lang="scss" scoped>
.cart-dropdown {
    border-radius: 0 !important;
}

.greenback {
    z-index: 1010;
}

.fa-layers {
    .fa-shopping-cart,
    .fa-layers-counter {
        z-index: 1011;
    }
}

</style>