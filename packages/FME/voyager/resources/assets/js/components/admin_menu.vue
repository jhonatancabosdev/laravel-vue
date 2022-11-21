<template>
    <ul class="nav navbar-nav " :class="'pl-' + paddingLeft">
        <li v-for="(item, i) in items" :class="classes(item)">
            <a
                class="d-flex flex-row align-items-center text-decoration-none"
                :role="item.children.length > 0 ? 'button' : ''"
                :target="item.target" 
                :href="item.children.length > 0 ? '#'+item.id+'-dropdown-element' : item.href" 
                :style="'color:'+color(item)" 
                :data-toggle="item.children.length > 0 ? 'collapse' : false" 
                :aria-expanded="item.children.length > 0 ? String(item.active) : false"
                @click.prevent="openItem(item)"
            >
                <span :class="'mt-0 text-center icon '+item.icon_class"></span>
                <span class="title text-nowrap position-relative">{{ item.title }}</span>
            </a>
            <div v-if="item.children.length > 0" :id="''+item.id+'-dropdown-element'" class="collapse show">
                <div class="panel-body">
                    <admin-menu :items="item.children" :padding-left="paddingLeft * 2"></admin-menu>
                </div>
            </div>
        </li>
    </ul>
</template>
<script>
export default {
    props: {
        paddingLeft: {
            type: Number,
            default: 2
        },
        items: {
            type: Array,
            default: [],
        }
    },
    methods: {
        openItem(item) {
            if (item.children.length > 0 && item.target == '_self') {
                $('#'+item.id+'-dropdown-element').toggleClass('show')
            } else {
                location.href = item.href;
            }
        },

        classes: function(item) {
            var classes = [];
            if (item.children.length > 0) {
                classes.push('dropdown');
            }
            if (item.active) {
                classes.push('active');
            }


            return classes.join(' ');
        },
        color: function(item) {
            if (item.color && item.color != '#000000') {
                return item.color;
            }

            return '';
        }
    },

    mounted() {
        $(this.$el).find('[data-toggle="popover"]').popover()
    }
};
</script>
