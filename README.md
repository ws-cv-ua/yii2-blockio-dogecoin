#Block.io pack

##1. Установка:
```shell
composer require ws-cv-ua/yii2-blockio-dogecoin
```

Подключаем:
```php
'components' => [
    'doge' => [
        'class' => \wscvua\yii2_blockio_dogecoin\components\DogeComponent::className(),
        'apiKey' => '',
        'pin' => '',
        'urlBase' => '' /** ['/status/index'] или абсолютный путь */
    ]
]
```

##2. Получение оплат
Для получение оплаты нам нужно создать адрес и задать WebHook для него (оповещеение от сервера о получении средств). Для этого в компоненте DogeComponent есть методы:
1. generateNewAddress($label). Можно задать уникальную метку для адреса (отобр. на Block.io), можно не задавать.
2. createWebHook($params). Пример:
```php
Yii::$app->doge->createWebHook([
    'type' => 'address',
    'address' => $address,
    'url' => $webHook
]);
```
3. generateSmartAddress($label). Метод объединяет 1 и 2 методы для удобства.

###Пример ответа сервера (WebHook):
```json
{
    "notification_id": "019e3ebf656bf837a45f437a",
    "delivery_attempt": 1,
    "type": "address",
    "data": {
        "network": "DOGETEST",
        "address": "2N43JSwxgdEdv3m5ryyNcE3SaoGKHYuuupt",
        "balance_change": "20.00000000",
        "amount_sent": "0.00000000",
        "amount_received": "20.00000000",
        "txid": "b4ef8e368654c92c774a3702d316d3739a7be6b4935efcaa6da45b3cf0ecc030",
        "confirmations": 0,
        "is_green": true
    },
    "created_at": 1544210682
}
```

###Начальный шаблон контролера:
```php
class StatusController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex($validateCode)
    {
        $params = json_decode(@file_get_contents('php://input'), true);
        $params = ArrayHelper::toArray($params);
        if ($params['type'] != 'address')
            return;

        $address = ArrayHelper::getValue($params, 'data.address');

        /** There are we finding record with this $address and $validateCode... */
    }

}
```

## 3. Оплата (Withdraw)
В компоненте создан метод "pay" для отправки на указаные адреса средств. Пример вызова:
```php
Yii::$app->doge->pay([
    'address1' => 'amount1',
    'address2' => 'amount2',
    'address3' => 'amount3',
]);
```

Пример ответа-ошибки:
```json
{
  "status" : "fail",
  "data" : {
    "error_message" : "Invalid value for parameter TO_ADDRESSES provided."
  }
}
```

Пример успешного завершения:
```json
{
  "status" : "success",
  "data" : {
    "network" : "DOGETEST",
    "txid" : "f9262836c0e6334c3e150c460508f405b0f1c84643c073aeba4b60b20e1b3761",
    "amount_withdrawn" : "6.00000000",
    "amount_sent" : "5.00000000",
    "network_fee" : "1.00000000",
    "blockio_fee" : "0.00000000"
  }
}
```

## 4. Дополнительные виджеты
```php
<?= \wscvua\yii2_blockio_dogecoin\widgets\AddressWidget::widget([
    'address' => 'A6qMXXr5WdroSeLRZVwRwbiPBVP8gBGS6W'
]); ?>

<?= \wscvua\yii2_blockio_dogecoin\widgets\TxWidget::widget([
    'tx' => '9f5f15769fd34f413d557ed1c09ee16cc684aabb1c06bd937a14c50d794130a8'
]); ?>

<?= \wscvua\yii2_blockio_dogecoin\widgets\DogeAmount::widget([
    'amount' => 20
]); ?>
```