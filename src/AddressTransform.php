<?php

namespace Address;
use Hyperf\DbConnection\Db;

class AddressTransform
{

    protected function getOutAddressCode($codes,$out_type)
    {
        $count = count($codes);
        $channel = new \Swoole\Coroutine\Channel();
        foreach ($codes as $code) {
            co(
                function () use ($channel, $code,$out_type) {
                    $out_id = Db::table("comm_address_binding")->where("out_address_type", $out_type)
                        ->where("address_id", $code[1])->value("out_address_id");
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
}
