<?php

require_once __DIR__.'/vendor/autoload.php';
use Address\AddressTransform;

function demoFoo(){
    /**
     * 参数格式必须为一级_二级_三级_四级
     * @var $local_address_codes
     * 浙江省—杭州市-西湖区-三墩镇
     */
    $address_transform = new AddressTransform();
    $local_address_codes = "19_65_2003_12184";
    $jd = $address_transform->getJdAddressCodes($local_address_codes);
    $suning = $address_transform->getSuningAddressCodes($local_address_codes);
    $deli = $address_transform->getDeliAddressCodes($local_address_codes);
    $zzyc = $address_transform->getZzycAddressCodes($local_address_codes);

    var_dump($jd);
    var_dump($suning);
    var_dump($deli);
    var_dump($zzyc);
}