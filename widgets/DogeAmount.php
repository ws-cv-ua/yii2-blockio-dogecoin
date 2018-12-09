<?php

namespace wscvua\yii2_blockio_dogecoin\widgets;

use Yii;
use yii\base\Widget;

class DogeAmount extends Widget
{

    public $amount= 5;

    public $sign = null;

    public function init()
    {
        if (is_null($this->sign)){
            $this->sign = 'Ð';
        }
    }

    public function run()
    {
        return Yii::$app->formatter->asDecimal($this->amount / pow(10, 8), 8) .
            " " .
            $this->sign;
    }

}