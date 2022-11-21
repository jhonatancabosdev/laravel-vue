<template>
    <span class="add-to-favorites__wrapper d-flex justify-content-start px-2 align-items-start">
        <a class="link link--add-to-favorites px-1 mx-1 " v-bind:class="this.isFavorited ? 'text-red' : ''" @click="toggleItem()">
            <i class= "fa-heart" v-bind:class="this.isFavorited ? 'fas' : 'far'"></i>
        </a>
    </span>

</template>

<script>

export default {

    props: [
        'product'
    ],

    data() {
        return {
            isFavorited: false
        }
    },

    mounted() {
        let product = this.product;

        if(typeof this.product === 'string'){
            product = JSON.parse(this.product);
        }

        let favorites = localStorage.getItem('favorites')

        if (favorites === null || favorites === undefined) {
            localStorage.setItem('favorites', JSON.stringify([]))
        } else {
            try {
                let id = parseInt(product.id);
                favorites = JSON.parse(localStorage.getItem('favorites'))
                favorites = favorites.filter(function(favoriteId) {
                    return favoriteId === id;
                })
                this.isFavorited = favorites.length === 0 ? false : true
            } catch (e) {
                console.log(e)
            }
        }
    },
    
    methods: {
        toggleFavorites() {
            let product = this.product;

            if(typeof this.product === 'string'){
                product = JSON.parse(this.product);
            }

            try {

                let id = parseInt(product.id);
                let favorites = JSON.parse(localStorage.getItem('favorites'))

                if (favorites.length === 0) {
                    favorites.push(id);
                    localStorage.setItem('favorites', JSON.stringify(favorites));
                    return true;
                }

                if ($.inArray(id, favorites) === -1) {
                    favorites.push(id);
                    localStorage.setItem('favorites', JSON.stringify(favorites));
                    return true;   
                }

                if ($.inArray(id, favorites) !== -1) { 
                    favorites = favorites.filter(function(favoriteId) {
                        return favoriteId !== id;
                    })
                    localStorage.setItem('favorites', JSON.stringify(favorites));
                    return false;
                }

            } catch (e) {
                console.log(e)
            }
        },

        toggleItem() {
            let product = this.product;

            if(typeof this.product === 'string'){
                product = JSON.parse(this.product);
            }

            this.isFavorited = this.toggleFavorites();

            if (this.isFavorited) {
                toast(product.name + ' - <strong>ADDED</strong> to your wishlist.')
            } else {
                toast(product.name + ' - <strong>REMOVED</strong> from your wishlist')
            }

            this.$root.$emit('favorites_updated');
        }
    }
};
</script>

<style lang="scss" scoped>
    .link--add-to-favorites {
        cursor: pointer;
        font-size: 1.35rem;
    }
</style>