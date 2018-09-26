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
});
