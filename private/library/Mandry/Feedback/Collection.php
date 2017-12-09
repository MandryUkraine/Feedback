<?php
/**
 * Колекція
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

namespace Mandry\Feedback;

class Collection implements \IteratorAggregate  {

    /** @var array Внутрішній робочий масив */
    private $items = array();

    /** @var integer Кількість елементів в масиві */
    private $count = 0;

    /** @var string Назва сутностей колекції */
    protected $entity;


    /**
     * Повертає ітератор з робочим масивом
     *
     * @return Iterator Ітератор з робочим масивом
     */
    public function getIterator() {

        return new Iterator($this->items);
    }

    /**
     * Додає новий едемент до масиву
     *
     * @param Entity $value Значення нового елементу масиву
     */
    public function add(Entity $value) {

        $this->items[$this->count++] = $value;
    }
}