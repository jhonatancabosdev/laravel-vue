<template>
    <div>
        <div class="shadow-lg offcanvas-collapse " :class="isOpen == true ? 'open' : ''">
            <div class="px-0 px-sm-1">
                <div class="modal-header d-flex flex-row border-bottom-0 text-right d-block w-100" @click.prevent>
                    <h1 v-if="addresses.length > 0" class="h4 mb-3">Choose a valid address</h1>
                    <button class="btn position-relative btn-danger text-white rounded-circle shadow-lg" style="padding: 0px 6.5px;" @click="close()" aria-label="Close cart">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="container-fluid mb-5">

                    <div class="row">

                        <div class="col-12">
                            <div class="list-group">
                                <div v-for="(address, key) in addresses" class="list-group-item list-group-item-action" :class="selectedAddress === address ? 'bg-secondary' : ''" v-if="address !== null">
                                    <label :for="'address-key-' + key" class="d-flex mb-0 p-0 flex-row w-100 align-items-center justify-content-between">
                                        <div class="d-flex flex-column flex-fill">
                                            <span v-if="address.addressLine !== null && address.addressLine !== ''">{{ address.addressLine }}, </span>
                                            <span v-if="address.addressLine2 !== null && address.addressLine2 !== ''">{{ address.addressLine2 }}, </span>
                                            <span v-if="address.addressLine3 !== null && address.addressLine3 !== ''">{{ address.addressLine3 }}, </span>
                                            <span v-if="address.buildingName !== null && address.buildingName !== ''">{{ address.buildingName }}, </span>
                                            <span v-if="address.politicalDivision1 !== null && address.politicalDivision1 !== ''">{{ address.politicalDivision1 }}, </span>
                                            <span v-if="address.politicalDivision2 !== null && address.politicalDivision2 !== ''">{{ address.politicalDivision2 }}, </span>
                                            <span>{{ address.postcodePrimaryLow }} - {{ address.postcodeExtendedLow }}, </span>
                                            <span v-if="address.region !== null">{{ address.region }}</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center px-2">
                                            <input type="radio" v-model="selectedAddress" :value="address" :id="'address-key-' + key">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-right mt-4">
                            <div class="btn-group shadow-sm">
                                <button v-if="selectedAddress !== null" class="btn btn-primary" @click.prevent="processCheckout()">CONTINUE CHECKOUT</button>
                                <button v-if="selectedAddress !== null" class="btn btn-secondary" @click.prevent="selectedAddress = null">
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
                addresses: [],
                selectedAddress: null
            }
        },

        mounted() {
            let self = this

            this.$root.$on('validateAddress', function(addresses) {
                if (addresses.length > 1) {
                    self.open();
                    self.addresses = addresses;
                } else if (addresses.length === 1) {
                    self.selectedAddress = addresses[0]
                    self.processCheckout();
                } else {
                    alert('Something went wrong, Please check your address.')
                    self.close();
                }
            })
        },

        methods: {

            processCheckout() {
                this.close();
                let self = this;
                setTimeout(function() {
                    self.$root.$emit('validationAddressCompleted', self.selectedAddress)
                }, 100)
            },

            open() {
                this.isOpen = true;
                $('body').css('overflow-y', 'hidden')
            },

            close() {
                this.$root.$emit('validationAddressClosed', this.selectedAddress)
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