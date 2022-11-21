<template>
    <label for="coupon_code" class="d-flex flex-wrap justify-content-between mb-0" :class="cssClass">
        <h4 class="col-12 px-0 text-muted">Enter Coupon Code</h4>
        <input name="coupon_code" value="" type="hidden" aria-label="Enter Coupon Code">
        <input name="coupon_code_entry" id="coupon_code_entry" type="text" placeholder="Coupon Code" class="form-control col-7">
        <div class="col-5 pr-0">
            <button type="button" class="btn-highlight btn link link--hover__bg-brand-secondary col-12 py-2 fw-700 uppercase coupon-code-button" @click="applyCouponCode()">Apply</button>
        </div>
    </label>
</template>

<script>

import cart from '../../api/cart';

export default {
    
    props: [
        'cssClass',
    ],
    
    methods: {
        // method ajax into the cart details where coupon code is entered into the session data
        applyCouponCode() {
            cart.applyCouponCode(document.querySelector('#coupon_code_entry').value)
            .then(response => {
                if(response.data === 'code invalid') {
					alert('We\'re sorry, that coupon code is not currently valid.')
					return false;
				} else {
                    this.$root.$emit('couponCodeAdded', response.data);
                }
            }).catch(error => {
                alert(error.response.data.message)
            })
        }
    }
};

</script>