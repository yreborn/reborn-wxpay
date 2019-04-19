<?php
/**
 * Created by PhpStorm.
 * User: Reborn
 * Date: 2019/4/19
 * Time: 15:26
 */

namespace reborn\wxpay;

class wxservice
{
    private $app_id;
    private $app_secret;
    private $key;
    private $mch_id;

    public function __construct($appid, $appsecret,$key,$mch_id)
    {
        $this->key=$key;
        $this->app_id=$appid;
        $this->mch_id=$mch_id;
        $this->app_secret=$appsecret;
    }

    /**
     * 订单查询
     * Time: 2018/12/11 14:17
     * @param $order
     * @return mixed
     */
    public function orderQuery($order)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";

        $data['appid'] = $this->app_id;
        $data['mch_id'] = $this->mch_id;
        $data['out_trade_no'] = $order;
        $data['nonce_str'] = date('YmdHis',time()).rand(10000,99999);
        $data['sign'] = $this->getSign($data);
        $xml  = $this->toXml($data);
        $res  = $this->getCurl($url,true,'post',$xml);
        return $res;
    }

    /**
     * 微信统一下单
     * User: Reborn
     * 2019/2/25 10:30:32
     * @param $data
     * @return bool|string
     */
    public function setOrder($data)
    {
        $payUrl = 'https://api.mch.weixin.qq.com/pay/unifiedorder';//微信下单接口地址
        $data['appid']= $this->app_id;
        $data['mch_id']= $this->mch_id;
        $data['sign'] = $this->getSign($data);//获取签名
        $xml  = $this->toXml($data);          //下单参数数组转为xml
        $res  = $this->getCurl($payUrl,true,'post',$xml);
        return $res;
    }

    /**
     * 获取微信签名
     * Time: 2018/12/10 17:20
     * @param array $array
     * @return string
     */
    private function getSign($array=[])
    {
        ksort($array);//对数组进行排序
        $string = "";
        foreach ($array as $key => $value) {
            if($string == ""){
                $string =  $key . "=" . $value;
            }else{
                $string .= "&" . $key . "=" . $value;
            }
        }
        $sign = $string . "&key=".$this->key;//拼接商户key
        return strtoupper(md5($sign));//将签名进行md5加密后转化为大写
    }

    /**
     * 数组转xml
     * Time: 2018/12/10 17:21
     * @param $data
     * @return string
     */
    private function toXml($data)
    {
        //将参数进行xml转义
        $xml = "<xml>";
        foreach ($data as $k => $val) {
            $xml .= "<" . $k . ">" . $val . "</" . $k . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * CURL请求
     * User: Reborn
     * 2019/2/25 10:29:49
     * @param $url
     * @param bool $https
     * @param string $method
     * @param null $data
     * @return bool|string
     */
    function getCurl($url, $https = true, $method = 'get', $data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);//设置访问的url；
        curl_setopt($ch, CURLOPT_HEADER, false);//不需要头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//只获取内容，不输出
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//不做服务器端验证，
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//不做客户端验证，
        }
        if($method == 'post'){
            curl_setopt($ch, CURLOPT_POST, true);//设置请求方式，
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置POST请求的数据，
        }
        $result = curl_exec($ch);//执行访问，返回结果,
        return $result;
    }
}