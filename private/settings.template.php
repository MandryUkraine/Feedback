<?php
/**
 * Налаштування
 *
 * @author      Артем Висоцький <a.vysotsky@gmail.com>
 * @package     Mandry\Feedback
 * @link        https://відгуки.мандри.укр
 * @copyright   Всі права застережено (c) 2017 Мандри
 */

/** Адреса сайту */
define('_HOST', $_SERVER['HTTP_HOST']);

/** Шлях до головної теки сайту */
define('_PATH_ROOT', realpath( __DIR__ . '/..'));

/** Шлях до головної публічної теки сайту */
define('_PATH_PUBLIC', _PATH_ROOT .'/public');

/** Шлях до головної приватної теки сайту */
define('_PATH_PRIVATE', _PATH_ROOT .'/private');

/** Адреса сервера БД MySQL */
define('_DB_HOST', 'localhost');

/** Назва БД MySQL */
define('_DB_NAME', '');

/** Назва користувача БД MySQL */
define('_DB_USER', '');

/** Пароль користувача БД MySQL */
define('_DB_PASSWORD', '');

/** Логін доступу до редактора */
define('_USER', '');

/** Хеш md5 паролю доступу до редактора */
define('_PASSWORD_HASH', '');

/** Відкритий ключ reCAPTCHA */
define('_RECAPTCHA_PUBLIC_KEY', '');

/** Таємний ключ reCAPTCHA */
define('_RECAPTCHA_PRIVATE_KEY', '');

/** Ознка роботи в режимі відлагодження */
define('_DEBUG', false);
