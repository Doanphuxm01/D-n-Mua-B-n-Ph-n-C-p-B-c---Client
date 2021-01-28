var app = new Vue({
    el: '#app',
    components: {
        'myheader': httpVueLoader('vue_component/header.vue'),
        'myfooter': httpVueLoader('vue_component/footer.vue'),
        'product-item': httpVueLoader('vue_component/product-item.vue'),
    },
});