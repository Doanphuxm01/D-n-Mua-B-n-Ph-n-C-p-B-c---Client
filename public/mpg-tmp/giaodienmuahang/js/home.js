var app = new Vue({
    el: '#app',
    components: {
        'myheader': httpVueLoader('vue_component/header.vue'),
        'myfooter': httpVueLoader('vue_component/footer.vue'),
        'product-sale': httpVueLoader('vue_component/product-sale.vue'),
        'product-sugget': httpVueLoader('vue_component/product-sugget.vue'),
        'product-item': httpVueLoader('vue_component/product-item.vue'),
    },
});