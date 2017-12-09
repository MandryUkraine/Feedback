<?php
/**
 * Маршрутизатор
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

namespace Mandry\Feedback;

class Router {

    /** @var array Адреса сторінки */
    protected $uri;

    /** @var string Псевдонім адреси сторінки */
    protected $alias;

    /** @var array Дозволені псевдоніми */
    protected $aliases = array(

        'перелік' => 'list', 'отримати' => 'get', 'зберегти' => 'set', 'видалити' => 'delete',

        'авторизація' => 'login', 'вихід' => 'logout');

    /** @var string Назва дії контролера */
    protected $action;


    /**
     * Конструктор
     *
     * @throws Exception Невідома адреса сторінки
     */
    public function __construct() {

        $this->setURI();

        $this->setAlias();

        $this->setAction();
    }

    /**
     * Отримує та зберігає адресу сторінки
     */
    private function setURI() {

        preg_match_all('/\/([^\?]*)\??/i', urldecode($_SERVER['REQUEST_URI']), $this->uri);

        $this->uri = explode('/', $this->uri[1][0]);
    }

    /**
     * Повертає адресу сторінки
     *
     * @param null|integer $key Ключ переліку адреси
     * @return string|null Адреса сторінки
     */
    public function getURI($key = 0) {

        return (isset($this->uri[$key])) ? $this->uri[$key] : null;
    }

    /**
     * Отримує, перевіряє та зберігає псевдонім сторінки
     *
     * @throws Exception Невідома адреса сторінки
     */
    private function setAlias() {

        $alias = $this->uri[0];

        if (strlen($alias) > 0) {

            if (!key_exists($alias, $this->aliases))

                throw new Exception('Невідома адреса сторінки');

        } else {

            $alias = key($this->aliases);
        }

        $this->alias = $alias;
    }

    /**
     * Повертає псевдонім сторінки
     *
     * @return string Назва дії контролера
     */
    public function getAlias() {

        return $this->alias;
    }

    /**
     * Отримує та зберігає назву дії контролера
     */
    private function setAction() {

        $this->action = $this->aliases[$this->alias];
    }

    /**
     * Повертає назву дії
     *
     * @return string Назва дії контролера
     */
    public function getAction() {

        return $this->action . 'Action';
    }
}