<?php
/**
 * Головний файл в загальному доступі
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

/** Час початку виконання скриптів */
define('_TIME', microtime(true));

/** Кількість використовуваної пам’яті на початку виконання скриптів */
define('_MEMORY', memory_get_usage());

session_start();

set_error_handler('exceptionErrorHandler');

setlocale(LC_ALL, 'uk_UA.utf8');

mb_internal_encoding('UTF-8');

header('Content-Type: text/html; charset=utf-8');

$_SERVER['REQUEST_URI'] = urldecode($_SERVER['REQUEST_URI']);

$root = realpath( __DIR__ . '/..');

$private = $root . '/private';

require_once($private . '/index.php');

/**
 * Перетворює помилки у винятки
 *
 * @param string $number Номер помилки
 * @param string $string Опис помилки
 * @param string $file Файл, в якому виникла помилка
 * @param string $line Рядок файлу, в якому виникла помилка
 * @throws ErrorException
 */
function exceptionErrorHandler($number, $string, $file, $line) {

    throw new \ErrorException($string, 0, $number, $file, $line);
}