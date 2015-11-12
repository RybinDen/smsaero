<? namespace rybinden\smsaero;
use yii\base\Object;

/**
* SmsAero API. Отправка sms-сообщений через API сервиса SmsAero.
* https://github.com/RybinDen/SmsAero
*/
class SmsAero extends Object
{
    const API_URL = 'http://gate.smsaero.ru/';
    const VERSION = 1.0;
    public $login;
    public $password;
    public $sign;
    public $digital;
public $type;
    public $json;
    private $_lastHttpCode;
    private static $_curlInstance;

  public function __destruct()
  {
        // Если у нас открыт экземпляр curl, то нужно его закрывать
        if (!is_null(self::$_curlInstance))
            curl_close(self::$_curlInstance);
  }

    // Получение баланса пользователя
  public function getBalance()
  {
        $method = "balance";
        $params = [
            "user" => $this->login,
            "password" => $this->password
        ];
        return $this->sendRequest($method, $params);
  }

    // Получение всех подписей пользователя
  public function getSigns()
  {
        $method = "senders";
        $params = [
            "user" => $this->login,
            "password" => $this->password
        ];
        return $this->sendRequest($method, $params);
  }

    // Получение статуса отправленного сообщения
    // param $msgId int - идентификатор сообщение, получаемый после отправки
  public function getStatus($msgId)
  {
        $method = "status";
        $params = [
            "user" => $this->login,
            "password" => $this->password,
            "id" => $msgId
        ];
        return $this->sendRequest($method, $params);
  }

    // Запрос на получение новой подписи
    // param $sign string - запрашиваемая подпись
  public function signRequest($sign)
  {
        $method = "sign";
        $params = [
            "user" => $this->login,
            "password" => $this->password,
            "sign" => $sign
        ];
        return $this->sendRequest($method, $params);
  }

    // Отправка сообщения
    // $phone string - 11 значный телефонный номер получателя сообщения, начинающийся с 7
    // $text string - текст сообщения
    // $date int - Дата и время отправки отложенного сообщения в UNIXTIME
  public function sendMessage($phone, $text, $date = false)
  {
        $method = "send";
        $params = [
            "user" => $this->login,
            "password" => $this->password,
            "from" => $this->sign,
            "to" => $phone,
            "text" => $text
      ];
        if ($this->digital)
            $params['digital'] = 1;
        if ($this->type)
            $params['type'] = $this->type;
        if ($date != false)
            $params['date'] = $date;
        return $this->sendRequest($method, $params);
  }

    // Отправка сообщения на сервер
  private function sendRequest($method, $params)
  {
        if (is_null(self::$_curlInstance))
            self::$_curlInstance = curl_init();
        if ($this->json)
            $params['answer'] = 'json';

        $url = self::API_URL . $method . '/?' . http_build_query($params);
        curl_setopt(self::$_curlInstance, CURLOPT_URL, $url);
        curl_setopt(self::$_curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$_curlInstance, CURLOPT_USERAGENT, "YII2 PHP API CLIENT v" . self::VERSION);
        $respone = curl_exec(self::$_curlInstance);
        $this->_lastHttpCode = curl_getinfo(self::$_curlInstance, CURLINFO_HTTP_CODE);
        return $respone;
  }
}
