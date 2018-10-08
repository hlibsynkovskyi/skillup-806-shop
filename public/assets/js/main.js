jQuery(function($) {
    var $loadingMask = $('#loading-mask');

    $('.js-add-to-cart').click(function (event) {
        var $me = $(this);

        event.preventDefault();
        $loadingMask.show();

        $.get($me.attr('href'), function(data) {
            $('.js-cart-in-header').html(data);
            $loadingMask.hide();
        })
    });

    $('#cart-table').each(function () {
        var $me = $(this);

        $me.find('.js-item-quantity').on('input', function() {
            var $input = $(this),
                data = {};

            data[$input.attr('name')] = $input.val();
            $.post($input.data('update-url'), data)
                .done(updateCart)
                .fail(function () {
                    alert("Ошибка обновления корзины. Перезагрузите страницу.");
                    //document.location.reload();
                });
        });

        function updateCart(cartData) {
            updateCartInHeader();
            $me.find('.js-order-amount').html(cartData.amount);

            $.each(cartData.items, function (itemId, itemCost) {
                var selector = '[data-item-id=' + itemId + '] .js-item-cost';

                $me.find(selector).html(itemCost);
            });
        }
    });

    function updateCartInHeader() {
        var $cart = $('.js-cart-in-header');

        $cart.load($cart.data('url'));
    }
});
