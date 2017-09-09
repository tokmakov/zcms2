<?php
/**
 * Класс Category_Catalog_Frontend_Controller формирует страницу категории каталога,
 * т.е. список дочерних категорий и список товаров категории, получает данные от
 * модели Category_Catalog_Frontend_Model, общедоступная часть сайта
 */
class Category_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * категории каталога, т.е. список дочерних категорий, форма фильтров и список
     * товаров категории с постраничной навигацией
     */
    protected function input() {

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        /*
         * Если у пользователя отключен JavaScript, данные формы фильтров отправляются без
         * использования XmlHttpRequest, пользователь просто выбирает нужные фильтры и жмет
         * кнопку «Применить». Здесь мы обрабатываем эти данные и делаем редирект на эту же
         * страницу, но уже с параметрами формы в URL. В обычной ситуации, когда данные формы
         * отправляются по событию change элементов формы с использованием XmlHttpRequest,
         * данные обрабатывает класс контроллера Xhr_Category_Catalog_Frontend_Controller, см.
         * файл app/controller/frontend/calalog/Xhr_Category_Catalog_Frontend_Controller.php
         */
        if ($this->isPostMethod()) {
            $this->processFormData();
        }

        /*
         * получаем от модели данные, необходимые для формирования страницы категории, и
         * записываем их в массив переменных, который будет передан в шаблон center.php
         */
        $this->getCategory();
        // товар не найден в таблице БД categories
        if ($this->notFoundRecord) {
            return;
        }

        // переопределяем переменную, которая будет передана в шаблон left.php,
        // чтобы раскрыть ветку текущей категории меню каталога в левой колонке
        $this->leftVars['catalogMenu'] = $this->menuCatalogFrontendModel->getCatalogMenu($this->params['id']);

    }

    /**
     * Функция получает от модели данные о категории и сохраняет их в массиве,
     * который будет передан в шаблон center.php
     */
    private function getCategory() {

        // получаем от модели информацию о категории
        $category = $this->categoryCatalogFrontendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * обращаемся к родительскому классу Catalog_Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Category_Catalog_Frontend_Controller
         */
        parent::input();

        $this->title = $category['name'];

        if ( ! empty($category['keywords'])) {
            $this->keywords = $category['keywords'];
        }
        if ( ! empty($category['description'])) {
            $this->description = $category['description'];
        }

        // формируем хлебные крошки
        $breadcrumbs = $this->categoryCatalogFrontendModel->getCategoryPath($this->params['id']); // путь до категории
        array_pop($breadcrumbs); // последний элемент - текущая категория, нам она не нужна

        // включен фильтр по функционалу (функциональной группе)?
        $group = 0;
        if (isset($this->params['group']) && ctype_digit($this->params['group'])) {
            $group = (int)$this->params['group'];
        }

        // включен фильтр по производителю?
        $maker = 0;
        if (isset($this->params['maker']) && ctype_digit($this->params['maker'])) {
            $maker = (int)$this->params['maker'];
        }

        // включены доп.фильтры (параметры подбора для выбранного функционала)?
        $param = array();
        if ($group && isset($this->params['param']) && preg_match('~^\d+\.\d+(-\d+\.\d+)*$~', $this->params['param'])) {
            $temp = explode('-', $this->params['param']);
            foreach ($temp as $item) {
                $tmp = explode('.', $item);
                $key = (int)$tmp[0];
                $value = (int)$tmp[1];
                $param[$key] = $value;
            }
            // проверяем корректность переданных параметров и значений
            if ( ! $this->categoryCatalogFrontendModel->getCheckParams($param)) {
                $this->notFoundRecord = true;
                return;
            }
        }

        // включен фильтр по лидерам продаж?
        $hit = 0;
        if (isset($this->params['hit']) && 1 == $this->params['hit']) {
            $hit = 1;
        }

        // включен фильтр по новинкам?
        $new = 0;
        if (isset($this->params['new']) && 1 == $this->params['new']) {
            $new = 1;
        }

        // включена сортировка?
        $sort = 0;
        if (isset($this->params['sort'])
            && ctype_digit($this->params['sort'])
            && in_array($this->params['sort'], array(1,2,3,4,5,6))
        ) {
            $sort = (int)$this->params['sort'];
        }

        // кол-во товаров на одной странице
        $perpage = $this->config->pager->frontend->products->perpage;
        $others = $this->config->pager->frontend->products->getValue('others'); // другие доступные варианты
        if (isset($this->params['perpage']) && in_array($this->params['perpage'], $others)) {
            $perpage = (int)$this->params['perpage'];
        }

        // мета-тег robots
        if ($group || $maker || $hit || $new || $sort) {
            $this->robots = false;
        }

        /*
         * Получаем от модели массив дочерних категорий с учетом фильтров по функционалу,
         * производителю, новинкам и лидерам продаж, параметрам подбора для выбранного
         * функционала. Массив кроме идентификатора категории и наименования содержит
         * информацию о количестве товаров в каждой дочерней категории (с учетом фильтров)
         * и URL категории. При переходе на страницу дочерней категории сохраняются все
         * фильтры, которые пользователь применил к текущей категории. Кроме того, если у
         * дочерней категории все товары принадлежат одной функциональной группе, сразу
         * включается фильтр по функционалу. Чтобы при переходе в эту категорию сразу стали
         * доступны параметры подбора (без выбора единственной функциональной группы из
         * выпадающего списка).
         *
         * $categories = Array (
         *   [0] => Array (
         *     [id] => 30
         *     [name] => Извещатели тепловые максимальные
         *     [count] => 0
         *     [url] => //www.host.ru/catalog/category/30/group/459/maker/59
         *   )
         *   [1] => Array (
         *     [id] => 31
         *     [name] => Извещатели тепловые максимально-дифференциальные
         *     [count] => 0
         *     [url] => //www.host.ru/catalog/category/31/maker/59
         *   )
         *   [2] => Array (
         *     [id] => 33
         *     [name] => Извещатели дымовые
         *     [count] => 2
         *     [url] => //www.host.ru/catalog/category/33/maker/59
         *   )
         *   ..........
         * )
         */
        $categories = $this->categoryCatalogFrontendModel->getChildCategories(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param,              // массив параметров подбора
            $sort,               // сортировка
            $perpage             // кол-во товаров на одной странице
        );

        // получаем от модели массив функциональных групп
        $groups = $this->categoryCatalogFrontendModel->getCategoryGroups(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );

        // получаем от модели массив производителей
        $makers = $this->categoryCatalogFrontendModel->getCategoryMakers(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );

        // получаем от модели массив доп.фильтров (параметров подбора для выбранного функционала)
        $params = $this->categoryCatalogFrontendModel->getCategoryGroupParams(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );

        // получаем от модели количество лидеров продаж
        $countHit = $this->categoryCatalogFrontendModel->getCountCategoryHit(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );

        // получаем от модели количество новинок
        $countNew = $this->categoryCatalogFrontendModel->getCountCategoryNew(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) { // текущая страница
            $page = (int)$this->params['page'];
        }
        // общее кол-во товаров категории
        $totalProducts = $this->categoryCatalogFrontendModel->getCountCategoryProducts(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );
        $pager = null; // постраничная навигация
        $start = 0;    // стартовая позиция для SQL-запроса
        if ($totalProducts > $this->config->pager->frontend->products->perpage) { // постраничная навигация нужна?
            // URL этой страницы
            $thisPageURL = $this->categoryCatalogFrontendModel->getCategoryURL(
                $this->params['id'], // уникальный идентификатор категории
                $group,              // идентификатор функциональной группы или ноль
                $maker,              // идентификатор производителя или ноль
                $hit,                // включен или нет фильтр по лидерам продаж
                $new,                // включен или нет фильтр по новинкам
                $param,              // массив параметров подбора
                $sort,               // сортировка
                $perpage             // кол-во товаров на странице
            );
            $temp = new Pager(
                $thisPageURL,                                       // URL этой страницы
                $page,                                              // текущая страница
                $totalProducts,                                     // общее кол-во товаров категории
                $perpage,                                           // кол-во товаров на странице
                $this->config->pager->frontend->products->leftright // кол-во ссылок слева и справа
            );
            $pager = $temp->getNavigation();
            if (false === $pager) { // недопустимое значение $page (за границей диапазона)
                $this->notFoundRecord = true;
                return;
            }
            // стартовая позиция для SQL-запроса
            $start = ($page - 1) * $perpage;
        }

        /*
         * получаем от модели массив товаров категории в кол-ве $perpage, начиная с
         * позации $start с учетом фильтров по функционалу, производителю, параметрам
         * подбора, лидерам продаж и новинкам
         */
        $products = $this->categoryCatalogFrontendModel->getCategoryProducts(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param,              // массив параметров подбора
            $sort,               // сортировка
            $start,              // стартовая позиция для SQL-запроса
            $perpage             // кол-во товаров на странице
        );

        // ссылки для сортировки товаров по цене, наменованию, коду
        $sortorders = $this->categoryCatalogFrontendModel->getCategorySortOrders(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param,              // массив параметров подбора
            $perpage             // кол-во товаров на странице
        );

        // ссылки для переключения на показ 10,20,50,100 товаров на страницу
        $perpages = $this->categoryCatalogFrontendModel->getOthersPerPage(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param,
            $sort,
            $perpage
        );

        // единицы измерения товара
        $units = $this->categoryCatalogFrontendModel->getUnits();

        // атрибут action формы фильтров для товаров категории
        $action = $this->categoryCatalogFrontendModel->getURL(
            'frontend/catalog/category/id/' . $this->params['id']
        );

        // URL ссылки для сборса фильтра
        $url = 'frontend/catalog/category/id/' . $this->params['id'];
        if ($sort) {
            $url = $url . '/sort/' . $sort;
        }
        if ($perpage !== $this->config->pager->frontend->products->perpage) {
            $url = $url . '/perpage/' . $perpage;
        }
        $clearFilterURL = $this->categoryCatalogFrontendModel->getURL($url);

        // представление списка товаров: линейный или плитка
        $view = 'line';
        if (isset($_COOKIE['view']) && $_COOKIE['view'] == 'grid') {
            $view = 'grid';
        }

        // выбранный вариант кол-ва товаров на странице или ноль — если значение по умолчанию
        $perpage = ($perpage === $this->config->pager->frontend->products->perpage) ? 0 : $perpage;

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs'    => $breadcrumbs,        // хлебные крошки
            'id'             => $this->params['id'], // уникальный идентификатор категории
            'name'           => $category['name'],   // наименование категории
            'categories'     => $categories,         // массив дочерних категорий
            'action'         => $action,             // атрибут action тега форм
            'view'           => $view,               // представление списка товаров
            'group'          => $group,              // id выбранной функциональной группы или ноль
            'maker'          => $maker,              // id выбранного производителя или ноль
            'param'          => $param,              // массив выбранных параметров подбора
            'hit'            => $hit,                // показывать только лидеров продаж?
            'countHit'       => $countHit,           // количество лидеров продаж
            'new'            => $new,                // показывать только новинки?
            'countNew'       => $countNew,           // количество новинок
            'groups'         => $groups,             // массив функциональных групп
            'makers'         => $makers,             // массив производителей
            'params'         => $params,             // массив всех параметров подбора
            'sort'           => $sort,               // выбранная сортировка или ноль
            'sortorders'     => $sortorders,         // массив вариантов сортировки
            'perpage'        => $perpage,            // выбранный вариант кол-ва товаров на странице или ноль
            'perpages'       => $perpages,           // массив всех вариантов кол-ва товаров на страницу
            'units'          => $units,              // массив единиц измерения товара
            'products'       => $products,           // массив товаров категории
            'clearFilterURL' => $clearFilterURL,     // URL ссылки для сброса фильтра
            'pager'          => $pager,              // постраничная навигация
            'page'           => $page,               // текущая страница
        );

    }

    /**
     * Вспомогательная функция, обрабатывает отправленные данные формы фильтров в том
     * случае, если у посетителя отключен JavaScript, после чего делает редирект на
     * эту же страницу, но уже с фильтрами в URL
     */
    private function processFormData() {

        // базовый URL категории, без фильтров и сортировки
        $url = 'frontend/catalog/category/id/' . $this->params['id'];
        // включен фильтр по функционалу (функциональной группе)?
        $grp = false;
        if (isset($_POST['group']) && ctype_digit($_POST['group'])  && $_POST['group'] > 0) {
            $url = $url . '/group/' . $_POST['group'];
            $grp = true;
        }
        // включен фильтр по производителю?
        if (isset($_POST['maker']) && ctype_digit($_POST['maker'])  && $_POST['maker'] > 0) {
            $url = $url . '/maker/' . $_POST['maker'];
        }
        // включен фильтр по лидерам продаж?
        if (isset($_POST['hit'])) {
            $url = $url . '/hit/1';
        }
        // включен фильтр по новинкам?
        if (isset($_POST['new'])) {
            $url = $url . '/new/1';
        }
        // включены доп.фильтры (параметры подбора для выбранного функционала)?
        if ($grp && isset($_POST['param']) && is_array($_POST['param'])) {
            $param = array();
            foreach ($_POST['param'] as $key => $value) {
                if ($key > 0 && ctype_digit($value) && $value > 0) {
                    $param[] = $key . '.' . $value;
                }
            }
            if ( ! empty($param)) {
                $url = $url . '/param/' . implode('-', $param);
            }
        }
        // включена сортировка?
        if (isset($_POST['sort'])
            && ctype_digit($_POST['sort'])
            && in_array($_POST['sort'], array(1,2,3,4,5,6))
        ) {
            $url = $url . '/sort/' . $_POST['sort'];
        }
        // кол-во товаров на странице
        if (isset($_POST['perpage']) && ctype_digit($_POST['parpage'])) { // TODO: in_array 20, 50, 100
            $url = $url . '/perpage/' . $_POST['perpage'];
        }
        // выполняем редирект
        $this->redirect($this->categoryCatalogFrontendModel->getURL($url));

    }

}
