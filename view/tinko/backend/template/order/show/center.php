<?php
/**
 * Подробная информация об отдельном заказе в магазине,
 * файл view/example/frontend/template/order/show/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $order - подробная информация о заказе
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/order/show/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Заказ № <?php echo $order['order_id']; ?></h1>

<?php if (!empty($order['user_name'])): ?>
    <ul>
        <li>Имя: <?php echo $order['user_name']; ?></li>
        <li>Фамилия: <?php echo $order['user_surname']; ?></li>
        <li>E-mail: <?php echo $order['user_email']; ?></li>
    </ul>
<?php else: ?>
    <p>Незарегистрированный пользователь</p>
<?php endif; ?>

<table class="data-table">
    <tr>
        <th width="10%">Код</th>
        <th width="20%">Наименование</th>
        <th width="40%">Функциональное наименование</th>
        <th width="10%">Кол.</th>
        <th width="10%">Цена</th>
        <th width="10%">Стоим.</th>
    </tr>
    <?php foreach ($order['products'] as $product): ?>
       <tr>
           <td><?php echo $product['code']; ?></td>
           <td><?php echo $product['name']; ?></td>
           <td><?php echo $product['title']; ?></td>
           <td><?php echo $product['quantity']; ?></td>
           <td><?php echo number_format($product['price'], 2, '.', ''); ?></td>
           <td><?php echo number_format($product['cost'], 2, '.', ''); ?></td>
       </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="5" align="right">Итого:</td>
        <td><?php echo number_format($order['amount'], 2, '.', ''); ?></td>
    </tr>
</table>

<h2>Получатель</h2>
<ul>
    <li>Имя: <?php echo $order['recipient_name']; ?></li>
    <li>Фамилия: <?php echo $order['recipient_surname']; ?></li>
    <li>E-mail: <?php echo $order['recipient_email']; ?></li>
    <li>Телефон: <?php echo $order['recipient_phone']; ?></li>
</ul>
<ul>
<?php if (!$order['own_shipping']): ?>
    <li>Адрес доставки: <?php echo $order['recipient_physical_address']; ?></li>
    <li>Город: <?php echo $order['recipient_city']; ?></li>
    <li>Почтовый индекс: <?php echo $order['recipient_postal_index']; ?></li>
<?php else: ?>
    <li>Самовывоз со склада</li>
<?php endif; ?>
</ul>
<?php if ($order['recipient_legal_person']): ?>
    <ul>
        <li>Название компании: <?php echo $order['recipient_company']; ?></li>
        <li>Генеральный директор: <?php echo $order['recipient_ceo_name']; ?></li>
        <li>Юридический адрес: <?php echo $order['recipient_legal_address']; ?></li>
        <li>ИНН: <?php echo $order['recipient_inn']; ?></li>
        <li>Название банка: <?php echo $order['recipient_bank_name']; ?></li>
        <li>БИК: <?php echo $order['recipient_bik']; ?></li>
        <li>Расчетный счет: <?php echo $order['recipient_settl_acc']; ?></li>
        <li>Корреспондентский счет: <?php echo $order['recipient_corr_acc']; ?></li>
    </ul>
<?php endif; ?>


<?php if ($order['recipient_payer_different']): ?>
    <h2>Плательщик</h2>
    <ul>
        <li>Имя: <?php echo $order['payer_name']; ?></li>
        <li>Фамилия: <?php echo $order['payer_surname']; ?></li>
        <li>E-mail: <?php echo $order['payer_email']; ?></li>
        <li>Телефон: <?php echo $order['payer_phone']; ?></li>
    </ul>
    <?php if ($order['payer_legal_person']): ?>
        <ul>
            <li>Название компании: <?php echo $order['payer_company']; ?></li>
            <li>Генеральный директор: <?php echo $order['payer_ceo_name']; ?></li>
            <li>Юридический адрес: <?php echo $order['payer_legal_address']; ?></li>
            <li>ИНН: <?php echo $order['payer_inn']; ?></li>
            <li>Название банка: <?php echo $order['payer_bank_name']; ?></li>
            <li>БИК: <?php echo $order['payer_bik']; ?></li>
            <li>Расчетный счет: <?php echo $order['payer_settl_acc']; ?></li>
            <li>Корреспондентский счет: <?php echo $order['payer_corr_acc']; ?></li>
        </ul>
    <?php endif; ?>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/order/show/center.php -->
