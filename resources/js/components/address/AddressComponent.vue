<template>
    <div>
        <div class="mb-3">
            <div v-if="! onCheckout">

                <label class="text-dark font-size-0-9rem mb-2">
                    <span class="text-danger font-weight-bolder">*</span>
                    Address Type
                </label>

                <select
                    name="address_type"
                    class="form-control rounded-0 border bg-secondary-1 border-muted-6 mb-3 col-6"
                    placeholder=""
                    v-model="type"
                >
                    <option value="billing" v-bind:selected="type == 'billing'">Billing</option>
                    <option value="shipping" v-bind:selected="type == 'shipping'">Shipping</option>
                </select>
            </div>

            <input
                v-else
                type="hidden"
                name="address_type"
                v-model="type"
            />

            <label class="text-dark font-size-0-9rem mb-2">
                <span class="text-danger font-weight-bolder">*</span>
                Address
            </label>

            <input 
                type="text" 
                :class="'form-control rounded-0 border bg-secondary-1 border-muted-6 ' + getValidationClass(type + '_address_1')"
                placeholder="1234 Main St" 
                v-model="address_1" 
                required="" 
                :name="type + '_address_1'"
            >

            <div v-if="hasError(type + '_address_1')" class="invalid-feedback">
                {{ getError(type + '_address_1') }}
            </div>

            <div  v-else class="mt-2 small">
                <i class="fas fa-info-circle"></i> We do not ship to PO Boxes
            </div>

        </div>
        <div class="mb-3">

            <label class="text-dark font-size-0-9rem mb-2">Address 2</label>

            <input
                type="text" 
                :class="'form-control rounded-0 border bg-secondary-1 border-muted-6 ' + getValidationClass(type + '_address_2')"
                placeholder="Apartment or suite" 
                v-model="address_2"
                :name="type + '_address_2'"
            >

        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <city-component 
                    label="City" 
                    :selected-city="city" 
                    :address-type="type" 
                    :cssClass="'form-control rounded-0 border bg-secondary-1 border-muted-6 ' + getValidationClass(type + '_address_city')"
                >
                    <div v-if="hasError(type + '_address_city')" class="invalid-feedback d-block">
                        {{ getError(type + '_address_city') }}
                    </div>
                </city-component>
            </div>
            <div class="col-md-5 mb-3">

                <state-component 
                    label="State" 
                    :selected-state-id="state_id" 
                    :address-type="type" 
                    :cssClass="'form-control rounded-0 border bg-secondary-1 border-muted-6 ' + getValidationClass(type + '_address_state_id')"
                >
                </state-component>

                <div v-if="hasError(type + '_address_state_id')" class="invalid-feedback d-block">
                    {{ getError(type + '_address_state_id') }}
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label class="text-dark font-weight-bold" for="zip">
                    <span class="text-danger font-weight-bolder">*</span>
                    Zip
                </label>
                <input 
                    type="number" 
                    :class="'form-control rounded-0 border bg-secondary-1 border-muted-6 ' + getValidationClass(type + '_address_zipcode')"
                    placeholder="Your zipcode" 
                    required
                    v-model="zipcode" 
                    maxlength="5" 
                    :name="type + '_address_zipcode'"
                >
                <div v-if="hasError(type + '_address_zipcode')" class="invalid-feedback d-block">
                    {{ getError(type + '_address_zipcode') }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import errorsBag from '../../helpers/errors';

    export default {

        props: [
            'addressType', 'errors', 'address', 'onCheckout'
        ],

        data(){
            return {
                type: this.addressType,
                address_1: '' ,
                address_2: '' ,
                zipcode: '' ,
                state_id: '' ,
                state: '',
                city: '',
            }
        },

        watch: {
            type (val, oldVal) {
                if (val && val !== oldVal) {
                    this.type = val;
                }
            },

            address_1(val) {
                localStorage.setItem(this.addressType + '.address_1', val)
            },

            address_2(val) {
                localStorage.setItem(this.addressType + '.address_2', val)
            },

            state (val, oldVal) {
                if (val) {
                    this.state_id = val.value;
                } else {
                    this.state_id = null;
                }
            },

            zipcode (val, oldVal){
                if (val !== null && val.length === 5) {

                    this.$root.$emit('cartTaxUpdated', {
                        zipcode: val,
                        addressType: this.addressType,
                    })

                    let zipcode = localStorage.getItem('zipcode')

                    if (zipcode != val && this.addressType == 'billing') {
                        localStorage.setItem('zipcode', val)
                    }

                    if (val != '' && this.addressType == 'shipping') {
                        localStorage.setItem('shipping.zipcode', val)
                    }
                }
            }

        },

        mounted(){

            let self = this;

            if (this.address !== undefined) {
                this.address_1 = this.address.address_1;
                this.address_2 = this.address.address_2;
                this.zipcode = this.address.zipcode;
                this.city = this.address.city
                this.state_id = parseInt(this.address.state_id)
            }

            let storageKey = this.addressType == 'billing' ? '' : this.addressType + '.';
            let zipcode = localStorage.getItem(storageKey + 'zipcode')

            if (! isNaN(parseInt(zipcode)) && parseInt(zipcode)) {
                this.zipcode = zipcode
            }

            if (this.address_1 == '') {
                this.address_1 = localStorage.getItem(this.addressType + '.address_1')
                this.address_1 = this.address_1 === null || this.address_1 == 'null' ? '' : this.address_1;
            }

            if (this.address_2 == '') {
                this.address_2 = localStorage.getItem(this.addressType + '.address_2')
                this.address_2 = this.address_2 === null || this.address_2 == 'null' ? '' : this.address_2;
            }

            this.$root.$on('selectedAddressUpdated', function(newAddress) {
                self.address_1 = newAddress.address_1;
                self.address_2 = newAddress.address_2;
                self.zipcode = newAddress.zipcode;
                self.city = newAddress.city
                self.state_id = parseInt(newAddress.state_id)
            })
        },

        methods: {

            getValidationClass(attribute) {
                if (! this.hasErrors) {
                    return '';
                }

                if (this.hasError(attribute)) {
                    return 'is-invalid'
                }

                return 'is-valid'
            },

            hasError(attribute){
                return errorsBag.has(this.errors, attribute)
            },

            getError(attribute){
                return errorsBag.get(this.errors, attribute)
            }
        },

        computed: {
            hasErrors() {

                if (this.errors !== undefined && Object.keys(this.errors).length === 0 && this.errors.constructor === Object) {
                    return false;
                }

                return this.errors !== undefined && this.errors.length !== false;
            }
        }
    };
</script>