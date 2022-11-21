<template>
    <div :class="'navbar-collapse shadow-lg offcanvas-collapse bg-white ' + (display ? 'open' : '') " style="z-index: 101;">
        <div class="px-4 py-1">
            <div v-if="order.id !== undefined">
                <div class="modal-header border-bottom-0 px-1">
                    <h5 class="modal-title font-weight-bold">Order <kbd>#{{ order.id }}</kbd></h5>
                    <button type="button" class="close" @click="close()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="container-fluid px-0">
                    <div class="d-flex flex-row mb-5">
                        <div class="d-flex flex-column w-25 px-2">
                            <div class="p-3 bg-light rounded-lg shadow-sm h-100">
                                <h1 class="h5 font-weight-bold text-nowrap">Customer</h1>
                                <p class="mb-0">{{ order.name }}</p>
                                <p class="mb-0">
                                    <a :href="'tel:' + order.phone">{{ order.phone | phone }}</a>
                                </p>
                                <p class="mb-0">
                                    <a :href="'mailto:' + order.email">{{ order.email }}</a>
                                </p>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-fill px-2">
                            <div class="p-3 bg-light rounded-lg shadow-sm h-100">
                                <h1 class="h5 font-weight-bold text-nowrap">Billing Address</h1>
                                <div v-if="order.billingAddress !== null">
                                    <p class="mb-0 text-nowrap">{{ order.billingAddress.address_1 }}</p>
                                    <p v-if="order.billingAddress.address_2 !== null" class="mb-0 text-nowrap">{{ order.billingAddress.address_2 }}</p>
                                    <p class="text-nowrap">{{ order.billingAddress.city }}, {{ order.billingAddress.state.abv }} {{ order.billingAddress.zipcode }}</p>
                                </div>
                                <p v-else>
                                    ---
                                </p>
                            </div>
                        </div>
                        <div class="d-flex flex-column w-25 px-2">
                            <div class="p-3 bg-light rounded-lg shadow-sm h-100">
                                <h1 class="h5 font-weight-bold text-nowrap">Shipping Address</h1>
                                <div v-if="order.shippingAddress !== null">
                                    <p class="mb-0 text-nowrap">{{ order.shippingAddress.address_1 }}</p>
                                    <p v-if="order.shippingAddress.address_2 !== null" class="mb-0 text-nowrap">{{ order.shippingAddress.address_2 }}</p>
                                    <p class="text-nowrap">{{ order.shippingAddress.city }}, {{ order.shippingAddress.state.abv }} {{ order.shippingAddress.zipcode }}</p>
                                </div>
                                <p v-else>
                                    ---
                                </p>
                            </div>
                        </div>

                        <div class="d-flex flex-column px-2" v-if="order.confirmed === true">
                            <div class="p-3 alert-primary shadow-sm rounded-lg h-100">
                                <div class="mb-4">
                                    <div>
                                        <h1 class="h5 text-dark font-weight-bold">Payment</h1>
                                    </div>
                                    <div class="mt-1 text-dark text-nowrap">
                                        <strong>{{ order.card_type | ucfirst }}:</strong> <code>XXXX-XXXX-XXXX-<strong class="font-weight-bold">{{ order.lastCCDigits }}</strong></code>
                                        <div>
                                            Paid at: <code class="text-dark">{{ order.confirmed_at | usDate }}</code>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="order.carriers !== undefined && order.carriers !== null && order.carriers.length > 0">
                                    <div>
                                        <h1 class="h5 text-dark font-weight-bold text-nowrap">Shipping Method</h1>
                                    </div>
                                    <div class="mt-1 text-dark text-nowrap">
                                        <p class="mb-0" v-for="carrier in order.carriers">
                                            <kbd>{{ carrier.service_name }}</kbd>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column w-25 px-2">
                            <div class="d-flex flex-column p-3 bg-default shadow-sm text-white rounded-lg h-100">
                                <div class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between">
                                    <div class="py-0">
                                        <code class="text-white">Shipping:</code>
                                    </div>
                                    <div class="py-0">
                                        <code class="py-0 text-white font-weight-semi-bold">{{ order.shipping_cost | currency }}</code>
                                    </div>
                                </div>
                                <div class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between">
                                    <div class="py-0">
                                        <code class="text-white">Tax:</code>
                                    </div>
                                    <div class="py-0">
                                        <small><code class="text-light">({{ order.tax_rate | decimal }}%)</code></small> <code class="py-0 text-white font-weight-semi-bold">{{ order.tax | currency }}</code>
                                    </div>
                                </div>
                                <div class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between">
                                    <div class="py-0">
                                        <code class="text-white">Subtotal:</code>
                                    </div>
                                    <div class="py-0">
                                        <code class="py-0 text-white font-weight-semi-bold">{{ order.subtotal | currency }}</code>
                                    </div>
                                </div>
                                <div class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between">
                                    <div class="py-0 font-weight-semi-bold">
                                        <code class="text-white">Total:</code>
                                    </div>
                                    <div class="py-0 font-weight-semi-bold font-size-1-1rem">
                                        <code class="font-weight-semi-bold py-0 text-white">{{ order.total | currency }}</code>
                                    </div>
                                </div>    

                            </div>
                        </div>

                    </div>                    

                    <div v-if="order.products.length > 0" class="d-flex flex-column mb-5">
                        <div class="px-2">
                            <h1 class="h5 mb-3 font-weight-bold">Products</h1>
                            <table class="table table-sm table-striped table-hover mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>SKU</th>
                                        <th>Title</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in order.products" :style="product.status == 0 || product.deleted_at !== null ? 'opacity: 0.8; background: rgba(237, 39, 77, 0.15) !important;' : ''">
                                        <td>

                                            {{ product.id }}

                                            <small v-if="product.status == 0 || product.deleted_at !== null" class="text-danger">(Disabled)</small>
                                        </td>
                                        <td>
                                            {{ product.sku }}
                                        </td>
                                        <td>
                                            {{ product.name | truncate(200) }}
                                        </td>
                                        <td>
                                            {{ product.pivot.quantity }}
                                        </td>
                                        <td>
                                            {{ product.pivot.price | currency }}
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group">

                                                <a class="btn btn-sm btn-default d-flex align-items-center" target="_blank" :href="editProductRoute.replace('__id', product.id)"><i class="fas fa-edit"></i></a>

                                                <a 
                                                    v-if="product.status == 0 || product.deleted_at !== null"
                                                    class="btn btn-sm btn-pink view d-flex align-items-center" 
                                                    style="opacity: 0.2;" 
                                                    @click.prevent
                                                    href="#"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a 
                                                    v-else
                                                    class="btn btn-sm btn-pink view d-flex align-items-center" 
                                                    target="_blank" 
                                                    :href="viewProductRoute.replace('__id', product.id)"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-else class="d-flex flex-column mb-5">
                        <div class="px-1">
                            <div class="alert alert-danger">
                                No products under this order.
                            </div>
                        </div>
                    </div>

                    <div v-if="order.tracking_numbers.length > 0">
                        <order-tracking-table 
                            :tracking-numbers="order.tracking_numbers"
                            :storage-path="storagePath"
                            class="d-flex flex-column mb-5"
                        >
                        </order-tracking-table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {

        props: [
            'viewProductRoute', 'editProductRoute', 'storagePath'
        ],

        data() {
            return {
                display: false,
                order: {}
            }
        },

        mounted(){
            this.$root.$on('open_order_details', (order) => {
                this.order = order
                this.display = true
                $('body').css('overflow-y', 'hidden')
            })
        },

        methods: {

            close(){
                let self = this

                setTimeout(function() {
                    self.order = {}
                }, 250)

                $('body').css('overflow-y', 'auto')

                this.display = false
            }
        }
    }

</script>

<style scoped>
    .offcanvas-collapse {
        position: fixed;
        top: 0;
        bottom: 0;
        right: 0;
        width: 90%;
        overflow-y: auto;
        background-color: var(--gray-dark);
        transition: -webkit-transform 0.25s ease-in-out;
        transition: transform 0.25s ease-in-out;
        transition: transform 0.25s ease-in-out, -webkit-transform 0.25s ease-in-out;
        -webkit-transform: translateX(110%);
        transform: translateX(110%);
    }
    .offcanvas-collapse.open {
        z-index: 101;
        -webkit-transform: translateX(0%);
        transform: translateX(0%);
        transition: transform 0.5s ease-in-out;
    }
</style>