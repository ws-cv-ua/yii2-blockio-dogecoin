<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 07.12.2018
 * Time: 19:23
 */

namespace wscvua\yii2_blockio_dogecoin\components;


use wscvua\yii2_blockio_dogecoin\lib\BlockIo;
use yii\base\Component;
use yii\web\HttpException;

class DogeComponent extends Component
{
    public $apiKey;
    public $pin;
    public $version = 2;

    /** @var  BlockIo */
    private $blockIo;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        if (!$this->apiKey || !$this->pin){
            throw new HttpException(400, 'You must to set apiKey and pin');
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
}