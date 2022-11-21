<template>
    <div>
        <div class="row">
            <div class="col-12">
                <div class="mb-1">
                    <div class="d-flex align-items-start flex-row jq-filters-container">

                        <div class="d-flex flex-column py-3 mr-4">
                            <label class="font-weight-semi-bold mb-1">Search</label>
                            <div class="d-flex flex-column align-items-center justify-content-center p-2 border bg-light rounded-lg h-100 min-w-335px" style="min-height: 130px;">
                                <div class="input-group mt-2 mb-0">
                                    <input type="text" class="form-control" name="s" v-model="searchText">
                                    <div class="input-group-append">
                                        <button class="btn btn-default" type="button" @click.prevent="search()">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="mt-1 flex-fill d-flex w-100 align-self-start">Search by ID, Name, Email, Phone number</small>
                            </div>
                        </div>

                        <div class="d-flex flex-column py-3 mr-4">

                            <label class="font-weight-semi-bold mb-1">Filter options</label>

                            <div class="d-flex flex-row p-2 border bg-light rounded-lg h-100" style="min-height: 130px;">

                                <div class="d-flex flex-column border-right pr-3">
                                    <strong class="font-weight-semi-bold">By Order Status</strong>
                                    <label class="mb-0" v-for="status in orderStatus">
                                        <input type="checkbox" v-model="orderStatusId" :value="status.id" @change="getResults(page)">
                                        {{ status.name | ucfirst }}
                                    </label>
                                </div>

                                <div class="d-flex flex-column border-right ml-3 pr-3">
                                    <strong class="font-weight-semi-bold">By order attributes</strong>
                                    <label class="mb-0">
                                        <input type="checkbox" :disabled="showAll" v-model="confirmed" :value="true" @change="getResults(page)">
                                        Confirmed
                                    </label>
                                    <label class="mb-0">
                                        <input type="checkbox" :disabled="showAll" v-model="refunded" :value="true" @change="getResults(page)">
                                        Refunded
                                    </label>
                                    <label class="mb-0">
                                        <input type="checkbox" :disabled="showAll" v-model="reported" :value="true" @change="getResults(page)">
                                        Reported to API
                                    </label>
                                    <label class="mb-0">
                                        <input type="checkbox" v-model="showAll" :value="true" @change="getResults(page)">
                                        Show All
                                    </label>
                                </div>

                                <div class="d-flex flex-column ml-3 mr-3">
                                    <strong class="font-weight-semi-bold">By Source</strong>
                                    <label class="mb-0">
                                        <input type="checkbox" v-model="source" value="adwords" @change="getResults(page)">
                                        Google Adwords
                                    </label>

                                    <label class="mb-0">
                                        <input type="checkbox" v-model="source" value="direct" @change="getResults(page)">
                                        Direct
                                    </label>
                                </div>

                            </div>
                        </div>

                        <div class="d-flex flex-column py-3 mr-4">

                            <label class="font-weight-semi-bold mb-1">Sort options</label>

                            <div class="d-flex flex-row p-2 border bg-light rounded-lg h-100 align-items-center" style="min-height: 130px;">

                                <div class="d-flex flex-column mr-3">
                                    <label class="mb-0">
                                        <span class="d-inline-block" style="width: 14px;">
                                            <span v-show="orderBy === 'updated_at'">
                                                <i v-show="sortOrder === 'desc'" class="fas fa-sort-up"></i>
                                                <i v-show="sortOrder === 'asc'" class="fas fa-sort-down"></i>
                                            </span>
                                        </span>
                                        <a href="#" class="text-decoration-none text-dark" @click="sortBy('updated_at')">Last update</a>
                                    </label>
                                    <label class="mb-0">
                                        <span class="d-inline-block" style="width: 14px;">
                                            <span v-show="orderBy === 'created_at'">
                                                <i v-show="sortOrder === 'desc'" class="fas fa-sort-up"></i>
                                                <i v-show="sortOrder === 'asc'" class="fas fa-sort-down"></i>
                                            </span>
                                        </span>
                                        <a href="#" class="text-decoration-none text-dark" @click="sortBy('created_at')">Created date</a>
                                    </label>
                                    <label class="mb-0">
                                        <span class="d-inline-block" style="width: 14px;">
                                            <span v-show="orderBy === 'mailed_at'">
                                                <i v-show="sortOrder === 'desc'" class="fas fa-sort-up"></i>
                                                <i v-show="sortOrder === 'asc'" class="fas fa-sort-down"></i>
                                            </span>
                                        </span>
                                        <a href="#" class="text-decoration-none text-dark" @click="sortBy('mailed_at')">Mailed Date</a>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column py-3 mr-4">

                            <label class="font-weight-semi-bold mb-1">Other</label>

                            <div class="d-flex flex-row align-items-center justify-content-center p-2 border bg-light rounded-lg" style="min-height: 130px;">
                                <div class="btn-group">
                                    <a :href="exportOrderRoute" class="btn btn-default" target="_blank">Export All</a>
                                    <a :href="exportOrderRoute + '?' + ajaxQueryString" class="btn btn-default" target="_blank">Export Filtered</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="text-right mb-1">
                    <code class="text-dark">Total orders <kbd v-if="laravelResponse.total !== undefined">{{ laravelResponse.total }}</kbd></code>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped" style="min-height: 200px;">
                        <thead class="thead-dark">

                            <th class="text-nowrap" @click="sortBy('id')">
                                ID
                                <span v-if="orderBy === 'id'">
                                    <i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
                                    <i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
                                </span>
                            </th>

                            <th class="text-nowrap" @click="sortBy('name')">
                                Customer Details
                                <span v-if="orderBy === 'name'">
                                    <i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
                                    <i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
                                </span>
                            </th>

                            <th class="text-nowrap" @click="sortBy('billingAddress')">
                                Billing Address
                                <span v-if="orderBy === 'billingAddress'">
                                    <i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
                                    <i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
                                </span>
                            </th>

                            <th class="text-nowrap" @click="sortBy('shippingAddress')">
                                Shipping Address
                                <span v-if="orderBy === 'shippingAddress'">
                                    <i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
                                    <i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
                                </span>
                            </th>

                            <th class="text-nowrap" @click="sortBy('total')">
                                Details
                                <span v-if="orderBy === 'total'">
                                    <i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
                                    <i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
                                </span>
                            </th>

                            <th class="text-nowrap" @click="sortBy('order_status_id')">
                                Status
                                <span v-if="orderBy === 'order_status_id'">
                                    <i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
                                    <i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
                                </span>
                            </th>

                            <th class="text-nowrap">
                                Carrier
                            </th>

                            <th class="text-nowrap" @click="sortBy('created_at')">
                                Created Date
                                <span v-if="orderBy === 'created_at'">
                                    <i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
                                    <i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
                                </span>
                            </th>

                            <th class="text-nowrap">
                                    
                            </th>

                            <th class="text-nowrap">
                                
                            </th>

                        </thead>

                        <tbody>
                            <tr v-for="order in orders" v-if="order.deleted_at === null">
                                <td><kbd>#{{ order.id }}</kbd></td>
                                <td>
                                    <p class="mb-0">{{ order.name }}</p>
                                    <p class="mb-0">
                                        <a :href="'tel:' + order.phone">{{ order.phone | phone }}</a>
                                    </p>
                                    <p class="mb-0">
                                        <a :href="'mailto:' + order.email">{{ order.email }}</a>
                                    </p>
                                </td>
                                <td>
                                    <div v-if="order.billingAddress !== null">
                                        <p class="mb-0">{{ order.billingAddress.address_1 }}</p>
                                        <p v-if="order.billingAddress.address_2 !== null" class="mb-0">{{ order.billingAddress.address_2 }}</p>
                                        <p class="mb-0">{{ order.billingAddress.city }}, {{ order.billingAddress.state.abv }} {{ order.billingAddress.zipcode }}</p>
                                    </div>
                                    <p v-else class="mb-0">
                                        ---
                                    </p>
                                </td>
                                <td>
                                    <div v-if="order.shippingAddress !== null">
                                        <p class="mb-0">{{ order.shippingAddress.address_1 }}</p>
                                        <p v-if="order.shippingAddress.address_2 !== null" class="mb-0">{{ order.shippingAddress.address_2 }}</p>
                                        <p class="mb-0">{{ order.shippingAddress.city }}, {{ order.shippingAddress.state.abv }} {{ order.shippingAddress.zipcode }}</p>
                                    </div>
                                    <p v-else class="mb-0">
                                        ---
                                    </p>
                                </td>
                                <td>
                                    <div class="d-flex py-1 px-2 bg-white rounedd-lg flex-column" style="max-width: 165px;">
                                        <div class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between border-bottom border-secondary">
                                            <div class="py-0">
                                                Shipping:
                                            </div>
                                            <div class="py-0">
                                                <code class="py-0 text-dark font-weight-semi-bold">{{ order.shipping_cost | currency }}</code>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between border-bottom border-secondary">
                                            <div class="py-0">
                                                Tax:
                                            </div>
                                            <div class="py-0">
                                                <small><code class="text-muted">({{ order.tax_rate | decimal }}%)</code></small> <code class="py-0 text-dark font-weight-semi-bold">{{ order.tax | currency }}</code>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between border-bottom border-secondary">
                                            <div class="py-0">
                                                Subtotal:
                                            </div>
                                            <div class="py-0">
                                                <code class="py-0 text-dark font-weight-semi-bold">{{ order.subtotal | currency }}</code>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-row w-100 flex-fill align-items-center justify-content-between">
                                            <div class="py-0 font-weight-semi-bold">
                                                Total:
                                            </div>
                                            <div class="py-0 font-weight-semi-bold font-size-1-1rem">
                                                <code class="font-weight-semi-bold py-0 text-dark">{{ order.total | currency }}</code>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-nowrap d-editable-none">
                                    <div v-if="editable.editableColumn == 'order_status_id' && editable.id == order.id">
                                        <div class="input-group flex-nowrap">
                                            <select v-model="order.order_status_id" class="form-control form-control-sm">
                                                <option v-for="status in orderStatus" :value="status.id">
                                                    {{ status.name | ucfirst }}
                                                </option>
                                            </select>
                                            <div class="input-group-append">
                                                <button @click="saveEditable(order, 'order_status_id', $event)" class="btn btn-sm btn-pink" type="button">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button @click="closeEditable(order, 'order_status_id')" class="btn btn-sm btn-default" type="button">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="d-flex flex-row align-items-center justify-content-between">
                                        <div class="mr-1">
                                            <kbd :style="order.confirmed === false ? 'opacity: 0.5; text-decoration: line-through;' : ''">
                                                <span v-for="status in orderStatus" v-if="order.order_status_id === status.id">
                                                    {{ status.name | ucfirst }}
                                                </span>
                                            </kbd>
                                        </div>
                                        <button @click="editRow(order, 'order_status_id')" class="btn btn-sm p-0 text-muted text-hover-pink jq-btn-editable" v-if="editable.editableColumn == ''">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                    </div>
                                </td>

                                <td>
                                    <div v-if="order.carriers !== undefined && order.carriers !== null && order.carriers.length > 0">
                                        <p class="mb-0" v-for="carrier in order.carriers">
                                            <kbd>{{ carrier.service_name }}</kbd>
                                        </p>
                                    </div>
                                    <div v-else>
                                        --
                                    </div>
                                </td>
                                <td>
                                    {{ order.created_at | usDate }}
                                </td>
                                <td>
                                    <div v-if="order.confirmed === true" class="btn-group shadow rounded-lg">
                                        <button :disabled="order.refunded === true" class="btn btn-sm btn-secondary" @click="viewTracking(order)">
                                            <small>Tracking</small>
                                        </button>
                                        <button :disabled="order.refunded === true" class="btn btn-sm btn-secondary" @click="mailInvoice(order, $event)">
                                            <small>Mail Invoice</small>
                                        </button>
                                        <button :disabled="order.refunded === true" class="btn btn-sm btn-danger" @click="refundOrder(order, $event)">
                                            <small>Refund</small>
                                        </button>
                                    </div>
                                    <div v-else>
                                        <span class="text-danger small">Abandoned / Not Completed</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group shadow rounded-lg" style="min-height: 28.5px;">
                                        <button class="btn btn-sm btn-default d-flex align-items-center" @click="deleteOrder(order)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <a class="btn btn-sm btn-default d-flex align-items-center" :href="editOrderRoute.replace('__id', order.id)">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-pink view d-flex align-items-center" @click="viewOrder(order)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        <pagination :limit="4" :data="laravelResponse" align="right" @pagination-change-page="getResults"></pagination>
    </div>
</template>

<script>
    export default {

        props: [
            'startPage', 
            'orderStatus',

            'dataRoute', 
            'editOrderRoute',
            'updateColumnRoute',
            'deleteOrderRoute',
            'notifyOrderRoute',
            'refundOrderRoute',
            'exportOrderRoute'
        ],

        data(){
            return {
                loading: false,
                searchText: '',
                orderBy: 'id',
                sortOrder: 'desc',
                orderStatusId: [],
                laravelResponse: {},
                orders: [],
                showAll: false,
                confirmed: true,
                refunded: false,
                reported: false,
                page: 1,
                source: [],

                editable: {
                    id: 0,
                    editableColumn: '',
                    order: null,
                }
            }
        },

        mounted() {

            this.$root.$on('refresh_orders', () => {
                this.getResults(this.page)
            })

            try {

                let self = this

                window.onpopstate = function(event) {
                    try {
                        let newPage = event.state.path.split('?page=').pop()
                        self.getResults(newPage);
                    } catch (e) {

                    }
                };

                if (localStorage !== undefined) {

                    if (localStorage.ordersSortOrder !== undefined) {
                        this.sortOrder = localStorage.ordersSortOrder;
                    }

                    if (localStorage.ordersOrderBy !== undefined) {
                        this.orderBy = localStorage.ordersOrderBy;
                    }

                    if (localStorage.ordersShowAll !== undefined) {
                        this.showAll = localStorage.ordersShowAll == 'false' ? false : true;
                    }

                    if (localStorage.ordersConfirmed !== undefined) {
                        this.confirmed = localStorage.ordersConfirmed == 'false' ? false : true;
                    }

                    if (localStorage.ordersRefunded !== undefined) {
                        this.refunded = localStorage.ordersRefunded == 'false' ? false : true;
                    }

                    if (localStorage.ordersReported !== undefined) {
                        this.reported = localStorage.ordersReported == 'false' ? false : true;
                    }
                }

                this.getResults(this.startPage);

            } catch (e) {

            }

        },

        methods: {

            sortBy(sortColumn) {

                if (sortColumn == this.orderBy) {
                    this.sortOrder = this.sortOrder == 'asc' ? 'desc' : 'asc';
                } else {
                    this.orderBy = sortColumn;
                }

                try {
                    localStorage.ordersSortOrder = this.sortOrder;
                    localStorage.ordersOrderBy = this.orderBy;
                } catch (e) {
        
                }

                this.getResults(this.page);
            },

            saveLocalStorage() {
                try {
                    localStorage.ordersShowAll = this.showAll;
                    localStorage.ordersConfirmed = this.confirmed;
                    localStorage.ordersRefunded = this.refunded;
                    localStorage.ordersReported = this.reported;
                } catch (e) {
                    console.log(e)
                }
            },

            editRow(row, editableColumn) {
                this.editable.id = row.id
                this.editable.order =  Object.assign({}, row)
                this.editable.editableColumn = editableColumn
            },

            closeEditable(row, editableColumn) {
                console.log(this.editable.order[editableColumn])
                this.$set(row, editableColumn, this.editable.order[editableColumn])
                this.$set(this.editable, 'id', 0)
                this.$set(this.editable, 'order', null)
                this.$set(this.editable, 'editableColumn', '')
            },

            search() {
                this.page = 1; 
                insertUrlParam('page', 1); 
                this.getResults(this.page);
            },

            saveEditable(row, editableColumn, $event) {
                
                if (confirm('Please confirm this action') === false) {
                    return false;
                }

                $($event.srcElement).parents('td').busyLoad('show')

                let self = this

                $.ajax({
                    url: this.updateColumnRoute.replace('__id', row.id).replace('__column', editableColumn),
                    type: 'POST',
                    data: {
                        value: row[editableColumn],
                        options: [row[editableColumn]],
                        values: [row[editableColumn]]
                    },
                })
                .done(function() {
                    self.$set(self.editable.order, editableColumn, row[editableColumn])
                    self.closeEditable(row, editableColumn)
                })
                .fail(function(response) {
                    toastr.error(response.responseJSON.message)
                })
                .always(function() {
                    $($event.srcElement).parents('td').busyLoad('hide')
                });
            },

            deleteOrder(order) {

                let self = this
                let el = $(this.$el)
                let status = confirm('Are you sure you want to delete order ID ' + order.id + '?');

                if (status == false) {
                    return false;
                }

                el.busyLoad('show')

                $.ajax({
                    url: this.deleteOrderRoute.replace('__id', order.id),
                    type: 'POST',
                    data: {_method: 'delete'},
                })
                .done(function(response) {
                    if (response['alert-type'] == 'error') {
                        toastr.error(response.message)
                    } else {
                        toastr.success(response.message)
                        self.$set(order, 'deleted_at', order.created_at)
                    }
                })
                .fail(function(response) {
                    try {
                        toastr.error(response.responseJSON.message)
                    } catch (e) {
                        toastr.error('Something went wrong')
                    }
                })
                .always(function() {
                    el.busyLoad('hide')
                });
            },

            viewOrder(order) {
                this.$root.$emit('open_order_details', order);
            },

            viewTracking(order) {
                this.$root.$emit('open_order_tracking', order);
            },

            mailInvoice(order, $event) {

                let self = this
                let el = $($event.target).parents('.btn-group')  

                let status = confirm('Please confirm, send invoice to order ID ' + order.id + ', ' + order.email);

                if (status == false) {
                    return false;
                }

                el.busyLoad('show')

                $.ajax({
                    url: this.notifyOrderRoute,
                    type: 'POST',
                    data: {
                        id: order.id
                    },
                })
                .done(function() {
                    toastr.success('Invoice mailed successfully')
                })
                .fail(function(response) {
                    try {
                        toastr.error(response.responseJSON.message)
                    } catch (e) {
                        toastr.error('Something went wrong')
                    }
                })
                .always(function() {
                    el.busyLoad('hide')
                });
            },

            refundOrder(order, $event) {

                let self = this
                let el = $($event.target).parents('.btn-group')

                let status = confirm('Please confirm, Refund order ID ' + order.id);

                if (status == false) {
                    return false;
                }

                el.busyLoad('show')

                $.ajax({
                    url: this.refundOrderRoute.replace('__id', order.id),
                    type: 'POST',
                    data: {
                        id: order.id
                    },
                })
                .done(function(response) {
                    self.$set(order, 'refunded', response.refunded)
                    self.$set(order, 'order_status_id', response.order_status_id)
                    self.$set(order, 'order_status', response.order_status)
                    toastr.success('refund processed successfully')
                })
                .fail(function(response) {
                    try {
                        toastr.error(response.responseJSON.message)
                    } catch (e) {
                        toastr.error('Something went wrong')
                    }
                })
                .always(function() {
                    el.busyLoad('hide')
                });
            },

            getResults(newPage = 1) {

                let self = this

                $(self.$el).busyLoad('show')

                if (self.page > 1 || newPage > 1) {
                    insertUrlParam('page', newPage)
                }

                $.ajax({
                    url: this.dataRoute + '?page=' + newPage,
                    type: 'GET',
                    data: this.ajaxData,
                })
                .done(function(response) {
                    self.orders = response.data
                    self.laravelResponse = response
                    self.page = newPage
                    self.saveLocalStorage()
                })
                .always(function() {

                    $(self.$el).busyLoad('hide')

                    setTimeout(function() {
                        $('[data-toggle="popover"]').popover()
                    }, 450)
                });
            },

        },

        computed: {
            ajaxData() {
                return {
                    s: this.searchText,
                    sort_order: this.sortOrder,
                    order_by: this.orderBy,
                    order_status_id: this.orderStatusId,
                    confirmed: this.confirmed,
                    showAll: this.showAll,
                    refunded: this.refunded,
                    reported: this.reported,
                    source: this.source,
                }
            },

            ajaxQueryString() {
                return $.param(this.ajaxData)
            }
        }
    }
</script>