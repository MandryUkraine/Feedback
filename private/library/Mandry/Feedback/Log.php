<?php
/**
 * Лог
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

namespace Mandry\Feedback;

class Log {

    /** @var string Назва файлу лога */
    protected static $file = 'exceptions.log';

    /**
     * Додає запис в файл лога
     *
     * @param   string $message Повідомлення, що повинно записатись в лог-файл
     * @return  boolean Результат виконання операції
     */
    public static function append($message) {

        $file = _PATH_PRIVATE . '/' . static::$file;

        $string['time'] = date('Y-m-d H:i:s');

        $string['ip'] = sprintf("[%21s]", System::getIP() . ':' . $_SERVER['REMOTE_PORT']);

        $string['description'] = '"' . $message . '"';

        if (isset($_SERVER['HTTP_USER_AGENT']))

            $string['agent'] = '"' . $_SERVER['HTTP_USER_AGENT'] . '"';

        $string = implode('  ', $string) . "\n";

        $result = file_put_contents($file, $string, FILE_APPEND | LOCK_EX);

        return ($result !== false) ? true : false;
    }
}