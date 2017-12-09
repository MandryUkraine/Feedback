<?php
/**
 * Сутність
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

namespace Mandry\Feedback;

class Entity {

    /** @var integer Ідентифікатор повідомлення */
    protected $id;

    /** @var string Дата/час створення повідомлення */
    protected $date;

    /** @var string Ім'я автора повідомлення */
    protected $name;

    /** @var string Адреса електронної пошти */
    protected $email;

    /** @var string Адреса сайту автора */
    protected $site;

    /** @var string Текст повідомлення*/
    protected $text;

    /** @var string IP-адреса автора */
    protected $ip;

    /** @var string Оглядач автора */
    protected $browser;


    /**
     * Конструктор
     *
     * @param array $message перелік данних повідомлення
     */
    public function __construct($message) {

        if (isset($message['id'])) $this->setId($message['id']);

        if (isset($message['date'])) $this->setDate($message['date']);

        $this->setName($message['name']);

        $this->setEmail($message['email']);

        if (isset($message['site'])) $this->setSite($message['site']);

        $this->setText($message['text']);

        if (isset($message['ip'])) $this->setIP($message['ip']);

        if (isset($message['browser'])) $this->setBrowser($message['browser']);
    }

    /**
     * Зберігає ідентифікатор повідомлення
     *
     * @param integer $id Ідентифікатор повідомлення
     * @throws Exception Неправильний ідентифікатор
     */
    public function setID($id) {

        if (!preg_match('/^\d+$/', $id))

            throw new Exception("Неправильний ідентифікатор '$id'");

        $this->id = $id;
    }

    /**
     * Повертає ідентифікатор повідомлення
     *
     * @return integer Ідентифікатор повідомлення
     */
    public function getID() {

        return $this->id;
    }

    /**
     * Зберігає дату/час створення повідомлення
     *
     * @param string $date Дата/час створення повідомлення
     * @throws Exception Неправильні дата/час
     */
    public function setDate($date) {

        if (!preg_match('/^|\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $date))

            throw new Exception("Неправильна дата/час '$date'");

        $this->date = $date;
    }

    /**
     * Повертає дату/час створення повідомлення
     *
     * @return string Дата/час створення повідомлення
     */
    public function getDate() {

        return $this->date;
    }

    /**
     * Зберігає ім'я автора повідомлення
     *
     * @param string $name Ім'я
     * @throws Exception Неправильне ім`я
     */
    public function setName($name) {

        if (!preg_match('/^[ а-яіїґє\'\-]{3,32}/iu', $name))

            throw new Exception("Неправильне ім`я '$name'");

        $this->name = strip_tags($name);
    }

    /**
     * Повертає ім'я автора повідомлення
     *
     * @return string Ім'я автора повідомлення
     */
    public function getName() {

        return $this->name;
    }

    /**
     * Зберігає адресу електронної пошти автора повідомлення
     *
     * @param string $email Адресу електронної пошти
     * @throws Exception Неправильна адреса електронної пошти
     */
    public function setEmail($email) {

        if (!preg_match('/^[a-zA-Z0-9.-_]{1,64}@[a-zA-Z0-9.-_]{3,64}$/iu', $email))

            throw new Exception("Неправильна адреса електронної пошти '$email'");

        $this->email = strip_tags($email);
    }

    /**
     * Повертає адресу електронної пошти автора повідомлення
     *
     * @return string Адреса електронної пошти
     */
    public function getEmail() {

        return $this->email;
    }

    /**
     * Зберігає адресу сайта автора повідомлення
     *
     * @param string $site Адреса сайту
     * @throws Exception Неправильна адреса сайту
     */
    public function setSite($site) {

        if (!preg_match('/^|[^ ]{3,128}$/', $site))

            throw new Exception("Неправильна адреса сайту '$site'");

        $this->site = strip_tags($site);
    }

    /**
     * Повертає адресу сайту автора повідомлення
     *
     * @return string Адреса сайту
     */
    public function getSite() {

        return $this->site;
    }

    /**
     * Зберігає текст повідомлення
     *
     * @param string $text Текст повідомлення
     * @throws Exception Неправильний текст повідомлення
     */
    public function setText($text) {

        if (!preg_match('/^.{3,1024}$/iu', $text))

            throw new Exception("Неправильний текст повідомлення '$text'");

        $this->text = strip_tags($text);
    }

    /**
     * Повертає текст повідомлення
     *
     * @return string Текст повідомлення
     */
    public function getText() {

        return $this->text;
    }

    /**
     * Зберігає IP-адресу автора повідомлення
     *
     * @param string $ip IP-адреса
     * @throws Exception Неправильна IP-адреса
     */
    public function setIP($ip) {

        $this->ip = $ip;
    }

    /**
     * Повертає IP-адресу автора повідомлення
     *
     * @return string IP-адреса
     */
    public function getIP() {

        return $this->ip;
    }

    /**
     * Зберігає переглядач автора повідомлення
     *
     * @param string $text Переглядач
     * @throws Exception Неправильний переглядач
     */
    public function setBrowser($browser) {

        $this->browser = $browser;
    }

    /**
     * Повертає переглядач автора повідомлення
     *
     * @return string Переглядач
     */
    public function getBrowser() {

        return $this->browser;
    }
}
