<?php
/**
 * Репозиторій
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

namespace Mandry\Feedback;

class Repository {

    /** @var Mapper Мапер БД */
    protected $mapper;

    /** @var integer Номер сторінки переліку */
    protected $page = 1;

    /** @var integer Кількість сторінок переліку */
    protected $pages = 1;

    /** @var integer Зсув в переліку */
    protected $offset = 0;

    /** @var integer Обмеження кількості записів сторінки переліку */
    protected $limit = 5;

    /** @var string Назва поля по якому здійснюєтья сортування */
    protected $orderField = 'date';

    /** @var string Напрямок сортування (низхідний, висхідний) */
    protected $orderDirection = 'ASC';

    /** @var array Дозволені для сортування поля */
    protected $orderAllowed = array('date', 'name', 'email');

    /** @var string Опис помилки */
    protected $error;


    /**
     * Конструктор класу
     *
     * @param Mapper $mapper Мапер БД
     */
    public function __construct(Mapper $mapper) {

        $this->mapper = $mapper;
    }

    /**
     * Повертає повідомлення за його id
     *
     * @param integer $id Ідентифікатор повідомлення
     * @throws Exception Не знайдено повідомлення
     * @return Entity Повідомлення
     */
    public function get($id) {

        $query = "SELECT * FROM `feedback` WHERE `id` = '%d';";

        $this->mapper->query($query, $id);

        if ($this->mapper->getResultCount() != 1) {

            $message = "Повідомлення з ідентифікатором '$id' не знайдено";

            throw new Exception($message);
        }

        $message = $this->mapper->getResult(true);

        $message = new Entity($message);

        return $message;
    }

    /**
     * Повертає колекцію повідомлень
     *
     * @param integer $page
     * @param string $orderField
     * @param boolean $orderDirection
     * @throws Exception Помилка запиту
     * @return Collection|false Колекцію повідомлень
     */
    public function getList() {

        $query = /** @lang text */

            "SELECT SQL_CALC_FOUND_ROWS * FROM `feedback` ORDER BY `%s` %s LIMIT %d, %d;";

        $params = array($this->orderField, $this->orderDirection, $this->offset, $this->limit);

        $this->mapper->query($query, $params);

        if ($this->mapper->getResultCount() == 0) return false;

        $collection = new Collection();

        foreach($this->mapper->getResult() as $message)

            $collection->add(new Entity($message));

        $this->setPages();

        return $collection;
    }

    /**
     * Зберігає повідомлення
     *
     * @param Entity $message Сутність повідомлення
     * @throws Exception Не знайдено повідомлення
     */
    public function set(Entity $message) {

        $params = array(

            $message->getName(), $message->getEmail(), $message->getSite(), $message->getText()
        );

        if (is_null($message->getID())) {

            array_unshift($params, $message->getDate());

            array_push($params, $message->getIP());

            array_push($params, $message->getBrowser());

            $query = "
            
                INSERT INTO `feedback` (`date`, `name`, `email`, `site`, `text`, `ip`, `browser`)
                
                VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s');";

        } else {

            $params[] = $message->getId();

            $query = "

                UPDATE `feedback` 
                
                SET `name` = '%s', `email` = '%s', `site` = '%s', `text` = '%s'
                
                WHERE `id` = '%d';";
        }

        $this->mapper->query($query, $params);

        if (is_null($message->getId()))

            $message->setId($this->mapper->getInsertId());
    }

    /**
     * Видаляє повідомлення за його id
     *
     * @param integer $id Ідентифікатор повідомлення
     * @throws Exception Помилка запиту
     */
    public function delete($id) {

        $query = "DELETE FROM `feedback` WHERE `id` = '%d';";

        $this->mapper->query($query, $id);
    }

    /**
     * Зберігає назву поля для сортування
     *
     * @param string $field Назва поля для сортування
     * @throws Exception Не дозволене поле для сортуання
     */
    public function setOrderField($field) {

        if (!in_array($field, $this->orderAllowed)) {

            $message = "Поле '%s' не дозволене для сортування";

            throw new Exception(sprintf($message, $field));
        }

        $this->orderField = $field;
    }

    /**
     * Зберігає напрямок сортування
     *
     * @param boolean $direction Назва поля для сортування
     * @throws Exception Не дозволене поле для сортуання
     */
    public function setOrderDirection($direction) {

        if (!is_bool($direction)) {

            $message = "Неправильний тип напрямка сортування '%s'";

            throw new Exception(sprintf($message, $direction));
        }

        $this->orderDirection = ($direction) ? 'ASC' : 'DESC';
    }

    /**
     * Зберігає номер сторінки
     *
     * @param integer $page Номер сторінки переліку
     * @throws Exception Не дозволене поле для сортуання
     */
    public function setPage($page) {

        if (!preg_match('/^\d+$/', $page))

            throw new Exception(sprintf("Неправильний номер сторінки '%s'", $page));

        $this->page = $page;

        $this->setOffset();
    }

    /**
     * Вираховує та зберігає зсув в переліку
     */
    protected function setOffset() {

        $this->offset = ($this->page - 1) * $this->limit;
    }

    /**
     * Отримує та зберігає кількість сторінок переліку
     * @throws Exception Помилка запиту
     */
    protected function setPages() {

        $this->mapper->query("SELECT FOUND_ROWS() AS 'rows';");

        $rows = $this->mapper->getResult(true);

        $this->pages = ceil($rows['rows'] / $this->limit);
    }

    /**
     * Повертає кількість сторінок переліку
     *
     * @return integer Кількість сторінок переліку
     */
    public function getPages() {

        return $this->pages;
    }

    /**
     * Зберігає обмеження рядків на сторінку переліку
     *
     * @param integer $limit Обмеження рядків
     */
    public function setLimit($limit) {

        $this->limit = $limit;
    }

    /**
     * Зберігає опис помилки
     *
     * @param string $error Текст повідомлення про помилку
     */
    protected function setError($error) {

        $this->error = $error;
    }

    /**
     * Повертає опис помилки
     *
     * @return string Текст повідомлення про помилку
     */
    public function getError() {

        return $this->error;
    }
}