# Feedback
Відгуки та пропозиції
## Налаштування
### Налаштування файлів
- Скачайте код з git-репозиторію
>  git clone https://github.com/MandryUkraine/Feedback.git
- Переіменуйте файл налаштувань
>  /private/settings.template.php  >  /private/settings.php
### Налаштування Apache2
- Налаштуйте віртуальний сервер Apache2 на теку `/public`
- Підключіть в apache2 модуль `mod_rewrite`
### Налаштування БД MySQL
- Створіть БД на сервері MySQL
> Наприклад mandry@localhost
- Створіть таблицю `feedback` за допомогою файла `/private/feedback.sql`
- Створіть користувача з паролем та з повним доступ до таблиці `feedback`
- Заповніть ці дані в файл налаштувань
> _DB_HOST, _DB_NAME, _DB_USER, _DB_PASSWORD
### Налаштування reCAPTCHA
- Отримайте дані нової `reCAPTCHA` для вашого домену
>  https://www.google.com/recaptcha/intro/android.html
- Заповніть ключі (відкритий та закритий) в файлі налаштувань
> _RECAPTCHA_PUBLIC_KEY та _RECAPTCHA_PRIVATE_KEY
### Налаштування доступу  
- Заповніть константу `_USER` в файлі конфігурації
> Наприклад define('_USER', 'admin');
- Отримайте md5 хеш Вашого паролю
> Наприклад тут http://www.md5.cz/
- Заповніть константу `__PASSWORD_HASH` в файлі конфігурації
> Увага!!! В файлі налаштувань зберігається не сам пароль а його md5 хеш `md5($password)`
- Перевірте доступ на сторінці сайту `/авторизація`
### Додаткові налаштування  
- Ввімкнення режиму відлагодження на локальній машині:
> define('_DEBUG', false)  >  define('_DEBUG', true) 