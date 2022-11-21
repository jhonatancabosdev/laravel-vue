<template>
    <ol class="dd-list">
        <li v-for="(item, i) in items" :class="classes(item)" class="dd-item position-relative">
            <button v-if="item.children.length > 0" style="left: 3px; top: 3px; z-index: 10000" class="position-absolute btn btn-sm jq-expend-nested">
                <i class="fas fa-caret-down"></i>
            </button> 
            <div class="dd-handle position-relative">
                <label class="mb-0">
                    <input 
                        type="checkbox" 
                        name="selected_category"
                        v-model="selectedCategories" 
                        @change="$root.$emit('selectedCategoriesUpdated', selectedCategories)" 
                        :value="item.id" 
                        :id="'category-' + item.id"
                    >
                    &nbsp;{{ item.name }}
                </label>
            </div>
            <product-categories  v-if="item.children.length > 0" :items="item.children"></product-categories>
        </li>
    </ol>
    
</template>
<script>
export default {
    props: [
        'items'
    ],
    data() {
        return {
            selectedCategories: []
        }
    },
    mounted() {

        let el = this.$el
        $(el).busyLoad('show')

        this.$root.$on('selectCategories', categories => {

            let selectedIds = categories.map(function(c) {
                return c.id;
            });

            if (this.items.length > 0 && selectedIds.length > 0) {
                let isFound = this.items.filter(function(e) {
                    return $.inArray(e.id, selectedIds) !== -1;
                })
                isFound = isFound.length > 0;
                if (isFound) {
                    this.selectedCategories = selectedIds
                }
            }
        })

        setTimeout(function() {
            $(el).find('input[type="checkbox"]:checked').parents('.dd-item.dd-collapsed').removeClass('dd-collapsed')
            $.each($('.dd').find('input[type="checkbox"]:checked').parents('.dd-item').find('input[type="checkbox"]:checked:last'), function(index, val) {
                if ($(val).parents('.dd-item.dropdown:first').find('input[type="checkbox"]:checked').length === 1) {
                    $(val).parents('.dd-item.dropdown:first').addClass('dd-collapsed')
                }
            });
            $(el).busyLoad('hide')
        }, 350)
    },
    methods: {
        classes: function(item) {
            var classes = [];

            if (item.children.length > 0) {
                classes.push('dropdown');
            }

            if (item.active) {
                classes.push('active');
            }

            return classes.join(' ');
        }
    }
};
</script>

<style scoped="">
    .pl-2 {
        padding-left: 1.3rem !important; 
    }
    .pl-8 {
        padding-left: 2rem !important;
    }
</style>
