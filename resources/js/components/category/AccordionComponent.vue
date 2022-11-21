<template>
    <div :id="identifier +'_accordion'">
        <div v-for="link in links" class="card mb-0 border-0" :key="link.name">
            <div class="card-header" :id="link.slug + '-parent'">
                <a
                    v-if="link.children && link.children.length > 0"
                    v-on:click="setActive(link.slug)"
                    :href="'/' + identifier + '/' + (link.slug !== undefined && link.slug !== null ? link.slug : link.id)"
                    class="d-flex text-align-left align-items-center mr-auto btn btn-link p-0"
                    data-toggle="collapse"
                    :data-target="'#sidebar-menu-' + link.slug"
                    aria-expanded="false"
                    :class="currentActive === link.slug ? 'link-' + link.slug + ' text-highlight' : 'link-' + link.slug + ' text-black'"
                    :aria-controls="'sidebar-menu-' + link.slug">
                    <span class="mb-0 accordion-text">
                        {{link.name}}
                    </span>
                    <button class="ml-auto btn p-0">
                        <i class="fa fa-lg" :class="currentActive == link.slug ? 'fa-minus' : 'fa-plus'"></i>
                    </button>
                </a>
                <a
                    v-else
                    :href="'/' + identifier + '/' + (link.slug !== undefined && link.slug !== null ? link.slug : link.id)"
                    class="d-flex text-align-left align-items-center mr-auto btn btn-link p-0"
                    :class="currentActive === link.slug ? 'text-highlight' : 'text-black'"
                    >
                    <span class="mb-0 accordion-text">
                        {{link.name}}
                    </span>
                </a>
            </div>
            
            <div
                v-if="link.children && link.children.length > 0" 
                :class="currentActive !== link.slug ? 'collapse' : ''"
                :aria-labelledby="'#' + link.slug+'-parent'"
                :data-parent="'#' + identifier + '_accordion'"
                :id="'sidebar-menu-' + link.slug">
                <div class="card-body py-0 bg-secondary-2">
                    <ul class="list-unstyled mb-0">
                        <accordion-child-component v-for="child in link.children" :currentActive="currentActive" :identifier="identifier" :current-link="currentLink" :element="child" :key="child.slug"></accordion-child-component>
                    </ul>
                </div>
                <div class="card-body py-0 bg-secondary-2">
                    <a :href="'/' + identifier + '/' + link.slug"
                        class="accordion-child d-flex text-align-left align-items-center mr-auto btn btn-link p-0"
                        :class="currentActive === link.slug ? 'text-highlight' : 'text-black'"
                        >
                        <span class="mb-0 accordion-text py-1">
                            Shop All {{link.name}}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Accordion",
        props:{
            links: {},
            currentLink: '',
            active:  {
                type: String,
                default: ''
            },
            identifier: {
                type: String,
                default: 'links'
            }
        },
        data: function () {
            return  {
                currentActive: this.active,
            }
        },
        methods: {
            setActive: function(newVal) {
                let $iconEl = $('#' + this.identifier + '_accordion svg');
                let $currEl = $('.link-' + this.currentActive);
                if(newVal !== this.currentActive) {
                    this.currentActive = newVal ?? this.active;
                    $iconEl.attr('data-icon', 'plus');
                    let icon = $('#' + this.currentActive + '-parent').find('svg');
                    icon.attr('data-icon', 'minus');
                    return this;
                } else {
                    $iconEl.attr('data-icon', 'plus');

                }
            }
        },
        created() {
            this.setActive();
        }
    }
</script>

<style scoped>

</style>