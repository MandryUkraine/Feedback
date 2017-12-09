<?php
/**
 * reCAPTCHA
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

namespace Mandry\Feedback;

class Recaptcha {

    /** @var string Адреса перевірки капчі */
    protected $url = 'https://www.google.com/recaptcha/api/siteverify';

    /** @var string Таємний ключ для перевірки */
    protected $key = _RECAPTCHA_PRIVATE_KEY;

    /** @var array Масив для збереження помилок при наявності */
    protected $errors = array();


    /**
     * Перевіряє каптчу
     *
     * @param   string $response Відповідь каптчи
     * @param   string $ip ip-адреса користувача
     * @return  boolean Результат перевірки
     */
    public function validate($response, $ip) {

        $channel = curl_init();

        curl_setopt($channel, CURLOPT_URL, $this->url);

        curl_setopt($channel, CURLOPT_POST, 1);

        curl_setopt($channel, CURLOPT_POSTFIELDS,

            array(

                'secret' => $this->key,

                'response' => $response,

                'remoteip' => $ip
            )
        );

        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($channel), true);

        curl_close ($channel);

        if ($response['success'] === false) {

            $this->errors = $response['error-codes'];

            return false;
        }

        return true;
    }

    /**
     * Повертає помилки
     *
     * @param boolean $isString Ознака формату типу повернення
     * @return string|array Масив з помилками
     */
    public function getErrors($isString = true) {

        return ($isString) ? implode(', ', $this->errors) : $this->errors;
    }
}