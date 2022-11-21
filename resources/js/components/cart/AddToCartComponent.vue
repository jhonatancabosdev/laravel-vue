<template>
	<div :class="cssClass" @click="addToCart()">
		<slot></slot>
	</div>
</template>

<script>

import cart from '../../api/cart';

export default {
    
    props: [
        'productId',
        'cssClass',
        'quantity'
    ],
    
    methods: {
        addToCart() {
            cart.add({
                id: this.productId,
                quantity: this.quantity
            })
            .then(response => {
                this.$root.$emit('cartItemAdded', response.data);
            }).catch(error => {
                alert(error.response.data.message)
            })
        }
    }
};
</script>