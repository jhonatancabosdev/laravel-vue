<template>
	<div role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <slot></slot>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            	<div v-if="searchMode">
            		<div class="form-group p-3 bg-light border border-secondary shadow rounded-lg mb-4">
            			<label class="font-weight-bold">Search from existing products</label>
						<div class="input-group">
							<input type="text" class="form-control border-pink" placeholder="Search by id, sku, name" v-model="searchText">
							<div class="input-group-append">
								<button class="btn btn-outline-pink" @click="searchMode = false" type="button">Cancel</button>
								<button class="btn btn-pink" @click="search()" type="button">Search</button>
							</div>
						</div>
            		</div>
            	</div>
                <table class="table table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Availability</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody v-if="searchMode === false">
                    	<tr v-for="(row, index) in rows" :class="row.editMode === true ? 'table-pink' : ''">
                    		<td>{{ row.id }}</td>
                    		<td>
								<span v-if="row.editMode === false">{{ row.name }}</span>
								<div v-else>
									<input type="text" v-model="row.name" class="form-control">
								</div>
                    		</td>
                    		<td>
                    			<span v-if="row.editMode === false">{{ row.sku }}</span>
								<div v-else>
									<input type="text" v-model="row.sku" class="form-control">
								</div>
                    		</td>
                    		<td>
                    			<span v-if="row.editMode === false">{{ row.price | currency }}</span>
								<div v-else>
									<input type="text" v-model="row.price" class="form-control">
								</div>
                    		</td>
                    		<td>
								<a v-if="row.editMode === false" href="#" class="cursor-pointer" @click.prevent="toggleAvailability(row)" :class="row.availability_id == 0 ? 'font-weight-bold' : ''">
									{{ row.availability_id == 0 ? 'Out of stock' : 'In stock' }}
								</a>
								<div v-else>
									<select class="form-control" v-model="row.availability_id">
										<option value="0">Out of stock</option>
										<option value="1">In stock</option>
									</select>
								</div>
                    		</td>

                    		<td>
								<a v-if="row.editMode === false" href="#" class="cursor-pointer" @click.prevent="toggleStatus(row)">
									<span v-if="row.status == 0" class="text-danger">Disabled</span>
									<span v-if="row.status == 1">Enabled</span>
									<span v-if="row.status == 2" class="text-warning font-weight-bold">Draft</span>
								</a>
								<div v-else>
									<select class="form-control" v-model="row.status">
										<option value="0">Disabled</option>
										<option value="1">Enabled</option>
										<option value="2">Draft</option>
									</select>
								</div>
                    		</td>

                    		<td>
                    			<div class="btn-group">
                    				<button @click.prevent="removeRow(index, row)" class="btn btn-sm btn-light">
                    					<i class="fas fa-trash-alt"></i>
                    				</button>
                    				<button v-if="row.editMode === false" class="btn btn-sm btn-light" @click.prevent="row.editMode = true">
                    					<i class="fas fa-edit"></i>
                    				</button>
                    				<button v-else class="btn btn-sm btn-pink" @click.prevent="saveRow(row, true)">
                    					<i class="fas fa-check"></i>
                    				</button>
                    			</div>
                    		</td>
                    	</tr>
                    </tbody>

                    <tbody v-else-if="laravelResponse.data !== undefined">
						<tr v-for="(row, index) in laravelResponse.data" :class="row.editMode === true ? 'table-pink' : ''" v-if="row.added !== true">
                    		<td>{{ row.id }}</td>
                    		<td>
								<span>{{ row.name }}</span>
                    		</td>
                    		<td>
                    			<span>{{ row.sku }}</span>
                    		</td>
                    		<td>
                    			<span>{{ row.price | currency }}</span>
                    		</td>
                    		<td>
								<span :class="row.availability_id == 0 ? 'font-weight-bold' : ''">
									{{ row.availability_id == 0 ? 'Out of stock' : 'In stock' }}
								</span>
                    		</td>

                    		<td>
								<span>
									<span v-if="row.status == 0" class="text-danger">Disabled</span>
									<span v-if="row.status == 1">Enabled</span>
									<span v-if="row.status == 2" class="text-warning font-weight-bold">Draft</span>
								</span>
                    		</td>

                    		<td>
                    			<div class="btn-group">
                    				<button @click.prevent="removeRow(index, row)" class="btn btn-sm btn-light">
                    					<i class="fas fa-trash-alt"></i>
                    				</button>
                    				<button v-if="row.editMode === false" class="btn btn-sm btn-light" @click.prevent="row.editMode = true">
                    					<i class="fas fa-edit"></i>
                    				</button>
                    				<button v-else class="btn btn-sm btn-pink" @click.prevent="saveRow(row, true)">
                    					<i class="fas fa-check"></i>
                    				</button>
                    			</div>
                    		</td>
                    	</tr>
                    </tbody>

                </table>

                <div v-if="searchMode">
                    <pagination :limit="4" :data="laravelResponse" align="right" @pagination-change-page="search"></pagination>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <div class="btn-group">
                    <button type="button" class="btn btn-pink" @click="addRow()">Add new option</button>
                    <button type="button" class="btn btn-pink" @click="searchMode = !searchMode">Add existing item</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	export default {

		props: [
			'product', 'dataRoute', 'updateRoute', 'destroyRoute'
		],

		data(){
			return {
				id: 0,
				laravelResponse: {},
				searchMode: false,
				searchText: '',
				rows: [],
			}
		},

		mounted() {
			this.id = this.product.id

			if (this.product !== undefined && this.product.children !== undefined) {
				this.rows = this.product.children.map(function(e) {
					e.editMode = false;
					return e;
				})
			}
		},

		methods: {

			search(page = 1) {
				let self = this

				$(self.$el).busyLoad('show')

				$.ajax({
					url: this.dataRoute + '?page=' + page,
					type: 'GET',
					data: {
						s: this.searchText
					}
				})
				.done(function(response) {
					self.laravelResponse = response
				})
				.always(function() {
					$(self.$el).busyLoad('hide')
				});
			},

			toggleAvailability(row) {
				row.availability_id = row.availability_id == '0' ? '1' : '0';
				this.saveRow(row, false);
			},
			
			toggleStatus(row) {

				if (row.status == '0') {
					row.status = '1';
				} else if (row.status == '1') {
					row.status = '2';
				} else {
					row.status = '0';
				}
				
				this.saveRow(row, false);
			},

			saveRow(row, useConfirm) {

				if (useConfirm === true && confirm('Please confirm this action') === false) {
					return false;
				}

				let el = $(this.$el)
				el.busyLoad('show')

				let self = this

				$.ajax({
					url: this.updateRoute,
					type: 'POST',
					data: row,
				})
				.done(function(response) {

					if (self.searchMode) {

						try {

							let found = self.rows.filter(function(r) {
								return r.id === response.id;
							})

							if (found.length === 0) {
								self.rows.push(response)
							}

							self.$set(row, 'added', true)
							toastr.success('Product added successfully as a child');

						} catch (e) {

						}
					} else {
						row.id = response.id
						row.editMode = false;
						toastr.success('Product option updated successfully');
					}
				})
				.fail(function() {
					toastr.error('Something went wrong');
				})
				.always(function() {
					el.busyLoad('hide')
				});
			},

			addRow(index){
				let row = this.rows[index] !== undefined ? this.rows[index] : this.defaultRow;
				this.rows.push(
					JSON.parse(JSON.stringify(row))
				)
			},

			removeRow(index, row) {

				if (row.editMode !== true && confirm('Are you sure?') === false) {
					return false;
				}

				let el = $(this.$el)
				let self = this

				if (row.id === '--' && self.rows.length > 0) {
		            self.rows.splice(index, 1);
		            return false;
				}

				el.busyLoad('show')

				$.ajax({
					url: this.destroyRoute,
					type: 'DELETE',
					data: row,
				})
				.done(function() {
					toastr.success('Product option deleted successfully')
					if (self.rows.length > 0) {
			            self.rows.splice(index, 1);
					}
				})
				.fail(function() {
					toastr.error('Something went wrong')
				})
				.always(function() {
					el.busyLoad('hide')
				});
	        }
		},

		computed: {
			defaultRow() {
				return {
					id: '--',
					name: '',
					sku: '',
					price: 0,
					availability: undefined,
					status: 0,
					editMode: true,
				}
			}
		}
	}	
</script>