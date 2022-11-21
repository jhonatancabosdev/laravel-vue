<template>
    <div class="pl-3 pr-1 py-2 rounded border" v-if="item.deleted !== true" @click.stop>
        <div class="d-flex flex-row flex-wrap flex-md-nowrap justify-content-center align-items-center">

            <img :src="item.attributes.main_image" class="cart-item__img img-fluid max-h-100px max-w-100px ml-0 mr-0" :alt="item.name" @click.stop.prevent>

            <div class="align-self-center flex-column pb-2 px-2 mr-auto">
                <a class="mb-2" :href="route('product.show', item.attributes.slug !== undefined && item.attributes.slug !== null ? item.attributes.slug : item.attributes.id).url()">
                    {{ item.name | truncate(30) }}
                </a>
                 </div>
            <div class="align-self-center flex-column py-2 px-2">
                <div class="input--quantity__wrapper text-right">
                    <input class="border-radius-0 form-control d-inline-block" type="number" v-model.number="quantity" min="1" @click.stop.prevent>
                </div>
            </div>

            <div class="py-2 px-2" @click.stop.prevent>
                {{ item.price | currency }}
            </div>

            <button class="btn" @click.stop="deleteItem()">
                <i class="fa fa-trash-alt"></i>
            </button>

        </div>
    </div>
</template>

<script>

import cart from '../../api/cart';

export default {
    props: ['item'],
    methods: {
        deleteItem(){

            if (!confirm('Are you sure you want to remove ' + this.item.name + ' from your cart?')) {
                return true;
            } 

            cart.delete(this.item.id).then(response => {
                this.item.deleted = true
                this.$root.$emit('cartItemDeleted', this.item)
            }).catch(error => {
                alert('Please refresh this page. and try again')
            })
        },
    },
    watch: {
        quantity(newValue, oldValue){
            cart.update(this.item.id, {
                quantity: newValue
            }).then(response => {
                if(typeof response.data === 'object' && 'maxQuantity' in response.data) {
                    this.quantity = response.data.maxQuantity;
                    alert(response.data.message);
                } else {
                    this.$root.$emit('cartItemUpdated', this.item)
                }
            }).catch(error => {
                console.log(error);
            })
        }  
    },
    computed: {

        quantity: {
            // getter
            get: function () {
                return this.item.quantity
            },
            // setter
            set: function (newValue) {
                if (newValue > 0) {
                    this.item.quantity = newValue
                } else {
                    this.item.quantity = 1
                }
            }
        }
    }
};
</script>