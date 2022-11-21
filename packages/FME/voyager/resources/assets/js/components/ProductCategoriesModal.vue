<template>
	<div class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="min-height: 300px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title font-weight-bold" v-if="product.name !== undefined && product.name !== null">{{ product.name }}</h5>
                    <button type="button" class="close" aria-label="Close" @click.prevent="closeModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                	<div class="row" v-if="selectedCategories.length">
                		<div class="col-12">
                			<ul class="list-unstyled">
                				<li v-for="category in selectedCategories">{{ category.name }}</li>
                			</ul>
                		</div>
                	</div>
                	<div class="row">
                		<div class="col-12" v-if="display">
                			<div class="dd w-100">
			                    <product-categories class="d-flex flex-row justify-content-between align-items-start" :items="nestedCategories"></product-categories>
                			</div>
                		</div>
                	</div>
                </div>
                <div class="modal-footer border-top-0">
                    <div class="btn-group">
            			<button type="button" class="btn btn-default btn-sm jq-expend-all">Expand All</button>
                        <button type="button" class="btn btn-default btn-sm jq-collapse-all">Collapse All</button>
                        <button type="button" class="btn btn-default" @click.prevent="closeModal()">Cancel</button>
                        <button type="button" class="btn btn-pink" @click.prevent="saveProductCategories()">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
	export default {

		props: [
			'nestedCategories', 'flatCategories', 'saveCategoriesRoute'
		],

		data() {
			return {
				display: false,
				product: {},
				selectedCategories: []
			}
		},

		watch: {
			display(newValue) {
				if (newValue === true) {
					setTimeout(function() {
						$('.jq-collapse-all').trigger('click')
					}, 250)
				}
			}
		},

		methods: {
			closeModal() {
				$(this.$el).modal('hide')
				this.product = {}
				this.selectedCategories = []
				this.display = false;
			},

			saveProductCategories() {

				let self = this

				$(self.$el).busyLoad('show')

				$.ajax({
					url: this.saveCategoriesRoute,
					type: 'POST',
					data: {
						id: this.product.id,
						categories: this.selectedCategories.map(function(e) {
							return e.id;
						})
					},
				})
				.done(function() {
					self.$root.$emit('productCategoriesSaved', self.product, self.selectedCategories);
					toastr.success('Product categories updated successfully')
					self.closeModal();
				})
				.fail(function() {
					self.closeModal();
				})
				.always(function() {
					$(self.$el).busyLoad('hide')
				});
				
			}
		},

		mounted() {

			let self = this

			this.$root.$on('showProductCategoriesModal', product => {
				this.display = true;

				this.selectedCategories = product.categories.map(function(c) {
					return c.id;
				})

				this.product = product

				setTimeout(function() {
					self.$root.$emit('selectCategories', product.categories)
				}, 250)

				$(this.$el).modal('show')
			})

			this.$root.$on('selectedCategoriesUpdated', function() {
				self.selectedCategories = []

				$.each($(self.$el).find('[type="checkbox"]:checked'), function(index, val) {
					self.selectedCategories.push(parseInt($(val).attr('value')))
				});

				if (self.selectedCategories.length > 0) {
					self.selectedCategories = self.flatCategories.filter(function(c) {
						return $.inArray(c.id, self.selectedCategories) !== -1;
					})
				}

				self.$emit('categories-updated', self.selectedCategories);
			})
		}
	}
</script>