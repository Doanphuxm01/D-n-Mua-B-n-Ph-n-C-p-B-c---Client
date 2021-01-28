const app = new Vue({
    el: '#info-products',
    data: {
        lsObj: lsObj,
        amount: 0,
        errors: [],
        maximum: 100,
        minimum: 1,
    },
    mounted() {

    },
    methods: {
        getImageSrc(path) {
            return 'https://ad.kayn.pro/data/'+path
        },
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
        addToCart(obj) {
            var fk = this;
            fk.addToCartSuccess(obj)
        },
        downAmount(obj) {
            var fk = this
            if(obj.amount >= fk.minimum) {
                obj.amount--
                fk.addToCart(obj)
            }else {
                alert('Bạn đạt giới hạn mua với số lượng '+fk.minimum+' sản phẩm.')
            }

        },
        upAmount(obj) {
            var fk = this
            if(obj.amount < fk.maximum) {
                obj.amount++
                fk.addToCart(obj)
            }else {
                alert('Bạn đạt giới hạn mua với số lượng '+fk.maximum+' sản phẩm.')
            }
        },
        changeAmount(obj) {
            var fk = this
            if(obj.amount > fk.maximum) {
                alert('Bạn đạt giới hạn mua với số lượng '+fk.maximum+' sản phẩm.');
                fk.addToCart(obj)
            }else if(obj.amount > 0 && obj.amount < 100) {
                fk.addToCart(obj)
            }else if(obj.amount < 1) {
                fk.addToCart(obj)
            }
        },
        addToCartSuccess(obj) {
            var fk = this;
            axios.post(public_link('checkout/addToCart'), {'id': obj.id, 'sku': obj.sku, 'amount': obj.amount, 'options': obj.options})
                .then(response => {
                    let res = response.data;
                    if(res.status == 1) {

                    }else {
                        // console.log(response)
                        // alert(res.msg)
                    }
                    location.reload();
                })
                .catch(e => {
                    this.errors.push(e)
                })

        },
    }
});