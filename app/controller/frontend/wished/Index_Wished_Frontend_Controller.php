<?php
/**
 * Класс Index_Wished_Frontend_Controller формирует страницу со списком всех
 * отложенных посетителем товаров, получает данные от модели Wished_Frontend_Model,
 * общедоступная часть сайта
 */
class Index_Wished_Frontend_Controller extends Wished_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех отложенных посетителем товаров
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Wished_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Wished_Frontend_Controller
         */
        parent::input();

        $this->title = 'Отложенные товары. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->wishedFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
            array('url' => $this->wishedFrontendModel->getURL('frontend/catalog/index'), 'name' => 'Каталог'),
        );

        // постраничная навигация
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = $this->params['page'];
        }
        // общее кол-во отложенных товаров
        $totalProducts = $this->wishedFrontendModel->getCountWishedProducts();

        $temp = new Pager(
            $page,                                              // текущая страница
            $totalProducts,                                     // общее кол-во товаров
            $this->config->pager->frontend->products->perpage,  // кол-во товаров на странице
            $this->config->pager->frontend->products->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (is_null($pager)) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        if (false === $pager) { // постраничная навигация не нужна
            $pager = null;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->frontend->products->perpage;

        // получаем от модели массив отложенных посетителем товаров
        $wishedProducts = $this->wishedFrontendModel->getWishedProducts($start);

        // единицы измерения товара
        $units = $this->catalogFrontendModel->getUnits();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'    => $breadcrumbs,
            // URL ссылки на эту страницу
            'thisPageUrl'    => $this->wishedFrontendModel->getURL('frontend/wished/index'),
            // массив отложенных товаров
            'wishedProducts' => $wishedProducts,
            // массив единиц измерения товара
            'units'          => $units,
            // постраничная навигация
            'pager'          => $pager,
            // текущая страница
            'page'           => $page,
        );

    }

}
