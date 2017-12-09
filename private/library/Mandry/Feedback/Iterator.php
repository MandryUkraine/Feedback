<?php
/**
 * Ітератор
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

namespace Mandry\Feedback;

class Iterator implements \Iterator {

    /** @var array Внутрішній робочий масив */
    private $items = array();


    /**
     * Конструктор класу
     *
     * @param array $array Перелік
     */
    public function __construct(array $array = null) {

        if (is_array($array)) $this->items = $array;
    }

    /**
     * Переводить вказівник масиву на перший елемент (перемотка на початок)
     */
    public function rewind() {reset($this->items);}

    /**
     * Повертає поточний елемент масиву
     *
     * @return mixed Значення поточно елементу масиву
     */
    public function current() {return current($this->items);}

    /**
     * Повертає ключ поточного едементу масиву
     *
     * @return integer Значення ключа поточно елементу масиву
     */
    public function key() {return key($this->items);}

    /**
     * Переводить вказівник масиву на наступний елеммент
     *
     * @return mixed Значення поточно елементу масиву
     */
    public function next() {return next($this->items);}

    /**
     * Перевіряє чи існує елемент за поточним вказівником масиву
     *
     * @return mixed Значення поточно елементу масиву
     */
    public function valid() {

        $key = key($this->items);

        return ($key !== NULL && $key !== FALSE);
    }

}
