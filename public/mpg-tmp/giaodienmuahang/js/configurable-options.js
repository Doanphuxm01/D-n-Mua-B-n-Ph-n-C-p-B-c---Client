const app = new Vue({
    el: '#info-products',
    data: {
        options: options,
        products: products,
        item: [],
        op_keys: {},
        id: pid,
        amount: 1,
        errors: []
    },
    watch: {
        amount: function() {
            this.changeAmount()
        }
    },
    mounted() {
        $('[data-toggle="tooltip"]').tooltip();
        $('#options ul>li:first-child a').addClass('active');
        $.each($('.js-filter'), function (i, e) {
            $('.js-filter.active').siblings('input.input-option-'+i+'[type="radio"]').prop('checked', true);
            $('.js-filter.group-option-'+i).click(function (event) {
                $('.js-filter.group-option-'+i).removeClass('active');
                $('.js-filter.group-option-'+i).siblings('input.input-option-'+i+'[type="radio"]').prop('checked', false)
                $container = $(this);
                $container.addClass('active')
                $container.siblings('input.input-option-'+i+'[type="radio"]').prop('checked', true)
            })

            $('input.input-option-'+i+'[type="radio"]').click(function (e) {
                $('.js-filter.group-option-'+i).removeClass('active');
                $container = $(this);
                $container.siblings('.js-filter.group-option-'+i).addClass('active');
            });
        })
        this.item = this.products[0];
        this.setOpKeys();
    },
    methods: {
        formatMoney(value) {
            let val = (value/1).toFixed(0).replace('.', ',');
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")+' ₫';
        },
        calcDiscount(finalPrice, regularPrice, format = false) {
            var fk = this
            finalPrice = parseInt(finalPrice);
            regularPrice = parseInt(regularPrice);
            if(!finalPrice || !regularPrice){
                return 0;
            }
            if(format) {
                return fk.formatMoney(regularPrice - finalPrice);
            }
            return Math.round(100 - (finalPrice/regularPrice*100))+'%';
        },
        chooseThis(event, val, option) {
            var fk = this
            fk.item = fk.filterProduct(fk.op_keys);
        },
        filterProduct(op_keys) {
            var fk = this;
            let selectedPro = fk.products.filter(
                function(item) {
                    var temp = 0;
                    $.each(op_keys,function(i,v){
                        if(item[i] == v) {
                            temp++;
                        }else {
                            temp = 0;
                        }
                    });
                    return temp > 1;
                }
            );
            if(typeof selectedPro[0] != 'undefined') {
                return selectedPro[0];
            }
            return fk.item;
        },
        addToCart() {
            var fk = this;
            if(typeof fk.item['amount'] != 'undefined' && fk.item.amount > 0) {
                if(fk.amount > 0) {
                    fk.addToCartSuccess()
                }
            }else {
                $('.fxbotbtnbuy').attr('disabled', 'disabled')
            }
        },
        downAmount() {
            var fk = this
            if(fk.amount > 1) {
                fk.amount--
            }
        },
        upAmount() {
            var fk = this
            if(fk.amount < 100) {
                fk.amount++
            }else {
                alert('Bạn đạt giới hạn mua với số lượng 100 sản phẩm.')
            }
        },
        changeAmount() {
            var fk = this
            if(fk.amount > 100) {
                alert('Bạn đạt giới hạn mua với số lượng 100 sản phẩm.');
                fk.amount = 100
            }
            if(fk.amount < 1) {
                fk.amount = 1
            }
        },
        addToCartSuccess() {
            var fk = this;
            axios.post(public_link('checkout/addToCart'), {'id': fk.id, 'sku': fk.item['sku'], 'amount': fk.amount, 'options': fk.op_keys, 'type': 'product'})
            .then(response => {
                let res = response.data;
                if(res.status == 1) {
                    $(".add-to-cart-success").removeClass('d-none').addClass('d-block');
                    $('html, body').animate({scrollTop:0}, '300');
                    cart_load_number()
                }else {
                    console.log(response)
                    alert(res.msg)
                }
            })
            .catch(e => {
                this.errors.push(e)
            })

        },
        setOpKeys(id) {
            var fk = this
            $.each(fk.options, function (i, v) {
                fk.$set(fk.op_keys, v.key, v.values[0].name)
            })
        },
    }
});