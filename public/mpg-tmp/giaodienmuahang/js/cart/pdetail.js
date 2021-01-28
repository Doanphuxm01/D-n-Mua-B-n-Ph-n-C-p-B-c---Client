$('#them-vao-gio').click(function () {
    var id = $('input[name="obj[_id]"]').val();
    var sku = $('input[name="obj[sku]"]').val();
    var amount = $('input[name="count"]').val();
    var obj = [
        {'name': 'id', 'value': id},
        {'name': 'sku', 'value': sku},
        {'name': 'amount', 'value': amount},
        {'name': 'type', 'value': 'product'},
    ]
    if(typeof amount != 'undefined' && amount > 0) {
        addToCartSuccess(obj)
    }else {
        $('.fxbotbtnbuy').attr('disabled', 'disabled')
    }
})


function addToCartSuccess(obj) {
    var formdata = obj;

    let callBack = function (json) {
        if (json.status != 1) {
            alert(json.msg);
        } else {
            if (typeof json.data !== 'undefined') {
                try {
                    $(".add-to-cart-success").removeClass('d-none').addClass('d-block');
                    $('html, body').animate({scrollTop: 0}, '300');
                    cart_load_number()
                } catch (e) {
                    console.log(e)
                }
            }
        }
    };
    _POST(public_link('checkout/addToCart'), formdata, callBack);
}
