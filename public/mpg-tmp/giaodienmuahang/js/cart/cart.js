const app = new Vue({
    el: '#info-products',
    data: {
        lsObj: lsObj,
        amount: 0,
        errors: [],
        maximum: 10000,
        minimum: 1,
    },
    mounted() {

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
                location.reload();
            }
        },
        changeAmount(obj) {
            var fk = this
            if(obj.amount > fk.maximum) {
                alert('Bạn đạt giới hạn mua với số lượng '+fk.maximum+' sản phẩm.');
                location.reload();
            }else if(obj.amount > 0 && obj.amount < fk.maximum) {
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
                        location.reload();
                    }else {
                        // console.log(response)
                        alert(res.msg)
                    }
                })
                .catch(e => {
                    this.errors.push(e)
                })
                .then(response => {
                    if (typeof response != 'undefined') {
                        let res = response.data;
                        if(res.status == 1) {

                        }else {
                            // console.log(response)
                            alert(res.msg)
                        }
                    }
                    location.reload();
                })

        },
        remove: function(e,index,obj){
            e.preventDefault();
            Swal.fire({
                title: 'Thông báo',
                text:'Bạn muốn loại sản phẩm này ra khỏi giỏ hàng?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#41bb29',
                cancelButtonColor: '#f36f21',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Không',
            }).then((result) => {
                if (result.value) {
                    axios.post(public_link('checkout/cartRemove'), {'id': obj.id, 'sku': obj.sku, 'amount': obj.amount, 'options': obj.options})
                        .then(response => {
                            let res = response.data;
                            if (res.status == 1) {
                                Swal.fire({
                                    title: 'Thông báo',
                                    text: res.msg,
                                    type: 'success',
                                    showCancelButton: !true,
                                    confirmButtonColor: '#41bb29',
                                    confirmButtonText: 'Ok',
                                })
                                location.reload();
                            } else {
                                Swal.fire({
                                    title: 'Thông báo',
                                    text: res.msg,
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#41bb29',
                                    cancelButtonColor: '#f36f21',
                                    confirmButtonText: 'Đồng ý',
                                    cancelButtonText: 'Không',
                                })
                            }
                        })
                        .catch(e => {
                            this.errors.push(e)
                        })
                }
            });
        },
    }
});