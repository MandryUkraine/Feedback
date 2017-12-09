<?php
/**
 * Виняток
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

namespace Mandry\Feedback;

class Exception extends \Exception {

    /**
     * Конструктор класу
     *
     * @param string $message Опис винятка (помилки)
     * @param integer $code Код винятка (помилки)
     * @param \Exception $preview Попередній виняток
     */
    public function __construct($message, $code = null, \Exception $preview = null) {

        Log::append($message);

        parent::__construct($message, $code, $preview);
    }
}