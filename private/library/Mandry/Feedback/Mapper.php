<?php
/**
 * Мапер
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

namespace Mandry\Feedback;

class Mapper {

    /** @var View Перегляд */
    protected $view;

    /** @var string Адреса сервера БД */
    protected $host = _DB_HOST;

    /** @var string Назва БД*/
    protected $db = _DB_NAME;

    /** @var string Логін доступу */
    protected $login = _DB_USER;

    /** @var string Пароль для доступу */
    protected $password = _DB_PASSWORD;

    /** @var \mysqli Підключення до БД */
    protected $connection;

    /** @var string Запит sql */
    protected $query;

    /** @var array Додаткові параметри sql-запиту */
    protected $params;

    /** @var \mysqli_result Результата виконання запиту */
    protected $result;


    /**
     * Конструктор класу
     *
     * @param View $view Вивід
     */
    public function __construct(View $view) {

        $this->view = $view;

        $this->connection = new \mysqli($this->host, $this->login, $this->password, $this->db);

        $this->connection->set_charset('utf8');
    }

    /**
     * Виконує sql-запит
     *
     * @param   string $query Запит sql
     * @param   string|array $params Додаткові параметри sql-запиту
     * @throws  Exception Помилка запиту
     */
    public function query($query, $params = array()) {

        $this->query = $query;

        $this->params = (is_array($params) ? $params : array($params));

        if (count($this->params) > 0) {

            foreach($this->params as $key => $param) {

                $this->params[$key] = $this->protect($param);
            }
        }

        $query = vsprintf($this->query, $this->params);

        $time = microtime(true);

        $this->result = $this->connection->query($query);

        if ($this->result === false) {

            $exception = sprintf('Помилка запиту "%s"', $this->connection->error);

            throw new Exception($exception);
        }

        if (isset($this->view)) {

            $queryView = $this->view->debug->mapper->queries->addChild('query');

            $queryView->addAttribute('sql', $query);

            $timeQuery = round((microtime(true) - $time) * 1000, 6);

            $queryView->addAttribute('time', $timeQuery);
        }
    }

    /**
     * Повертає кількість рядків результату запиту
     *
     * @return integer Кількість рядків результату запиту
     */
    public function getResultCount() {

        return $this->result->num_rows;
    }

    /**
     * Повертає рядок/рядки результату запиту
     *
     * @param   boolean $firstOnly Чи повернути тільки перший рядок результату запиту
     * @param   string $name Назва поля з першого рядка результата запиту (при потребі)
     * @return  array Асоціативний масив (масиви) з результатом запиту
     */
    public function getResult($firstOnly = false, $name = null) {

        if ($firstOnly) {

            $row = $this->result->fetch_assoc();

            if (isset($name)) return $row[$name];

            return $row;

        } else {

            $rows = null;

            while($row = $this->result->fetch_assoc()) $rows[] = $row;

            return $rows;
        }
    }

    /**
     * Повертає id нового запису
     *
     * @return integer id нового запису
     */
    public function getInsertId() {

        return $this->connection->insert_id;
    }

    /**
     * Захищає додаткові параметри sql-запиту від sql-ін`єкцій
     *
     * @param   string $string Додатковий параметр sql-запиту
     * @return  string Захищений додатковий параметр sql-запиту
     */
    private function protect(&$string) {

        return $this->connection->real_escape_string($string);
    }

    /**
     * Конструктор об’єкта
     */
    public function __destruct() {

        if (is_object($this->connection)) $this->connection->close();
    }
}