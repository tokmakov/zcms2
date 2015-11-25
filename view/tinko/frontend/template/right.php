<?php
/**
 * Правая колонка, файл view/example/frontend/template/right.php,
 * общедоступная часть сайта
 *
 * Переменные, доступные в шаблоне:
 * $basketProducts - массив товаров в корзине
 * $basketTotalCost - общая стоимость товаров в корзине
 * $basketUrl - URL страницы с корзиной
 * $checkoutUrl - URL страницы с формой для оформления заказа
 * $wishedProducts - массив последних 10 отложенных товаров
 * $wishedUrl - URL страницы со списком всех отложенных товаров
 * $comparedProducts - массив последних 10 товаров для сравнения
 * $comparedUrl - URL страницы со списком всех товаров для сравнения
 * $viewedProducts - массив последних 10 просмотренных товаров
 * $viewedUrl - URL страницы со списком всех просмотренных товаров
 *
 * $authUser - пользователь авторизован?
 *  - если авторизован, доступны переменные
 *     $userIndexUrl - URL ссылки на страницу личного кабинета
 *     $userEditUrl - URL ссылки на страницу с формой для редактирования личных данных
 *     $userProfilesUrl - URL ссылки на страницу со списком всех профилей
 *     $userOrdersUrl - URL ссылки на страницу со списком всех заказов
 *     $userLogoutUrl - URL ссылки для выхода из личного кабинета
 *  - если не авторизован, доступны переменные
 *     $action - атрибут action тега form формы для авторизации пользователя
 *     $regFormUrl - URL ссылки на страницу регистрации
 *     $forgotFormUrl - URL ссылки на страницу восстановления пароля
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/right.php -->

<div id="side-login">
    <div class="side-heading">
        <span>
            <i class="fa fa-user"></i>&nbsp;&nbsp;<span>Личный кабинет</span>
        </span>
    </div>
    <div class="side-content">
    <?php if ($authUser): ?>
        <ul id="logged-user-right">
            <li><a href="<?php echo $userIndexUrl; ?>">Личный кабинет</a></li>
            <li><a href="<?php echo $userEditUrl; ?>">Личные данные</a></li>
            <li><a href="<?php echo $userProfilesUrl; ?>">Ваши профили</a></li>
            <li><a href="<?php echo $userOrdersUrl; ?>">История заказов</a></li>
            <li><a href="<?php echo $userLogoutUrl; ?>">Выйти</a></li>
        </ul>
    <?php else: ?>
        <form action="<?php echo $action; ?>" method="post" id="login-user-right">
            <input type="text" name="email" maxlength="32" value="" placeholder="Введите e-mail" />
            <input type="text" name="password" maxlength="32" value="" placeholder="Введите пароль" />
            <label><input type="checkbox" name="remember" value="1" /> <span>запомнить меня</span></label>
            <input type="submit" name="submit" value="Войти" />
        </form>
        <ul id="reg-forgot-right">
            <li><a href="<?php echo $regFormUrl; ?>">Регистрация</a></li>
            <li><a href="<?php echo $forgotFormUrl; ?>">Забыли пароль?</a></li>
        </ul>
    <?php endif; ?>
    </div>
</div>

<div id="side-basket">
    <div class="side-heading">
        <span>
            <i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;<span>Ваша корзина</span>
        </span>
    </div>
    <div class="side-content">
        <div>
        <?php if (!empty($basketProducts)): /* покупательская корзина */ ?>
            <table>
                <tr>
                    <th>Код</th>
                    <th>Наименование</th>
                    <th>Кол.</th>
                </tr>
                <?php foreach ($basketProducts as $item): ?>
                    <tr>
                        <td><a href="<?php echo $item['url']; ?>"><?php echo $item['code']; ?></a></td>
                        <td><?php echo $item['name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="text-align: right;">Итого: <span><?php echo number_format($basketTotalCost, 2, '.', ''); ?></span> руб.</td>
                </tr>
            </table>
            <ul id="goto-basket-checkout">
                <li><a href="<?php echo $basketUrl; ?>">Перейти в корзину</a></li>
                <li><a href="<?php echo $checkoutUrl; ?>">Оформить заказ</a></li>
            </ul>
        <?php else: ?>
            <p class="empty-list-right">Ваша корзина пуста</p>
        <?php endif; ?>
        </div>
    </div>
</div>

<div id="side-wished">
    <div class="side-heading">
        <span>
            <i class="fa fa-star"></i>&nbsp;&nbsp;<span>Избранное</span>
        </span>
    </div>
    <div class="side-content">
        <div>
            <?php if (!empty($wishedProducts)): /* отложенные товары */ ?>
                <table>
                    <tr>
                        <th>Код</th>
                        <th>Наименование</th>
                        <th>Цена</th>
                    </tr>
                    <?php foreach ($wishedProducts as $item): ?>
                        <tr>
                            <td><a href="<?php echo $item['url']; ?>"><?php echo $item['code']; ?></a></td>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo number_format($item['price'], 2, '.', ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <p class="all-products"><a href="<?php echo $wishedUrl; ?>">Все отложенные товары</a></p>
            <?php else: ?>
                <p class="empty-list-right">Нет отложенных товаров</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="side-compared">
    <div class="side-heading">
        <span>
            <i class="fa fa-balance-scale"></i>&nbsp;&nbsp;<span>Сравнение товаров</span>
        </span>
    </div>
    <div class="side-content">
        <div>
        <?php if (!empty($comparedProducts)): /* товары для сравнения */ ?>
            <table>
                <tr>
                    <th>Код</th>
                    <th>Наименование</th>
                    <th></th>
                </tr>
                <?php foreach ($comparedProducts as $item): ?>
                    <tr>
                        <td><a href="<?php echo $item['url']; ?>"><?php echo $item['code']; ?></a></td>
                        <td><?php echo $item['name']; ?></td>
                        <td>
                            <form action="<?php echo $item['action']; ?>" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="submit" title="Удалить из сравнения"><i class="fa fa-times"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <p class="all-products"><a href="<?php echo $comparedUrl; ?>">Перейти к сравнению</a></p>
        <?php else: ?>
            <p class="empty-list-right">Нет товаров для сравнения</p>
        <?php endif; ?>
        </div>
    </div>
</div>

<div id="side-viewed">
    <div class="side-heading">
        <span>
            <i class="fa fa-eye"></i>&nbsp;&nbsp;<span>Вы уже смотрели</span>
        </span>
    </div>
    <div class="side-content">
        <div>
            <?php if (!empty($viewedProducts)): /* просмотренные товары */ ?>
                <table>
                    <tr>
                        <th>Код</th>
                        <th>Наименование</th>
                        <th>Цена</th>
                    </tr>
                    <?php foreach ($viewedProducts as $item): ?>
                        <tr>
                            <td><a href="<?php echo $item['url']; ?>"><?php echo $item['code']; ?></a></td>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo number_format($item['price'], 2, '.', ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <p class="all-products"><a href="<?php echo $viewedUrl; ?>">Все просмотренные товары</a></p>
            <?php else: ?>
                <p class="empty-list-right">Нет просмотренных товаров</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Конец шаблона view/example/frontend/template/right.php -->
