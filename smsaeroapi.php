<?php
/**
 * SmsAero API. Быстрая отправка sms-сообщений через API сервиса SmsAero.
 *
 * @author Vadim Babykin <creativelink@yandex.ru>
 */
class SmsAero
{
    const API_URL = 'http://gate.smsaero.ru/';
    const VERSION = 1.0;

    private $_login;
    private $_password;
    private $_json;
    private $_lastHttpCode;
    private static $_curlInstance;

    /*
     * Создание клиента SmsAero
     * @param $login string - Логин пользователя в системе
     * @param $password string - НЕ хэшированный пароль пользователя
     * @param $json boolean - Флаг, указывающий нужно ли получать ответы в формате json
     */
    public function __construct($login, $password, $json = false)
    {
        $this->_login = $login;
        $this->_password = md5($password);
        $this->_json = $json;
    }

    /*
     * Деструктор.
     */
    public function __destruct()
    {
        // Если у нас открыт экземпляр curl, то нужно его закрывать
        if (!is_null(self::$_curlInstance))
            curl_close(self::$_curlInstance);
    }

    /*
     * Получение баланса пользователя
     */
    public function getBalance()
    {
        $method = "balance";
        $params = array(
            "user" => $this->_login,
            "password" => $this->_password
        );

        return $this->sendRequest($method, $params);
    }

    /*
     * Получение подписей пользователя
     */
    public function getSigns()
    {
        $method = "senders";
        $params = array(
            "user" => $this->_login,
            "password" => $this->_password
        );

        return $this->sendRequest($method, $params);
    }

    /*
     * Получение статуса отправленного сообщения
     * @param $msgId int - идентификатор сообщение, получаемый после отправки
     */
    public function getStatus($msgId)
    {
        $method = "status";
        $params = array(
            "user" => $this->_login,
            "password" => $this->_password,
            "id" => $msgId
        );

        return $this->sendRequest($method, $params);
    }

    /*
     * Запрос на получение новой подписи
     * @param $sign string - запрашиваемая подпись
     */
    public function signRequest($sign)
    {
        $method = "sign";
        $params = array(
            "user" => $this->_login,
            "password" => $this->_password,
            "sign" => $sign
        );

        return $this->sendRequest($method, $params);
    }

    /*
     * Отправка сообщения
     * @param $sign string - подтвержденная подпись пользователя
     * @param $phone string - 11 значный телефонный номер получателя сообщения, начинающийся с 7
     * @param $text string - текст сообщенимя
     * @param $date int - Дата и время отправки сообщения в UNIXTIME
     */
    public function sendMessage($sign, $phone, $text, $date = false)
    {
        $method = "send";
        $params = array(
            "user" => $this->_login,
            "password" => $this->_password,
            "from" => $sign,
            "to" => $phone,
            "text" => $text
        );
        if ($date != false)
            $params['date'] = $date;

        return $this->sendRequest($method, $params);
    }

    /*
     * Отправка сообщения на сервер
     */
    private function sendRequest($method, $params)
    {
        if (is_null(self::$_curlInstance))
            self::$_curlInstance = curl_init();
        if ($this->_json)
            $params['answer'] = 'json';

        $url = self::API_URL . $method . '/?' . http_build_query($params);
        curl_setopt(self::$_curlInstance, CURLOPT_URL, $url);
        curl_setopt(self::$_curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$_curlInstance, CURLOPT_USERAGENT, "SMSAERO PHP API CLIENT v" . self::VERSION);
        $respone = curl_exec(self::$_curlInstance);
        $this->_lastHttpCode = curl_getinfo(self::$_curlInstance, CURLINFO_HTTP_CODE);

        return $respone;
    }
}