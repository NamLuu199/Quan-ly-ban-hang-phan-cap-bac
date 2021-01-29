$('#them-vao-gio').click(function () {
    var type_muaban = $('input[name="obj[type_muaban]"]:checked').val();
    var id = $('input[name="obj[_id]"]').val();
    var sku = $('input[name="obj[sku]"]').val();
    var amount = $('input[name="count"]').val();
    var obj = [
        {'name': 'id', 'value': id},
        {'name': 'type_muaban', 'value': type_muaban},
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

$('.type-muaban, #count-amount').on('change',function () {
    getDataProduct()
})

function getDataProduct() {
    var val = $('input[name="obj[type_muaban]"]:checked').val();
    var id = $('input[name="obj[_id]"]').val();
    var sku = $('input[name="obj[sku]"]').val();
    var amount = $('input[name="count"]').val();
    formdata = [
        {'name': 'type_muaban', 'value': val},
        {'name': 'sku', 'value': sku},
        {'name': '_id', 'value': id},
        {'name': 'amount', 'value': amount},
    ];
    let callBack = function (json) {
        if (json.status != 1) {
            alert(json.msg);
        } else {
            if (typeof json.data !== 'undefined') {
                try {
                    data = json.data;
                    $('#amount').text('(tối đa có thể mua: '+data.amount+')')
                    $('.price').text(data.finalPrice)
                } catch (e) {
                    console.log(e)
                }
            }
        }
    };
    _POST(public_link('ajax/get-data-product'), formdata, callBack);
}


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
