<?php
/**
 * Created by PhpStorm.
 * User: Reborn
 * Date: 2019/4/19
 * Time: 15:22
 */

namespace reborn\wxpay;

class wxpay
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
     * 微信支付
     * User: Reborn
     * 2019/3/13 16:50:06
     * @return array
     */
    public function pay($data)
    {
        $resA = (new wxservice( $this->app_id,$this->app_secret,$this->key,$this->mch_id))->setOrder($data);
        $res  = self::xmlToArray($resA);
        $rdata = [];
        if (!empty($res) && $res['return_code'] == 'SUCCESS'){
            if ($res['result_code'] == 'SUCCESS'){
                //下单成功后，返回到前端调取支付所需参数
                $rdata['appId']     = $res['appid'];
                $rdata['timeStamp'] = self::getTimeStr();
                $rdata['nonceStr']  = time() . mt_rand(10000,99999);
                $rdata['package']   = 'prepay_id='.$res['prepay_id'];
                $rdata['signType']  = 'MD5';
                $rdata['paySign']   = $this->getSign($rdata);
                return ['code'=>200,'msg'=>'微信下单成功',$rdata];
            }else{
                return ['code'=>400,'msg'=>'微信下单失败'];
            }
        }else{
            return ['code'=>400,'msg'=>'微信下单失败'];
        }
    }

    /**
     * 获取随机字符串
     * Time: 2018/12/10 17:21
     * @return string
     */
    public static function getTimeStr()
    {
        $number = range(0,9);
        $strArr = range("a",'Z');
        $arrComplipe = implode('',array_merge($number,$strArr));
        $str = str_shuffle($arrComplipe);
        return md5($str);
    }

    /**
     * xml转换为数组
     * Time: 2018/12/10 17:21
     * @param $xml
     * @return mixed
     */
    public static function xmlToArray($xml)
    {
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
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
}