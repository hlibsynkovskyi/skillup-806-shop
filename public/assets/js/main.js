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
                data = {},
                value = parseInt($input.val()),
                min = parseInt($input.attr('min')),
                max = parseInt($input.attr('max'));

            if ($input.val() === '') {
                value = min;
            } else {
                if (isNaN(value) || value < min) {
                    value = min;
                } else if (value > max) {
                    value = max;
                }

                $input.val(value);
            }

            data[$input.attr('name')] = value;
            $.post($input.data('update-url'), data)
                .done(updateCart)
                .fail(function () {
                    alert("Ошибка обновления корзины. Перезагрузите страницу.");
                    //document.location.reload();
                });
        });

        $me.find('.js-remove-item').on('click', function(event) {
            var $a = $(this);

            event.preventDefault();

            if (confirm('Действительно хотите удалить товар из корзины?')) {
                $a.closest('tr').remove();
                $.post(this.href).done(updateCart);
            }
        });

        function updateCart(cartData) {
            if (cartData.isEmpty) {
                location.reload();

                return;
            }

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
