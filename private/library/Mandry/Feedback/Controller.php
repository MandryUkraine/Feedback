<?php
/**
 * Контролер
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

namespace Mandry\Feedback;

class Controller {

    /** @var Router Маршрутизатор */
    protected $router;

    /** @var Mapper Мапер БД */
    protected $mapper;

    /** @var View Вивід */
    protected $view;

    /** @var integer Номер сторінки переліку */
    protected $page = 1;

    /** @var integer Кількість сторінок в пагінації */
    protected $paginationPages = 7;

    /** @var string Поле та напрям сортування */
    protected $order;

    /** @var string Поле сортування */
    protected $orderField = 'date';

    /** @var boolean Напрям сортування */
    protected $orderDirection = true;

    /** @var array Варіанти сортування */
    protected $orderList = array(
        'Дата (зростання)'  => 'date-asc',
        'Дата (спадання)'   => 'date-desc',
        'Ім\'я (зростання)' => 'name-asc',
        'Ім\'я (спадання)'  => 'name-desc',
        'Email (зростання)' => 'email-asc',
        'Email (спадання)'  => 'email-desc'
    );

    /**
     * Конструктор
     *
     * @param View $view Вивід
     * @param Mapper $mapper Мапер БД
     * @param Router $router Маршрутизатор
     */
    public function __construct(View $view, Mapper $mapper, Router $router) {

        $this->view = $view;

        $this->mapper = $mapper;

        $this->router = $router;

        if ($this->router->getURI() == 'перелік')

            if (!is_null($this->router->getURI(1)))

                $this->page = $this->router->getURI(1);

        if (isset($_GET['сортування'])) {

            $this->order = $_GET['сортування'];

            $order = explode('-', $this->order);

            if (count($order) == 2) {

                $this->orderField = $order[0];

                $this->orderDirection = ($order[1] == 'asc') ? true : false;
            }
        }
    }

    /**
     * Головний метод контролера
     *
     * @throws Exception Невідома сторінка
     */
    public function run() {

        if (method_exists($this, $this->router->getAction())) {

            call_user_func(array($this, $this->router->getAction()));

        } else {

            throw new Exception(

                sprintf("Невідома сторінка '%s'", $this->router->getURI())
            );
        }
    }

    /**
     * Перелік повідомлень
     *
     * @throws Exception Помилка запиту
     */
    public function listAction() {

        $title = 'Відгуки та пропозиції';

        $description = 'На цій сторінці ви можете залишити свій відгук чи пропозицію.';

        $description .= ' Для нас важлива Ваша думка';

        $this->view['title'] = $title . ' - Mandry';

        $this->view['description'] = $description;

        $this->view['keywords'] = 'відгуки, пропозиції';

        $mainNode = $this->view->addChild('main');

        $mainNode->addAttribute('title', $title);

        if (_LOGIN) $mainNode->addAttribute('subtitle', 'редагування');

        $listNode = $mainNode->addChild('list');

        $messageRepository = new Repository($this->mapper);

        $messageRepository->setPage($this->page);

        if (isset($this->order)) {

            $messageRepository->setOrderField($this->orderField);

            $messageRepository->setOrderDirection($this->orderDirection);
        }

        $messageCollection = $messageRepository->getList();

        if ($messageCollection === false) return false;

        $messagesNode = $listNode->addChild('messages');

        foreach($messageCollection as $messageEntity) {

            $messageNode = $messagesNode->addChild('message');

            $messageNode->addAttribute('id', $messageEntity->getID());

            $messageNode->addAttribute('date', $messageEntity->getDate());

            $messageNode->addAttribute('name', $messageEntity->getName());

            $email = str_replace('@', ' # ', $messageEntity->getEmail());

            $messageNode->addAttribute('email', $email);

            $messageNode->addAttribute('site', $messageEntity->getSite());

            $messageNode->addAttribute('text', $messageEntity->getText());

            $messageNode->addAttribute('ip', $messageEntity->getIP());

            $messageNode->addAttribute('browser', $messageEntity->getBrowser());
        }

        $this->pagination($listNode, $messageRepository->getPages());

        $orderNode = $mainNode->addChild('order');

        foreach ($this->orderList as $name => $value) {

            $itemNode = $orderNode->addChild('item');

            $itemNode->addAttribute('name', $name);

            $itemNode->addAttribute('value', $value);

            if (isset($_GET['сортування']) && ($value == $_GET['сортування']))

                $itemNode->addAttribute('selected', 'selected');
        }

        return true;
    }

    /**
     * Отримання повідомлення
     */
    public function getAction() {

        try {

            $messageRepository = new Repository($this->mapper);

            $messageEntity = $messageRepository->get($this->getID());

            $data = array(
                'id'        => $messageEntity->getID(),
                'date'      => $messageEntity->getDate(),
                'name'      => $messageEntity->getName(),
                'email'     => $messageEntity->getEmail(),
                'site'      => $messageEntity->getSite(),
                'text'      => $messageEntity->getText(),
                'ip'        => $messageEntity->getIP(),
                'browser'   => $messageEntity->getBrowser(),
            );

            $this->response(true, $data);

        } catch (Exception $exception) {

            $this->response(false, $exception->getMessage());
        }
    }

    /**
     * Збереження повідомлення
     */
    protected function setAction() {

        $ip = System::getIP();

        $message = array(
            'name'      => $_POST['name'],
            'email'     => $_POST['email'],
            'site'      => $_POST['site'],
            'text'      => $_POST['text']
        );

        try {

            if (_LOGIN) {

                $message['id'] = $this->router->getURI(1);

            } else {

                $message['date'] = date('Y-m-d H:i:s');

                $message['ip'] = $ip;

                $message['browser'] = $_SERVER['HTTP_USER_AGENT'];

                $recaptcha = new Recaptcha();

                if (!$recaptcha->validate($_POST['g-recaptcha-response'], $ip)) {

                    $exception = sprintf('Помилка каптчі (%s)', $recaptcha->getErrors());

                    throw new Exception($exception);
                }

            }

            $messageEntity = new Entity($message);

            $messageRepository = new Repository($this->mapper);

            $messageRepository->set($messageEntity);

            $this->response(true, $message);

        } catch (Exception $exception) {

            $this->response(false, $exception->getMessage());
        }
    }

    /**
     * Видалення повідомлення
     *
     * @throws Exception Дія заборонена
     */
    public function deleteAction() {

        if (!_LOGIN) throw new Exception('Дія заборонена');

        try {

            $messageRepository = new Repository($this->mapper);

            $messageRepository->delete($this->getID());

            $this->response(true);

        } catch (Exception $exception) {

            $this->response(false, $exception->getMessage());
        }
    }

    /**
     * Авторизація
     */
    public function loginAction() {

        $title = 'Авторизація';

        $this->view['title'] = $title . ' - Mandry';

        $mainNode = $this->view->addChild('main');

        $mainNode->addAttribute('title', $title);

        try {

            if (_LOGIN) throw new Exception('Ви вже авторизовані');

            $mainNode->addChild('login');

            if (!isset($_POST['submit'])) return;

            $recaptcha = new Recaptcha();

            if (!$recaptcha->validate($_POST['g-recaptcha-response'], System::getIP())) {

                $exception = sprintf("Помилка каптчі '%s'", $recaptcha->getErrors());

                throw new Exception($exception);
            }

            if (!isset($_POST['login']) || (strlen($_POST['login']) == 0))

                throw new Exception("Відсутній логін");

            if ($_POST['login'] != _USER)

                throw new Exception(sprintf("Неправильний логін '%s'", $_POST['login']));

            if (!isset($_POST['password']) || (strlen($_POST['password']) == 0))

                throw new Exception("Відсутній пароль");

            if (md5($_POST['password']) != _PASSWORD_HASH)

                throw new Exception("Неправильний пароль");

            $_SESSION['token'] = md5(session_id() . _PASSWORD_HASH);

            $this->redirect();

        } catch (Exception $exception) {

            $this->view['message'] = $exception->getMessage();
        }
    }

    /**
     * Вихід
     */
    public function logoutAction() {

        unset($_SESSION['token']);

        $this->redirect();
    }

    /**
     * Отримує, перевіряє та повертає ідентифікатор повідомлення з адреси сторінки
     *
     * @param boolean $isRequired Ознака обовязковості ідентифікатора
     * @throws Exception Відсутній ідентифікатор повідомлення
     * @throws Exception Помилковий ідентифікатор повідомлення
     * @return integer Ідентифікатор повідомлення
     */
    protected function getID($isRequired = true) {

        $id = $this->router->getURI(1);

        if ($isRequired) {

            if (!isset($id))

                throw new Exception('Відсутній ідентифікатор повідомлення');

            if (!preg_match('/^\d{1,10}$/', $id))

                throw new Exception('Помилковий ідентифікатор повідомлення');
        }

        return $id;
    }

    /**
     * Повертає відповідь для ajax-запиту
     *
     * @param boolean $status Ознака успішності виконання запиту
     * @param array|string $params Перелік для повернення або текст повідомлення про помилку
     */
    protected function response($status, $params = null) {

        if ($status) {

            $response['status'] = 1;

            if (isset($params)) $response['data'] = $params;

        } else {

            $response['status'] = 0;

            if (isset($params)) $response['error'] = $params;
        }

        exit(json_encode($response));
    }

    /**
     * Створює пагінацію
     *
     * @param \SimpleXMLElement $node Елемент виводу, куди потрібно додати пагінацію
     * @param integer $pages Загальна кількість сторінок
     */
    protected function pagination(\SimpleXMLElement $node, $pages) {

        if ($pages == 1) return;

        $pagination = range(1, $pages);

        if ((floor($this->paginationPages / 2) * 2 + 1) >= 1) {

            $paginationMin = count($pagination) - $this->paginationPages;

            $paginationMax = intval($this->page) - ceil($this->paginationPages/2);

            $paginationOffset = max(0, min($paginationMin, $paginationMax));

            $pagination = array_slice($pagination, $paginationOffset, $this->paginationPages);
        }

        $paginationNode = $node->addChild('pagination');

        foreach($pagination AS $page2) {

            $pageNode = $paginationNode->addChild('page');

            if ($page2 == $this->page) $pageNode->addAttribute('active', null);

            $pageNode->addAttribute('title', 'Page '.$page2);

            $pageNode->addAttribute('value', $page2);

            $uri = '/';

            if ($page2 > 1) $uri .= $this->router->getAlias() . '/' . $page2;

            if (isset($this->order)) $uri .= '?сортування=' . $this->order;

            $pageNode->addAttribute('uri', $uri);
        }

        if ($this->page > 1) {

            $page = ' (Сторінка #' . $this->page . ')';

            $title = $this->view['title'] . $page;

            $this->view['title'] = $title;

            $this->view['description'] .= $page . '.';
        }
   }

    /**
     * Здійснює переадресацію
     *
     * @param   string $uri Адреса сторінки сайту, на яку потрібно переадресувати
     * @param   integer $code Код переадресації HTTP-протоколу
     */
    protected function redirect($uri = '', $code = 303) {

        header(sprintf("HTTP/1.x %d See Other", $code));

        header(sprintf("Location: http://%s/%s", _HOST, $uri));

        exit(303);
    }

    /**
     * Виводить сторінку з 404-ю помилкою
     */
    public function notFound() {

        $mainNode = $this->view->addChild('main');

        $mainNode->addChild('notFound');

        $this->view['title'] = 'Сторінка не знайдена';

        $description = 'Сторінка "%s" відсутня на нашому сайті';

        $this->view['description'] = sprintf($description, $_SERVER['QUERY_STRING']);

        $this->view['keywords'] = 'Сторінка не знайдена';

        header('HTTP/1.x 404 Not Found');
    }
}