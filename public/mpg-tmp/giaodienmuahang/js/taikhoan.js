var app = new Vue({
    el: '#app',
    components: {
        'myheader': httpVueLoader('vue_component/header.vue'),
        'myfooter': httpVueLoader('vue_component/footer.vue'),
        'breadcrumb': httpVueLoader('vue_component/breadcrumb.vue'),
    },
});