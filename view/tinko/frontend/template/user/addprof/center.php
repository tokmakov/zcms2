<?php
/**
 * Форма для добавления нового профиля пользователя,
 * файл view/example/backend/template/catalog/addprof/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены ошибки, мы должны
 * снова предъявить форму, заполненную уже введенными данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/catalog/addprof/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Новый профиль</h1>

<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <ul>
        <?php foreach($errorMessage as $message): ?>
            <li><?php echo $message; ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php
    $title            = ''; // название профиля
    $name             = ''; // имя контактного лица
    $surname          = ''; // фамилия контактного лица
    $email            = ''; // e-mail контактного лица
    $phone            = ''; // телефон контактного лица
    $own_shipping     = 1;  // самовывоз со склада?
    $physical_address = ''; // фактический адрес
    $city             = ''; // город
    $postal_index     = ''; // почтовый индекс
    $legal_person     = 0;  // юридическое лицо?
    $company          = ''; // название компании
    $ceo_name         = ''; // генеральный директор
    $legal_address    = ''; // юридический адрес
    $inn              = ''; // ИНН
    $bank_name        = ''; // название банка
    $bik              = ''; // БИК
    $settl_acc        = ''; // расчетный счет
    $corr_acc         = ''; // корреспондентский счет

    if (isset($savedFormData)) {
        $title            = htmlspecialchars($savedFormData['title']);
        $name             = htmlspecialchars($savedFormData['name']);
        $surname          = htmlspecialchars($savedFormData['surname']);
        $email            = htmlspecialchars($savedFormData['email']);
        $phone            = htmlspecialchars($savedFormData['phone']);
        $own_shipping     = $savedFormData['own_shipping'];
        $physical_address = htmlspecialchars($savedFormData['physical_address']);
        $city             = htmlspecialchars($savedFormData['city']);
        $postal_index     = htmlspecialchars($savedFormData['postal_index']);
        $legal_person     = $savedFormData['legal_person'];
        $company          = htmlspecialchars($savedFormData['company']);
        $ceo_name         = htmlspecialchars($savedFormData['ceo_name']);
        $legal_address    = htmlspecialchars($savedFormData['legal_address']);
        $inn              = htmlspecialchars($savedFormData['inn']);
        $bank_name        = htmlspecialchars($savedFormData['bank_name']);
        $bik              = htmlspecialchars($savedFormData['bik']);
        $settl_acc        = htmlspecialchars($savedFormData['settl_acc']);
        $corr_acc         = htmlspecialchars($savedFormData['corr_acc']);
    }
?>

<form action="<?php echo $action; ?>" method="post">
<div id="add-edit-profile">
    <div>
        <div>
            <div>Название профиля <span class="form-field-required">*</span></div>
            <div><input type="text" name="title" maxlength="32" value="<?php echo $title; ?>" /></div>
        </div>
    </div>

    <div>
        <label><input type="checkbox" name="legal_person" value="1"<?php echo $legal_person ? ' checked="checked"' : ''; ?> /> <span>Юридическое лицо</span></label> <span class="legal_person_help">?</span>
    </div>

    <div id="legal-person">
        <h2>Юридическое лицо</h2>
        <div>
            <div>Название компании <span class="form-field-required">*</span></div>
            <div><input type="text" name="company" maxlength="64" value="<?php echo $company; ?>" /></div>
        </div>
        <div>
            <div>Генеральный директор <span class="form-field-required">*</span></div>
            <div><input type="text" name="ceo_name" maxlength="64" value="<?php echo $ceo_name; ?>" /></div>
        </div>
        <div>
            <div>Юридический адрес <span class="form-field-required">*</span></div>
            <div><input type="text" name="legal_address" maxlength="250" value="<?php echo $legal_address; ?>" /></div>
        </div>
        <div>
            <div>ИНН <span class="form-field-required">*</span></div>
            <div><input type="text" name="inn" maxlength="32" value="<?php echo $inn; ?>" /></div>
        </div>
        <div>
            <div>Название банка <span class="form-field-required">*</span></div>
            <div><input type="text" name="bank_name" maxlength="64" value="<?php echo $bank_name; ?>" /></div>
        </div>
        <div>
            <div>БИК <span class="form-field-required">*</span></div>
            <div><input type="text" name="bik" maxlength="32" value="<?php echo $bik; ?>" /></div>
        </div>
        <div>
            <div>Расчетный счет <span class="form-field-required">*</span></div>
            <div><input type="text" name="settl_acc" maxlength="32" value="<?php echo $settl_acc; ?>" /></div>
        </div>
        <div>
            <div>Корреспондентский счет <span class="form-field-required">*</span></div>
            <div><input type="text" name="corr_acc" maxlength="32" value="<?php echo $corr_acc; ?>" /></div>
        </div>
    </div>

    <div>
        <h2>Контактное лицо</h2>
        <div>
            <div>Фамилия <span class="form-field-required">*</span></div>
            <div><input type="text" name="surname" maxlength="32" value="<?php echo $surname; ?>" /></div>
        </div>
        <div>
            <div>Имя <span class="form-field-required">*</span></div>
            <div><input type="text" name="name" maxlength="32" value="<?php echo $name; ?>" /></div>
        </div>
        <div>
            <div>E-mail <span class="form-field-required">*</span></div>
            <div><input type="text" name="email" maxlength="32" value="<?php echo $email; ?>" /></div>
        </div>
        <div>
            <div>Телефон</div>
            <div><input type="text" name="phone" maxlength="32" value="<?php echo $phone; ?>" /></div>
        </div>
    </div>

    <div>
        <label><input type="checkbox" name="own_shipping" value="1"<?php echo $own_shipping ? ' checked="checked"' : ''; ?> /> <span>Самовывоз со склада</span></label>
    </div>

    <div id="physical-address">
        <h2>Адрес доставки</h2>
        <div>
            <div>Адрес <span class="form-field-required">*</span></div>
            <div><input type="text" name="physical_address" maxlength="250" value="<?php echo $physical_address; ?>" /></div>
        </div>
        <div>
            <div>Город</div>
            <div><input type="text" name="city" maxlength="32" value="<?php echo $city; ?>" /></div>
        </div>
        <div>
            <div>Почтовый индекс</div>
            <div><input type="text" name="postal_index" maxlength="32" value="<?php echo $postal_index; ?>" /></div>
        </div>
    </div>

    <div>
        <div>
            <div></div>
            <div><input type="submit" name="submit" value="Сохранить" /></div>
        </div>
    </div>
</div>
</form>

<!-- Конец шаблона view/example/backend/template/catalog/addprof/center.php -->

