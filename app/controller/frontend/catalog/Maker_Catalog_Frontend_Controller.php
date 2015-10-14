<?php
/**
 * Класс maker_Catalog_Frontend_Controller формирует страницу со списком
 * всех товаров выбранного производителя, получает данные от модели
 * Catalog_Frontend_Model, общедоступная часть сайта
 */
class Maker_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех товаров выбранного производителя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Maker_Catalog_Frontend_Controller
         */
        parent::input();

        // если не передан id производителя или id производителя не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели информацию о производителе
        $maker = $this->catalogFrontendModel->getMaker($this->params['id']);
        // если запрошенный производитель не найден в БД
        if (empty($maker)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $maker['name'] . '. Все изделия производителя. ' . $this->title;

        if (!empty($maker['keywords'])) {
            $this->keywords = $maker['keywords'];
        }
        if (!empty($maker['description'])) {
            $this->description = $maker['description'];
        }

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->catalogFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
            array('url' => $this->catalogFrontendModel->getURL('frontend/catalog/index'), 'name' => 'Каталог'),
            array('url' => $this->catalogFrontendModel->getURL('frontend/catalog/allmkrs'), 'name' => 'Производители'),
        );

        // включена сортировка?
        $sort = 0;
        if (isset($this->params['sort'])
            && ctype_digit($this->params['sort'])
            && in_array($this->params['sort'], array(1,2,3,4,5,6))
        ) {
            $sort = $this->params['sort'];
        }

        // постраничная навигация
        $currentPage = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $currentPage = $this->params['page'];
        }
        // общее кол-во товаров производителя
        $totalProducts = $this->catalogFrontendModel->getCountMakerProducts($this->params['id']);

        $temp = new Pager(
            $currentPage,                                       // текущая страница
            $totalProducts,                                     // общее кол-во товаров производителя
            $this->config->pager->frontend->products->perpage,  // кол-во товаров на странице
            $this->config->pager->frontend->products->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (is_null($pager)) { // недопустимое значение $currentPage (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        if (false === $pager) { // постраничная навигация не нужна
            $pager = null;
        }
        // стартовая позиция для SQL-запроса
        $start = ($currentPage - 1) * $this->config->pager->frontend->products->perpage;

        // получаем от модели массив всех товаров производителя
        $products = $this->catalogFrontendModel->getMakerProducts($this->params['id'], $sort, $start);

        /*
         * Варианты сортировки:
         * 0 - по умолчанию,
         * 1 - по цене, по возрастанию
         * 2 - по цене, по убыванию
         * 3 - по наименованию, по возрастанию
         * 4 - по наименованию, по убыванию
         * 5 - по коду, по возрастанию
         * 6 - по коду, по убыванию
         */
        for ($i = 0; $i <= 6; $i++) {
            $url = 'frontend/catalog/maker/id/' . $this->params['id'];
            if ($i) {
                $url = $url . '/sort/' . $i;
            }
            /*
            if ($currentPage > 1) {
                $url = $url . '/page/' . $currentPage;
            }
            */
            switch ($i) {
                case 0: $name = 'без сортировки';  break;
                case 1: $name = 'цена, возр.';     break;
                case 2: $name = 'цена, убыв.';     break;
                case 3: $name = 'название, возр.'; break;
                case 4: $name = 'название, убыв.'; break;
                case 5: $name = 'код, возр.';      break;
                case 6: $name = 'код, убыв.';      break;
            }
            $sortorders[$i] = array(
                'url' => $this->catalogFrontendModel->getURL($url),
                'name' => $name
            );
        }

        // единицы измерения товара
        $units = $this->catalogFrontendModel->getUnits();

        // URL этой страницы
        $url = 'frontend/catalog/maker/id/' . $this->params['id'];
        if ($sort) {
            $url = $url . '/sort/' . $sort;
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // уникальный идентификатор производителя
            'id'          => $this->params['id'],
            // название производителя
            'name'        => $maker['name'],
            // URL этой страницы
            'thisPageUrl' => $this->catalogFrontendModel->getURL($url),
            // массив товаров производителя
            'products'    => $products,
            // выбранная сортировка
            'sort'        => $sort,
            // массив вариантов сортировки
            'sortorders'  => $sortorders,
            // массив единиц измерения товара
            'units'       => $units,
            // постраничная навигация
            'pager'       => $pager,
            // текущая страница
            'page'        => $currentPage,
        );

    }

}
