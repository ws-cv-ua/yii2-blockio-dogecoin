<?php
/**
 * @author Rapatij Oleksandr
 */

namespace wscvua\yii2_blockio_dogecoin\components;


use wscvua\yii2_blockio_dogecoin\lib\BlockIo;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\HttpException;

class DogeComponent extends Component
{
    public $apiKey;
    public $pin;
    public $version = 2;

    /** @var $urlBase - base url for web hook */
    public $urlBase;

    /** @var  BlockIo */
    private $blockIo;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        if (!$this->apiKey || !$this->pin || !$this->urlBase){
            throw new HttpException(400, 'You must to set apiKey and pin');
        }

        if (is_array($this->urlBase)) {
            $this->urlBase = Yii::$app->urlManager->createAbsoluteUrl($this->urlBase);
        }

        $this->blockIo = new BlockIo($this->apiKey, $this->pin, $this->version);
    }

    public function generateNewAddress($label = null)
    {
        if ($label) {
            return $this->blockIo->get_new_address(compact('label'));
        }else{
            return $this->blockIo->get_new_address();
        }
    }

    /**
     * @param array $params
     * for example: [
     *      'type' => 'address',
     *      'address' => 'ANY ADDRESS',
     *      'url' => 'YOUR URL'
     * ]
     * @return mixed
     */
    public function createWebHook($params = [])
    {
        return $this->blockIo->create_notification($params);
    }

    /**
     * @param null $label
     * @param array $webHooksParams
     * @return array|bool
     */
    public function generateSmartAddress($label = null, $webHooksParams = [])
    {
        $webHooksParams['validateCode'] = Yii::$app->security->generateRandomString(rand(8, 12));

        $data = $this->generateNewAddress($label);
        if (ArrayHelper::getValue($data, 'status') == 'success'){
            $address = ArrayHelper::getValue($data, 'data.address');
            $webHook = urlencode($this->urlBase . '?' . http_build_query($webHooksParams));
            $flag = $this->createWebHook([
                'type' => 'address',
                'address' => $address,
                'url' => $webHook
            ]);

            return [
                'address' => $address,
                'url' => $webHook,
                'validateCode' => $webHooksParams['validateCode']
            ];
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->blockIo->get_balance();
    }

    /**
     * @param $address
     * @return mixed
     */
    public function isValid($address){
        return $this->blockIo->is_valid_address(compact($address));
    }

    /**
     * @param array $params
     * Example: [
     *  'address1' => 'amount1',
     *  'address2' => 'amount2',
     * ];
     */
    public function pay($params = [])
    {
        return $this->blockIo->withdraw([
            'amounts' => implode(',', array_keys($params)),
            'to_addresses' => implode(',', array_values($params)),
        ]);
    }
}