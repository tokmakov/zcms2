<?php
/**
 * Класс Allprof_User_Frontend_Controller формирует страницу со списком всех профилей
 * пользователя (если он авторизован) или перенаправляет на страницу авторизации, получает
 * данные от модели User_Frontend_Model, общедоступная часть сайта
 */
class Allprof_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех профилей пользователя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Allprof_User_Frontend_Controller
         */
        parent::input();

        // если пользователь не авторизован, перенаправляем его на страницу авторизации
        if (!$this->authUser) {
            $this->redirect($this->userFrontendModel->getURL('frontend/user/login'));
        }

        $this->title = 'Личный кабинет, профили. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->userFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
            array('url' => $this->userFrontendModel->getURL('frontend/user/index'), 'name' => 'Личный кабинет')
        );

        // получаем от модели массив профилей пользователя
        $profiles = $this->userFrontendModel->getAllProfiles();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs' => $breadcrumbs, // хлебные крошки
            'profiles' => $profiles, // массив профилей пользователя
            // URL ссылки для добавления нового профиля
            'addProfileUrl' => $this->userFrontendModel->getURL('frontend/user/addprof'),
        );

    }

}
