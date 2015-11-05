  SmsAero API
=========

Быстрая отправка sms-сообщений через API сервиса SmsAero для фреймворка Yii2


Использование
--------------
 - установка. 
```
composer require rybinden/smsaero
```
- настраиваем

```php
'components' => [
...
'sms' => [
'class' => 'rybinden\smsaero\SmsAero',
'login' => 'Ваш_логин',
'password' => 'md5_вашего_пароля',
'json' => true, // установить true чтобы получать ответ в формате json
],
...
]
```

 - Пользуемся!

```php
    // Отправка сообщения
    Yii::$app->sms->sendMessage("mysign", 79999999999, "тестовое сообщение");

    // Запрос на получение подписей
     Yii::$app->sms->getSigns();

    // Получение баланса пользователя
     Yii::$app->sms->getBalance();

    // Получение статуса отправленного сообщения
     Yii::$app->sms->getStatus(2558711);

    // Запрос на получение новой подписи
     Yii::$app->sms->signRequest("newsign");
```
Все подробности об ответах API можно посмотреть в официальной документации [API SmsAero](http://smsaero.ru/api/)
