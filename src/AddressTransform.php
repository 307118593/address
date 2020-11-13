<?php

namespace Address;
use Hyperf\DbConnection\Db;

class AddressTransform
{

    /**
     * Note:高效获取三方地址编码
     * Author: Song
     * Date: 2020/11/13
     * @param $codes
     * @param $out_type
     * @return array|string
     */
    protected function getOutAddressCode($codes,$out_type)
    {
        $count = count($codes);
        $channel = new \Swoole\Coroutine\Channel();
        foreach ($codes as $code) {
            co(
                function () use ($channel, $code,$out_type) {
                    $out_id = Db::table("comm_address_binding")->where("out_address_type", $out_type)
                        ->where("address_id", $code[1])->value("out_address_id")??0;
                    $res = [$code[0], $out_id];
                    $channel->push($res);
                }
            );
        }
        while ($count--) {
            $address_group[] = $channel->pop();
        }
        array_multisort($address_group);
        foreach ($address_group as $item) {
            $address[] = $item[1];
        }
        $address = implode('_',$address);
        return $address;
    }

    public function getJdAddressCodes(string $local_address_codes)
    {
        $out_type = "jd";
        $codes = $this->resolveLocalCodes($local_address_codes);
        if ($codes === false) {
            return "0";
        }
        $address = $this->getOutAddressCode($codes,$out_type);
        return $address;
    }

    public function getDeliAddressCodes(string $local_address_codes)
    {
        $out_type = "deli";
        $codes = $this->resolveLocalCodes($local_address_codes);
        if ($codes === false) {
            return "0";
        }
        $address = $this->getOutAddressCode($codes,$out_type);
        return $address;
    }

    public function getSuningAddressCodes(string $local_address_codes)
    {
        $out_type = "suning";
        $codes = $this->resolveLocalCodes($local_address_codes);
        if ($codes === false) {
            return "0";
        }
        $address = $this->getOutAddressCode($codes,$out_type);
        return $address;
    }

    public function getZzycAddressCodes(string $local_address_codes)
    {
        $out_type = "zzyc";
        $codes = $this->resolveLocalCodes($local_address_codes);
        if ($codes === false) {
            return "0";
        }
        $address = $this->getOutAddressCode($codes,$out_type);
        return $address;
    }


    /**
     * Note:解决参数
     * Author: Song
     * Date: 2020/11/13
     * @param string $local_address_codes
     * @return array|bool
     */
    protected function resolveLocalCodes(string $local_address_codes)
    {
        $codes = explode("_", $local_address_codes);
        if (!is_array($codes) || count($codes) > 4) {
            return false;
        }
        foreach ($codes as $code) {
            $int = intval($code);
            if ($int == 0) {
                return false;
            }
        }
        $sort = [];
        foreach ($codes as $k => $code) {
            $sort[] = [$k, $code];
        }
        return $sort;
    }

    /**
     * Note:获取地址列表；获取顶级地址列表parent_id传0
     * Author: Song
     * Date: 2020/11/13
     * @param $parent_id
     * @return mixed
     */
    public function getAddressList($parent_id)
    {
        $field = ['id','level','code','name'];
        $address_list = Db::table('comm_address')->where(['parent_id'=>$parent_id])->select($field)->get();
        return $address_list;
    }
}
