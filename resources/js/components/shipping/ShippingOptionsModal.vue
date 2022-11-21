<template>
    <div>
        <div class="shadow-lg offcanvas-collapse " :class="isOpen == true ? 'open' : ''">
            <div class="px-0 px-sm-1">
                <div class="modal-header d-flex flex-row border-bottom-0 text-right d-block w-100" @click.prevent>
                    <h1 class="h4 mb-3">Choose a Shipping Option</h1>
                    <button class="btn position-relative btn-danger text-white rounded-circle shadow-lg" style="padding: 0px 6.5px;" @click="close()" aria-label="Close cart">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="container-fluid mb-5">

                    <div class="row">

                        <div class="col-12">
                            <div class="list-group">
                                <div v-for="(option, key) in shipping_options" class="list-group-item list-group-item-action" :class="selectedShipping === option ? 'bg-secondary' : ''" v-if="option !== null">
                                    <label :for="'address-key-' + key" class="d-flex mb-0 p-0 flex-row w-100 align-items-center justify-content-between">
                                        <div class="d-flex flex-column flex-fill">
                                            <span>{{option.label}}</span>
                                            <span v-if="option.cost > 0">
                                                <span>{{ ( option.cost ) | currency }}</span>
                                            </span>
                                            <span v-else-if="option.cost === 0" class="text-highlight">FREE SHIPPING</span>
                                            <span v-else>
                                                  &#45;&#45;
                                            </span>
                                            <span class="d-block small" v-if="option.shipping_label !== undefined && option.shipping_label !== null">
                                                {{ option.shipping_label }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center px-2">
                                            <input type="radio" v-model="selectedShipping" :value="option" :id="'address-key-' + key">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-right mt-4">
                            <div class="btn-group shadow-sm">
                                <button v-if="selectedShipping !== null" class="btn btn-primary" @click.prevent="processShipping()">CONTINUE CHECKOUT</button>
                                <button v-if="selectedShipping !== null" class="btn btn-secondary" @click.prevent="selectedShipping = null">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                <button class="btn btn-secondary" @click.prevent="close()">CLOSE</button>
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
        data() {
            return {
                isOpen: false,
                shipping_options: [],
                selectedShipping: null
            }
        },
        mounted() {
            let self = this
          console.log("mounted");
            this.$root.$on('shippingOptions', function(options) {
                let json_options = JSON.parse(options);
                if (json_options) {
                    self.open();
                    self.shipping_options = json_options;
                } else {
                    alert('Something went wrong, Please check your address.')
                    self.close();
                }
            })
        },

        methods: {

            processShipping() {
                this.close();
                this.$root.$emit('shippingOptionCompleted', this.selectedShipping)
            },

            open() {
                this.isOpen = true;
                $('body').css('overflow-y', 'hidden')
            },

            close() {
                this.$root.$emit('shippingOptionsClosed', this.selectedShipping)
                this.isOpen = false;
                $('body').css('overflow-y', 'auto')
            },
        }
    }

</script>

<style lang="scss" scoped>
    .offcanvas-collapse {
        z-index: 10000;
        position: fixed;
        top: 0;
        bottom: 0;
        right: 0;
        width: 33%;
        overflow-y: auto;
        background-color: #fff;
        transition: -webkit-transform .3s ease-in-out;
        transition: transform .3s ease-in-out;
        transition: transform .3s ease-in-out, -webkit-transform .3s ease-in-out;
        -webkit-transform: translateX(100%);
        transform: translateX(100%);

        // lg
        @media (max-width: 1286px) {
            width: 45%;
        }

        // lg
        @media (max-width: 1042) {
            width: 50%;
        }

        // sm
        @media (max-width: 768px) {
            width: 60%;
        }

        @media (max-width: 668px) {
            width: 68%;
        }

        @media (max-width: 568px) {
            width: 80%;
        }

        @media (max-width: 468px) {
            width: 100%;
        }

        &.open {
            -webkit-transform: translateX(0%);
            transform: translateX(0%);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
        }
    }
</style>