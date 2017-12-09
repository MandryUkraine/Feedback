<?php
/**
 * Головний файл з обмеженим доступом
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

use Mandry\Feedback\View;
use Mandry\Feedback\Mapper;
use Mandry\Feedback\Router;
use Mandry\Feedback\Controller;
use Mandry\Feedback\Log;

require_once($private . '/settings.php');

spl_autoload_register('autoload');

$controller = null;

try {

    $view = new View(file_get_contents(_PATH_PRIVATE . '/index.xml'));

    $view->debug->attributes()->time = _TIME;

    $view->debug->attributes()->memory = _MEMORY;

    if (isset($_SESSION['token'])) {

        $token = md5(session_id() . _PASSWORD_HASH);

        if ($_SESSION['token'] == $token) {

            $view->attributes()->login = 1;

        } else {

            Log::append("Неправильний token '$token'");
        }
    }

    define('_LOGIN', ($view->attributes()->login == 1) ? true : false);

    $view->attributes()->debug = (_DEBUG) ? 1 : 0;

    $view->attributes()->recaptcha = _RECAPTCHA_PUBLIC_KEY;

    $mapper = new Mapper($view);

    $router = new Router();

    $controller = new Controller($view, $mapper, $router);

    $controller->run();

} catch (\Exception $exception) {

    /** Додаємо інформацію для відлагодження */

    $view->attributes()->message =

    $view->debug->trace->attributes()->message = $exception->getMessage();

    foreach($exception->getTrace() as $trace) {

        $xmlItem = $view->debug->trace->addChild('item');

        if (isset($trace['file'])) $xmlItem->addAttribute('file', $trace['file']);

        if (isset($trace['line'])) $xmlItem->addAttribute('line', $trace['line']);

        $xmlItem->addAttribute('function', $trace['function']);
    }

    if (is_object($controller)) $controller->notFound();

    Log::append($view->debug->trace->attributes()->message);
}

print $view->getHTML();

/**
 * Створює автозавантажувач об’єктів
 *
 * @param string $object Назва об’єкту
 */
function autoload($object) {

    $object = str_replace('\\', '/', $object);

    $class = _PATH_PRIVATE . '/library/' . $object . '.php';

    require_once($class);
}