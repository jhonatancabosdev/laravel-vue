<template>
    <li class="my-0">
        <a
            :href="'/' + identifier + '/' + element.slug"
            :class="element.slug === currentLink ? 'text-highlight' : 'text-black'"
            class="accordion-child d-block py-2"
        >{{element.name}}</a>
    </li>
</template>

<script>
    export default {
        name: "AccordionChild",
        props:{
            element: {
                type: Object,
                default: function(){
                    return {};
                }
            },
            identifier:  {
                type: String,
                default: 'category'
            },
            currentLink: '',
            currentActive: {
                type: String,
                default: ''
            }
        }
    }
</script>

<style scoped>
 
</style>