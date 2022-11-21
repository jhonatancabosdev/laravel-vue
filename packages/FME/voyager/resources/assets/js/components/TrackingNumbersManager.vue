<template>
    <div :class="'navbar-collapse shadow-lg offcanvas-collapse bg-white ' + (display ? 'open' : '') " style="z-index: 101;">
        <div class="px-4 py-1">
            <div v-if="order.id !== undefined">
                <div class="modal-header border-bottom-0 px-1">
                    <h5 class="modal-title font-weight-bold">Tracking management for order <kbd>#{{ order.id }}</kbd></h5>
                    <button type="button" class="close" @click="close()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="container-fluid px-0">

                    <div class="px-2 mb-5 jq-add-tracking-container">
                        <div class="p-4 bg-secondary">
                            <h1 class="h5 mb-3 font-weight-bold">Create new tracking number</h1>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <div class="form-group flex-fill mr-3 mb-3">
                                    <label>Carrier Name</label>
                                    <select v-model="tracking.carrier_name" class="form-control">
                                        <option value="">-- Select --</option>
                                        <option :value="carrier.label" v-for="carrier in carriers">{{ carrier.label }}</option>
                                        <option value="Other">Other (Enter manually)</option>
                                    </select>
                                </div>
                                <div class="form-group flex-fill mr-3 mb-3" v-if="tracking.carrier_name === 'Other'">
                                    <label>Carrier</label>
                                    <input type="text" class="form-control" v-model="tracking.label">
                                </div>
                                <div class="form-group flex-fill">
                                    <label>Number</label>
                                    <input type="text" class="form-control" v-model="tracking.number">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end w-100">
                                <div class="btn-group">
                                    <button @click="clearForm()" class="btn btn-sm btn-default">Clear form</button>
                                    <button @click="addAndNotify()" class="btn btn-sm btn-pink">Add & send notification</button>
                                </div>
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
                    <div v-else>
                        <div class="px-1">
                            <div class="px-4 py-3 alert alert-warning">
                                There is no tracking number for this order yet.
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

        props: [
            'storagePath', 'createTrackingRoute', 'carriers'
        ],

        data() {
            return {
                display: false,
                tracking: {
                    carrier_name: "UPS Ground",
                    number: '',
                    label: '',
                },
                order: {}
            }
        },

        mounted(){
            this.$root.$on('open_order_tracking', (order) => {
                this.order = order
                this.display = true
                $('body').css('overflow-y', 'hidden')
            })
        },

        methods: {

            clearForm(){
                if (confirm('Please confirm this action')) {
                    this.tracking = {
                        carrier_name: "UPS Ground",
                        number: '',
                        label: '',
                    }
                }
            },

            addAndNotify() {

                let self = this

                if (this.tracking.carrier_name == '' || this.tracking.number == '') {
                    alert('Carrier name and tracking number are required!')
                    return false;
                }

                if (this.tracking.label !== '') {
                    this.tracking.carrier_name = this.tracking.label;
                    this.tracking.label = '';
                }

                $('.jq-add-tracking-container').busyLoad('show')

                $.ajax({
                    url: this.createTrackingRoute,
                    type: 'POST',
                    data: {
                        id: this.order.id,
                        tracking_number: this.tracking
                    },
                })
                .done(function(response) {

                    self.tracking = {
                        carrier_name: 'UPS Ground',
                        number: '',
                        label: '',
                    }

                    self.order.tracking_numbers.push(response)
                    toastr.success('Tracking number added to order id #' + self.order.id)
                    self.$root.$emit('refresh_orders');
                })
                .fail(function(response) {
                    toastr.error(response.responseJSON.message)
                })
                .always(function() {
                    $('.jq-add-tracking-container').busyLoad('hide')
                });
                
            },

            close(){
                let self = this

                this.tracking = {
                    carrier_name: 'UPS Ground',
                    number: '',
                    label: '',
                }

                setTimeout(function() {
                    self.order = {}
                }, 350)

                $('body').css('overflow-y', 'auto')

                this.display = false
            }
        },
    }

</script>

<style scoped>
    .offcanvas-collapse {
        position: fixed;
        top: 0;
        bottom: 0;
        right: 0;
        width: 70%;
        overflow-y: auto;
        background-color: var(--gray-dark);
        transition: -webkit-transform 0.35s ease-in-out;
        transition: transform 0.35s ease-in-out;
        transition: transform 0.35s ease-in-out, -webkit-transform 0.35s ease-in-out;
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