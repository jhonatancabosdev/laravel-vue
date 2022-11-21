<template>
	<div>

		<div class="row">
            <div class="col-12">
                <div class="mb-1">
                    <div class="d-flex align-items-start flex-row jq-filters-container">
                    	
                        <div class="d-flex flex-column py-3 mr-4">
                            <label class="font-weight-semi-bold mb-1">Search Products</label>
                            <div class="d-flex flex-column align-items-center justify-content-center p-2 border bg-light rounded-lg min-h-84px min-w-335px">
                                <form v-on:submit.prevent="page = 1; insertUrlParam('page', 1); getResults(page)" class="input-group mt-2 mb-0">
                                    <input type="text" class="form-control" name="s" v-model="searchText">
                                    <div class="input-group-append">
                                        <button class="btn btn-default" type="button" @keyup.enter="page = 1; insertUrlParam('page', 1); getResults(page)" @click.prevent="page = 1; insertUrlParam('page', 1); getResults(page)">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                                <small class="mt-1 flex-fill d-flex w-100 align-self-start">Search by ID, Name, Model, SKU</small>
                            </div>
                        </div>

                        <div class="d-flex flex-column py-3 mr-4">

                            <label class="font-weight-semi-bold mb-1">Filter options</label>

                            <div class="d-flex flex-row p-2 border bg-light rounded-lg min-h-84px">

                                <div class="d-flex flex-column mr-3">
                                    <label class="mb-0">
                                        <input type="checkbox" name="status[]" value="2" v-model="status" @change="getResults(page)">
                                        Draft
                                    </label>
                                    <label class="mb-0">
                                        <input type="checkbox" name="status[]" value="1" v-model="status" @change="getResults(page)">
                                        Enabled
                                    </label>
                                    <label class="mb-0">
                                        <input type="checkbox" name="status[]" value="0" v-model="status" @change="getResults(page)">
                                        Disabled
                                    </label>
                                </div>

                                <div class="d-flex flex-column mr-3">
                                    <label class="mb-0">
                                        <input type="checkbox" name="family[]" value="standalone" v-model="family" @change="getResults(page)">
                                        Standalone
                                    </label>
                                    <label class="mb-0">
                                        <input type="checkbox" name="family[]" value="parent" v-model="family" @change="getResults(page)">
                                        Parent
                                    </label>
                                    <label class="mb-0">
                                        <input type="checkbox" name="family[]" value="child" v-model="family" @change="getResults(page)">
                                        Child
                                    </label>
                                </div>

                                <div class="d-flex flex-column mr-3">
                                    <label class="mb-0">
                                        <input type="checkbox" name="images[]" value="yes" v-model="hasImages" @change="getResults(page)">
                                        Has images
                                    </label>
                                    <label class="mb-0">
                                        <input type="checkbox" name="images[]" value="no" v-model="hasImages" @change="getResults(page)">
                                        No images
                                    </label>
                                </div>

                                <div class="d-flex flex-column">
                                    <label class="mb-0">
                                        <input type="checkbox" name="availability_id[]" value="1" v-model="availability_id" @change="getResults(page)">
                                        In stock
                                    </label>
                                    <label class="mb-0">
                                        <input type="checkbox" name="availability_id[]" value="0" v-model="availability_id" @change="getResults(page)">
                                        Not in stock
                                    </label>
                                </div>

                            </div>
                        </div>

                        <div class="d-flex flex-column py-3 mr-4">

                            <label class="font-weight-semi-bold mb-1">Sort options</label>

                            <div class="d-flex flex-row p-2 border bg-light rounded-lg min-h-84px">

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
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column py-3">
                            <label class="font-weight-semi-bold mb-1">Mass update (<span class="jq-selected-items-count">0</span> checked items)</label>
                            <div class="d-flex flex-row align-items-center justify-content-center p-2 border bg-light rounded-lg min-h-84px">
                                <div class="btn-group">
                                    <button type="button" data-toggle="modal" data-target="#price-mass-update-modal" class="btn btn-default">Price</button>
                                    <button type="button" data-toggle="modal" data-target="#availability-mass-update-modal" class="btn btn-default">Availability</button>
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
        			<code class="text-dark">Total products <kbd v-if="laravelResponse.total !== undefined">{{ laravelResponse.total }}</kbd></code>
        		</div>
        		<div class="table-responsive">
        			<table class="table table-sm table-hover table-striped" style="min-height: 200px;">
						<thead class="thead-dark">
							<tr>
								<th>
									<input type="checkbox" class="select_all">
								</th>
								<th class="text-nowrap" @click="sortBy('id')">
									ID
									<span v-if="orderBy === 'id'">
										<i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
										<i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
									</span>
								</th>
								<th class="text-nowrap" @click="sortBy('sku')">
									Sku
									<span v-if="orderBy === 'sku'">
										<i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
										<i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
									</span>
								</th>
								<th class="text-nowrap" @click="sortBy('product_belongsto_brand_relationship')">
									Brand
									<span v-if="orderBy === 'product_belongsto_brand_relationship'">
										<i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
										<i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
									</span>
								</th>
								<th class="text-nowrap" @click="sortBy('mpn')">
									Model/MPN
									<span v-if="orderBy === 'mpn'">
										<i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
										<i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
									</span>
								</th>
								<th class="text-nowrap" @click="sortBy('name')">
									Title
									<span v-if="orderBy === 'name'">
										<i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
										<i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
									</span>
								</th>
								<th class="text-nowrap px-2" @click="sortBy('price')">
									Price
									<span v-if="orderBy === 'price'">
										<i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
										<i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
									</span>
								</th>
								<th class="text-nowrap" @click="sortBy('product_hasmany_product_image_relationship')">
									Image
									<span v-if="orderBy === 'product_hasmany_product_image_relationship'">
										<i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
										<i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
									</span>
								</th>
								<th class="text-nowrap" @click="sortBy('product_belongstomany_category_relationship')">
									Main category
									<span v-if="orderBy === 'product_belongstomany_category_relationship'">
										<i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
										<i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
									</span>
								</th>
								<th class="text-nowrap" @click="sortBy('children')">
									Family
									<span v-if="orderBy === 'children'">
										<i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
										<i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
									</span>
								</th>
								<th class="text-nowrap" @click="sortBy('status')">
									Status
									<span v-if="orderBy === 'status'">
										<i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
										<i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
									</span>
								</th>
								<th class="text-nowrap" @click="sortBy('availability_id')">
									Availability
									<span v-if="orderBy === 'availability_id'">
										<i v-if="sortOrder === 'desc'" class="fas fa-sort-up"></i>
										<i v-if="sortOrder === 'asc'" class="fas fa-sort-down"></i>
									</span>
								</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr :class="editable.editableColumn != '' && editable.id == product.id ? 'table-pink' : ''" v-for="product in products" v-if="product.deleted !== true">

								<td><input type="checkbox" name="row_id" :id="'checkbox_' + product.id" :value="product.id"></td>

								<td class="text-nowrap">{{ product.id }}</td>

								<td class="text-nowrap d-editable-none">
									<div v-if="editable.editableColumn == 'sku' && editable.id == product.id">
										<div class="input-group flex-nowrap">
										  <input type="text" class="form-control form-control-sm min-w-120px" v-model="product.sku">
										  <div class="input-group-append">
										    <button @click="saveEditable(product, 'sku', $event)" class="btn btn-sm btn-pink" type="button">Save</button>
										    <button @click="closeEditable(product, 'sku')" class="btn btn-sm btn-default" type="button">Cancel</button>
										  </div>
										</div>
									</div>
									<div v-else class="d-flex flex-row align-items-center justify-content-between">
										<div class="mr-1">
											<a
				                                @click.prevent
				                                href="#"
				                                role="button"
				                                class="text-default text-nowrap"
				                                data-html="true"
				                                data-container="body" 
				                                data-toggle="popover" 
				                                data-placement="top"
				                                data-trigger="hover"
				                                :data-content='product.sku'
				                            >
												{{ product.sku | truncate(15) }}
				                            </a>
										</div>
			                            <button @click="editRow(product, 'sku')" class="btn btn-sm p-0 text-muted text-hover-pink jq-btn-editable" v-if="editable.editableColumn == ''">
			                            	<i class="fas fa-pencil-alt"></i>
			                            </button>
									</div>
								</td>

								<td class="text-nowrap d-editable-none">
									<div v-if="editable.editableColumn == 'brand' && editable.id == product.id">
										<div class="input-group flex-nowrap">

										  	<v-select 
										  		taggable 
										  		class="bg-white text-nowrap rounded-lg min-w-190px" 
										  		v-model="product.brand" 
										  		label="name" 
										  		:loading="loading"
										  		:options="brands" 
										  		@input="updateProductBrand(product)"
										  		@option:created="brandCreated($event)"
										  	>	
									  		</v-select>

											<div class="input-group-append">
												<button @click="saveEditable(product, 'brand_id', $event)" class="btn btn-sm btn-pink" type="button">Save</button>
												<button @click="closeEditable(product, 'brand')" class="btn btn-sm btn-default" type="button">Cancel</button>
											</div>
										</div>
									</div>
									<div v-else class="d-flex flex-row align-items-center justify-content-between">
										<div class="mr-1">
											<a
				                                @click.prevent
				                                href="#"
				                                role="button"
				                                class="text-default text-nowrap"
				                                data-html="true"
				                                data-container="body" 
				                                data-toggle="popover" 
				                                data-placement="top"
				                                data-trigger="hover"
				                                :data-content="product.brand !== null && product.brand !== undefined ? product.brand.name : '--'"
				                            >
												{{ (product.brand !== null && product.brand !== undefined ? product.brand.name : '--') | truncate(15) }}
				                            </a>
										</div>
			                            <button @click="editRow(product, 'brand')" class="btn btn-sm p-0 text-muted text-hover-pink jq-btn-editable" v-if="editable.editableColumn == ''">
			                            	<i class="fas fa-pencil-alt"></i>
			                            </button>
									</div>
								</td>

								<td class="text-nowrap d-editable-none">
									<div v-if="editable.editableColumn == 'mpn' && editable.id == product.id">
										<div class="input-group flex-nowrap">
										  <input type="text" class="form-control form-control-sm min-w-120px" v-model="product.mpn">
										  <div class="input-group-append">
										    <button @click="saveEditable(product, 'mpn', $event)" class="btn btn-sm btn-pink" type="button">Save</button>
										    <button @click="closeEditable(product, 'mpn')" class="btn btn-sm btn-default" type="button">Cancel</button>
										  </div>
										</div>
									</div>
									<div v-else class="d-flex flex-row align-items-center justify-content-between">
										<div class="mr-1">
											<a
				                                @click.prevent
				                                href="#"
				                                role="button"
				                                class="text-default text-nowrap"
				                                data-html="true"
				                                data-container="body" 
				                                data-toggle="popover" 
				                                data-placement="top"
				                                data-trigger="hover"
				                                :data-content='product.mpn'
				                            >
												{{ product.mpn | truncate(15) }}
				                            </a>
										</div>
			                            <button @click="editRow(product, 'mpn')" class="btn btn-sm p-0 text-muted text-hover-pink jq-btn-editable" v-if="editable.editableColumn == ''">
			                            	<i class="fas fa-pencil-alt"></i>
			                            </button>
									</div>
								</td>

								<td class="text-nowrap d-editable-none">
									<div v-if="editable.editableColumn == 'name' && editable.id == product.id">
										<div class="input-group">
										  <input type="text" class="form-control form-control-sm min-w-120px" v-model="product.name">
										  <div class="input-group-append">
										    <button @click="saveEditable(product, 'name', $event)" class="btn btn-sm btn-pink" type="button">Save</button>
										    <button @click="closeEditable(product, 'name')" class="btn btn-sm btn-default" type="button">Cancel</button>
										  </div>
										</div>
									</div>
									<div v-else class="d-flex flex-row align-items-center justify-content-between">
										<a
			                                @click.prevent
			                                href="#"
			                                role="button"
			                                class="text-default text-nowrap mr-1 d-flex"
			                                data-html="true"
			                                data-container="body" 
			                                data-toggle="popover" 
			                                data-placement="top"
			                                data-trigger="hover"
			                                :data-content='product.name'>
											{{ product.name | truncate(55) }}
			                            </a>
			                            <button @click="editRow(product, 'name')" class="btn btn-sm p-0 text-muted text-hover-pink jq-btn-editable" v-if="editable.editableColumn == ''">
			                            	<i class="fas fa-pencil-alt"></i>
			                            </button>
									</div>
								</td>

								<td class="text-nowrap font-weight-bold px-2 d-editable-none">
									<div v-if="editable.editableColumn == 'price' && editable.id == product.id">
										<div class="input-group flex-nowrap">
										  <input type="number" min="0" class="form-control form-control-sm min-w-120px" v-model="product.price">
										  <div class="input-group-append">
										    <button @click="saveEditable(product, 'price', $event)" class="btn btn-sm btn-pink" type="button">Save</button>
										    <button @click="closeEditable(product, 'price')" class="btn btn-sm btn-default" type="button">Cancel</button>
										  </div>
										</div>
									</div>
									<div v-else class="d-flex flex-row align-items-center justify-content-between">
										<div class="mr-1">
											{{ product.price | currency }}
										</div>
			                            <button @click="editRow(product, 'price')" class="btn btn-sm p-0 text-muted text-hover-pink jq-btn-editable" v-if="editable.editableColumn == ''">
			                            	<i class="fas fa-pencil-alt"></i>
			                            </button>
									</div>
								</td>

								<td class="text-nowrap">
									<a
		                                href="#"
		                                @click.prevent
		                                role="button"
		                                class="text-default"
		                                title="Product Image" 
		                                data-html="true"
		                                data-container="body" 
		                                data-toggle="popover" 
		                                data-placement="top"
		                                data-trigger="hover"
		                                :data-content="'<img class=\'img-fluid\' style=\'max-width:100px\' src='+'\''+product.main_image+'\'>'"
		                            >
		                                Show
		                            </a>
								</td>

								<td class="text-nowrap d-editable-none">
									<a
		                                href="#"
		                                role="button"
		                                @click.prevent="$root.$emit('showProductCategoriesModal', product)"
		                                class="text-default text-decoration-none d-block"
		                                data-html="true"
		                                data-container="body" 
		                                data-toggle="popover" 
		                                data-placement="top"
		                                data-trigger="hover"
		                                :data-content="product.categories.map(function(c) {
		                                	return c.name
		                                }).join(' > ')"
		                            >

		                            	<span v-if="product.categories.length === 0">---</span>
		                            	<span v-else>
			                                {{
			                                	product.categories.map(function(c) {
				                                	return truncate(c.name, 6)
				                                }).join(' > ')
			                                }}
		                            	</span>

		                            	<button @click.prevent="$root.$emit('showProductCategoriesModal', product)" class="btn btn-sm p-0 text-muted text-hover-pink jq-btn-editable">
			                            	<i class="fas fa-pencil-alt"></i>
			                            </button>
		                            </a>
								</td>

								<td class="text-nowrap">
									{{ product.nested_type | ucfirst }}
								</td>

								<td>
									<a href="#" @click.prevent="updateProductStatus(product, $event)">
		                                <span v-if="product.status == 2" class="text-warning font-weight-bold">Draft</span>
		                                <span v-else-if="product.status == 1" class="font-weight-bold">Enabled</span> 
		                                <span v-else class="text-danger font-weight-bold">Disabled</span>
									</a>
								</td>

								<td class="text-nowrap">
									<a href="#" @click.prevent="updateProductAvailability(product, $event)">
		                                <span v-if="product.availability_id == 0" class="text-danger font-weight-bold">Out of stock</span>
		                                <span v-else="product.availability_id == 1" class="font-weight-bold">In stock</span> 
									</a>
		                        </td>

		                        <td>
		                        	<div class="btn-group">
										<a :href="viewRoute.replace('__id', product.id)" title="" class="btn btn-sm btn-default view d-flex" target="_blank">
											<i class="fas fa-eye"></i>
										</a>
										<a :href="editRoute.replace('__id', product.id) + '?page=' + page" title="" class="btn btn-sm btn-default edit d-flex">
											<i class="fas fa-edit"></i>
										</a>
										<a href="#" class="btn btn-sm btn-pink d-flex" @click.prevent="destroyProduct(product)" :data-id="product.id" :id="'delete-' + product.id">
											<i class="fas fa-trash-alt"></i>
										</a>
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
			'dataRoute', 'editRoute', 'viewRoute', 'toggleAvailabilityRoute', 'toggleStatusRoute', 'updateColumnRoute', 'storeBrandRoute', 'destroyProductRoute', 'brands', 'startPage'
		],

		data(){
			return {
				loading: false,
				searchText: '',
				status: [],
				family: [],
				hasImages: [],
				availability_id: [],
				orderBy: 'id',
				sortOrder: 'asc',
				laravelResponse: {},
				products: [],
				page: 1,
				editable: {
					id: 0,
					editableColumn: '',
					product: null,
				}
			}
		},

		mounted() {

			let self = this

			window.onpopstate = function(event) {
				try {
					let newPage = event.state.path.split('?page=').pop()
					self.getResults(newPage);
				} catch (e) {

				}
			};

			if (localStorage !== undefined) {

				try {
					if (localStorage.sortOrder !== undefined) {
				      	this.sortOrder = localStorage.sortOrder;
				    }
				} catch (e) {
					console.log(e)
				}

				try {
					if (localStorage.orderBy !== undefined) {
				      	this.orderBy = localStorage.orderBy;
				    }
				} catch (e) {
					console.log(e)	
				}

				try {
					this.status = JSON.parse(localStorage.status)
				} catch (e) {
					console.log(e)
				}

				try {
					this.family = JSON.parse(localStorage.family)
				} catch (e) {
					console.log(e)
				}
				
				try {
					this.hasImages = JSON.parse(localStorage.hasImages)
				} catch (e) {
					console.log(e)
				}
				
				try {
					this.availability_id = JSON.parse(localStorage.availability_id)
				} catch (e) {
					console.log(e)
				}
			}

			this.getResults(this.startPage);

			this.$root.$on('productCategoriesSaved', function(product, newCategories) {
				self.products.map(function(p) {
					if (p.id == product.id) {
						p.categories = newCategories
					}
				})
			})
		},

		methods: {

			saveLocalStorage() {
                try {
                	localStorage.status = JSON.stringify(this.status)
					localStorage.family = JSON.stringify(this.family)
					localStorage.hasImages = JSON.stringify(this.hasImages)
					localStorage.availability_id = JSON.stringify(this.availability_id)
                } catch (e) {
                    console.log(e)
                }
            },

			sortBy(sortColumn) {

				if (sortColumn == this.orderBy) {
					this.sortOrder = this.sortOrder == 'asc' ? 'desc' : 'asc';
				} else {
					this.orderBy = sortColumn;
				}

				try {
					localStorage.sortOrder = this.sortOrder;
					localStorage.orderBy = this.orderBy;
				} catch (e) {
		
				}

				this.getResults(this.page);
			},


			updateProductStatus(product, $event) {
				this.editRow(product, 'status');

				if (product.status == 1) {
					product.status = 2;
				} else if(product.status == 2) {
					product.status = 0;
				} else if(product.status == 0) {
					product.status = 1;
				}
				
				this.saveEditable(product, 'status', $event)
			},

			updateProductAvailability(product, $event) {
				this.editRow(product, 'availability_id');

				if (product.availability_id == 1) {
					product.availability_id = 0;
				} else {
					product.availability_id = 1;
				}
				
				this.saveEditable(product, 'availability_id', $event)
			},

			closeEditable(row, editableColumn) {
				this.$set(row, editableColumn, this.editable.product[editableColumn])
				this.$set(this.editable, 'id', 0)
				this.$set(this.editable, 'product', null)
				this.$set(this.editable, 'editableColumn', '')
			},

			saveEditable(row, editableColumn, $event) {
				
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
					self.$set(self.editable.product, editableColumn, row[editableColumn])
					self.closeEditable(row, editableColumn)
				})
				.fail(function(response) {
					toastr.error(response.responseJSON.message)
				})
				.always(function() {

					if (editableColumn == 'status' || editableColumn == 'availability_id') {
						self.$set(self.editable, 'id', 0)
						self.$set(self.editable, 'product', null)
						self.$set(self.editable, 'editableColumn', '')
					}

					$($event.srcElement).parents('td').busyLoad('hide')
				});
			},

			editRow(row, editableColumn) {
				this.editable.id = row.id
				this.editable.product =  Object.assign({}, row)
				this.editable.editableColumn = editableColumn
			},

			updateProductBrand(product) {

				try {
					if (product.brand !== undefined && product.brand.id !== undefined) {
						product.brand_id = product.brand.id;
					}
				} catch(e) {

				}

				try {
					if (product.brand === null) {
						product.brand_id = null;	
					}
				} catch (e) {
					
				}
			},

			brandCreated(brand) {
				this.loading = true
				$.ajax({
					url: this.storeBrandRoute.replace('__id', this.editable.id),
					type: 'POST',
					data: {
						id: this.editable.id,
						brand: brand.name
					}
				})
				.done(function(response) {
					toastr.error(brand.name + ' added successefully')
				})
				.fail(function() {
					toastr.error('Something went wrong')
				})
				.always(() => {
					this.loading = false
				});	
			},
 
			truncate(text, stop, clamp) {
				return truncate(text, stop, clamp)
			},

			destroyProduct(product) {

				if (confirm('Please confirm this action') === false) {
					return true;
				}

				$.ajax({
					url: this.destroyProductRoute.replace('__id', product.id),
					type: 'POST',
					data: {
						_method: 'DELETE'
					},
				})
				.done((response) => {
					this.$set(product, 'deleted', true)
				})
				.fail(function(response) {
					toastr.error('Something went wrong')
				})
			},

			insertUrlParam(param, value) {
				return insertUrlParam(param, value);
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
					data: {
						s: this.searchText,
						status: this.status,
						family: this.family,
						images: this.hasImages,
						availability_id: this.availability_id,
						sort_order: this.sortOrder,
						order_by: this.orderBy
					},
				})
				.done(function(response) {
					self.products = response.data
					self.laravelResponse = response
					self.page = newPage
					self.saveLocalStorage()
				})
				.always(function() {

					self.$set(self.editable, 'id', 0)
					self.$set(self.editable, 'product', null)
					self.$set(self.editable, 'editableColumn', '')

					$(self.$el).busyLoad('hide')

					setTimeout(function() {
						$('[data-toggle="popover"]').popover()
					}, 400)
				});
			}
		}
	}
</script>