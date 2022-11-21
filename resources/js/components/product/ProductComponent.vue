<template>

  <div class="product-component__wrapper container px-0">

    <div class="bg-white px-0 py-0 px-lg-3 pt-lg-3">

      <div class="row">

        <div class="col-md-6 d-flex flex-column justify-content-around">
          <product-images-component :product="currentProduct"></product-images-component>

          <ul class="list-inline text-center">
            <li class="list-inline-item">
              <a
                  target="_blank"
                  class="text-highlight d-block px-3"
                  :href="'https://www.facebook.com/sharer/sharer.php?u=https://gwcopiers.com/product/' + currentProduct.slug"
              >
                <i class="fab fa-facebook-f fa-lg h3"></i>
              </a>
            </li>
            <li class="list-inline-item">
              <a
                  target="_blank"
                  :href="'https://twitter.com/intent/tweet?text=' + encodeURI('Check out the ' + currentProduct.name) + '+https://gwcopiers.com/product/' + currentProduct.slug"
                  class="text-highlight d-block px-3">
                <i class="fab fa-twitter fa-lg h3"></i>
              </a>
            </li>
            <li class="list-inline-item">
              <a
                  target="_blank"
                  :href='"http://pinterest.com/pin/create/button/?url=https://pacificsandsinc.com/product/" + currentProduct.slug + "&media=https://gwcopiers.com" + currentProduct.large_main_image + "&description=" + encodeURI("Check out this " + currentProduct.name)'
                  class="text-highlight d-block px-3">
                <i class="fab fa-pinterest fa-lg h3"></i>
              </a>
            </li>
          </ul>
        </div>

        <div class="col-md-6 pl-2 pr-0 px-md-3">
          <add-to-favorites
              v-if="product.quantity > 0"
              class="px-0 position-absolute"
              context="short"
              icon="fa-star"
              defaultFilled="true"
              :product="product"
          ></add-to-favorites>
          <div class="px-2">
            <p v-if="currentProduct.sku"
               class="font-family-alt mb-0">
              SKU: {{ currentProduct.sku }}
            </p>
            <h1 class="h2 mb-2 monsterrat text-black">
              {{ currentProduct.name }}
            </h1>

            <div class="d-flex mb-2 text-seagreen2">
              <p class="mb-0 pr-2">
                <strike class="text-secondary-6"
                        v-if="currentProduct.compare_at_price > 0"> {{
                    currentProduct.compare_at_price | currency
                                                                    }} </strike> <strike class="text-secondary-6"
                                                                                         v-else>
                {{ currentProduct.original_price | currency }} </strike>
              </p>
              <p class="mb-0 h4 text-highlight"
                 title="product price">
                {{ currentProduct.price | currency }}
              </p>
              <p class="pl-4 py-0 text-right">
                <span v-if="currentProduct.quantity <= 0"
                      class="text-danger">Out of Stock</span>
              </p>
            </div>
            <table class="table-sm responsive w-100 d-block d-md-table">
              <tr style="border-bottom: 1px black solid" v-if="currentProduct.short_description">
                <td colspan="2">
                  <p class="font-family-alt">
                    {{ currentProduct.short_description }}
                    <a v-if="currentProduct.description.length > currentProduct.short_description.length"
                      href="#full">...read more
                    </a>
                  </p>
                </td>
              </tr>
              <tr style="border-top: 1px black solid; border-bottom: 1px black solid" v-for="tag in currentProduct.tags">
                <td v-if="tag.type != null">
                  {{ tag.type }}:
                </td>
                <td v-if="tag.type != null">
                  {{tag.name.en}}
                </td>
              </tr>
            </table>
            <div class="my-3 py-1">

              <product-cart-component :product="currentProduct"></product-cart-component>

            </div>

          </div>

        </div>

      </div>


    </div>

  </div>

</template>

<script>

export default {
  props: {
    product: {
      type: Object,
      default: function () {
        return null;
      }
    },
    children: {
      type: Object,
      default: function () {
        return null;
      }
    },
    averageRating: {
      type: Number,
      default: 0
    },
    reviewCount: {
      type: Number,
      default: 0
    }
  },

  data() {
    return {
      selectedProduct: this.product.name
    }
  },

  computed: {
    currentProduct: {
      get: function () {
        if (this.selectedProduct === undefined || this.selectedProduct === this.product.name || this.selectedProduct.length === 0) {
          return this.product
        }

        return this.selectedProduct;
      },
      set: function (newValue) {
        this.selectedProduct = newValue;
      }
    },

    mainTitle() {
      return (this.product.main_name != '' && this.product.main_name != null) ? this.product.main_name : this.currentProduct.title;
    }
  },

  mounted() {
    let product = this.product;

    if (typeof this.product === 'string') {
      product = JSON.parse(this.product);
    }

    let recentlyViewedProducts = localStorage.getItem('recentlyViewedProducts')

    if (recentlyViewedProducts === null || recentlyViewedProducts === undefined) {
      localStorage.setItem('recentlyViewedProducts', JSON.stringify([]));
      recentlyViewedProducts = [];
    } else {
      recentlyViewedProducts = JSON.parse(recentlyViewedProducts);
    }
    try {
      let id = parseInt(product.id);
      if (!recentlyViewedProducts.includes(id)) {
        recentlyViewedProducts.push(id);
        localStorage.setItem('recentlyViewedProducts', JSON.stringify(recentlyViewedProducts));
      }
    } catch (e) {
      console.log(e)
    }
  }
}

</script>
<style lang="scss"
       scoped>
a {
  color: #555;
  text-decoration: none !important;
}


</style>