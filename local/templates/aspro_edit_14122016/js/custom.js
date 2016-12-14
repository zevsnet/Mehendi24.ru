/*
 You can use this file with your scripts.
 It will not be overwritten when you upgrade solution.
 */
function z_yandex(name)
{
    //console.log(name);
    yaCounter39450960.reachGoal(name);
}


$(document).ready(function()
{
    $('.big_btn.to-cart.button').click(function(){z_yandex('BASKET_ADD')});
    $('.small.to-cart.button').click(function(){z_yandex('BASKET_ADD')});
    $('.transparent.big_btn.type_block.button.one_click').click(function(){z_yandex('BASKET_ONE_ADD')});
    $('#one_click_buy_form_button').click(function(){z_yandex('BASKET_ONE_BUY')});
    $('.button.transparent.big_btn.checkout').click(function(){z_yandex('SALE_BASKET')});
    $('.button.big_btn.fast_order').click(function(){z_yandex('SALE_BASKET_FAST')});
    $('.button.transparent.big_btn.grey_br').click(function(){z_yandex('NEXT_BUY_PRODUCT')});
    $('.checkout.button.big_btn#ORDER_CONFIRM_BUTTON').click(function(){z_yandex('SALE_BUY_BIG')});

});