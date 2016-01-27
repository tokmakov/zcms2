$(document).ready(function() {

    /*
     * Форма для редактирования личных данных пользователя
     */
    if ( ! $('#edit-user input[name="change"]').prop('checked')) {
        $('#edit-user .password').hide();
    }
    $('#edit-user input[name="change"]').change(function() {
        $('#edit-user .password').slideToggle();
    });

    /*
     * Форма для добавления/редактирования профиля
     */
    // всплывающее окно с подсказкой для названия профиля
    $('#add-edit-profile #profile-title-help').click(function() {
        $('<div><p>Введите название профиля, например, «ИП&nbsp;Иванов» или «ООО&nbsp;Восход» или «Доставка на Онежскую улицу».</p></div>')
            .prependTo('body')
            .hide()
            .addClass('modal-window')
            .center()
            .fadeIn(500, function() {
                $(this).delay(3000).fadeOut(500, function() {
                    $(this).remove();
                });
            });
    });
    // всплывающее окно с подсказкой для юридического лица
    $('#add-edit-profile #legal-person-help').click(function() {
        $('<div><p>Отметьте флажок, чтобы использовать этот профиль для оформления заказов на юридическое лицо.</p><p>Укажите название компании, юридический адрес, ИНН, название банка, номер расчетного счета.</p></div>')
            .prependTo('body')
            .hide()
            .addClass('modal-window')
            .center()
            .fadeIn(500, function() {
                $(this).delay(3000).fadeOut(500, function() {
                    $(this).remove();
                });
            });
    });
    // если не отмечен checkbox «Юридическое лицо», скрываем часть формы, связанную с юридическим лицом
    if ( ! $('#add-edit-profile input[name="legal_person"]').prop('checked')) {
        $('#add-edit-profile > #legal-person').hide();
    }
    $('#add-edit-profile input[name="legal_person"]').change(function() {
        $('#add-edit-profile > #legal-person').slideToggle();
    });
    // если отмечен checkbox «Самовывоз со склада», скрываем часть формы, связанную с адресом доставки
    if ($('#add-edit-profile input[name="shipping"]').prop('checked')) {
        $('#add-edit-profile > #shipping-address-city-index').hide();
    } else {
        $('#add-edit-profile select[name="office"]').hide();
    }
    $('#add-edit-profile input[name="shipping"]').change(function() {
        $('#add-edit-profile > #shipping-address-city-index').slideToggle('normal', function() {
            $('#add-edit-profile select[name="office"]').toggle();
        });
    });

    /*
     * Добавление товаров ранее сделанного заказа в корзину
     */
    $('#user-orders-list form').ajaxForm({
        target: '#side-basket > .side-content',
        beforeSubmit: function(formData, jqForm, options) {
            var sideBasketHeight = $('#side-basket > .side-content').height()+30/*padding*/;
            var sideBasketWidth = $('#side-basket > .side-content').width();
            $('<div></div>')
                .prependTo('#side-basket > .side-content')
                .addClass('overlay')
                .height(sideBasketHeight)
                .width(sideBasketWidth);
                
            // определаем координаты кнопки «Повторить заказ»
            var button = jqForm.children('input[type="submit"]');
            var buttonTop = Math.round(button.offset().top);
            var buttonLeft = Math.round(button.offset().left);
            // определаем размеры заголовка
            var buttonWidth = Math.round(button.width());
            var buttonHeight = Math.round(button.height());
            // определяем координаты корзины: либо в правой колонке, либо в шапке сайта
            var basket;
            if ($('#side-basket > .side-heading').is(':visible')) {
                basket = $('#side-basket > .side-heading > span > i');
            } else {
                basket = $('#top-menu > a:nth-child(1) > i') ;
            }
            var basketTop = basket.offset().top + 11;
            var basketLeft = basket.offset().left + 9;
            button
                .clone()
                .prependTo(jqForm)
                .css({
                    'position' : 'absolute',
                    'width' : buttonWidth,
                    'height' : buttonHeight,
                    'left' : buttonLeft,
                    'top' : buttonTop,
                    'background' : '#e9751f',
                    'z-index' : 5,
                    'color' : '#fff'
                })
                .delay(200)
                .animate(
                    {left: basketLeft, top: basketTop, width: 0, height: 0},
                    500,
                    function() {
                        // удаляем клона
                        $(this).remove();
                        // показываем окно с сообщением
                        $('<div>Товары добавлены в корзину</div>')
                            .prependTo('body')
                            .hide()
                            .addClass('modal-window')
                            .center()
                            .fadeIn(300, function() {
                                $(this).delay(1000).fadeOut(300, function() {
                                    $(this).remove();
                                });
                            });
                    }
                );
        },
        success: function() {},
        error: function() {
            alert('Ошибка при добавлении товаров в корзину');
        }
    });
});