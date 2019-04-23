<?php
/**
 * Created by PhpStorm.
 * User: Reborn
 * Date: 2019/4/23
 * Time: 11:25
 */

namespace reborn\wxpay;

use think\facade\Request;

class test
{
    protected $wx;

    public function __construct()
    {
        $wx['mch_id']=''; //商户号id
        $wx['app_id']='';
        $wx['key']='';
        $wx['app_secret']='';
        $this->wx=$wx;
    }

    /**
     * 微信支付
     * User: Reborn
     * 2019/4/23 10:05:15
     * @return array
     */
    public function wxpay()
    {
        $data['attach']             = '1';//用户id
        $data['nonce_str']          = time() . mt_rand(10000,99999);//订单编号
        $data['body']               = '测试123';//产品描述
        $data['out_trade_no']       = '00220190307104641';//订单号
        $data['total_fee']          = 1 * 100;//单位分
        $data['spbill_create_ip']   = Request::ip();
        $data['notify_url']         = "http://" . $_SERVER['HTTP_HOST'] . '/small/wxnotify';//异步回调地址
        $data['trade_type']         = 'JSAPI';//JSAPI，NATIVE
        $data['openid']             = "";//trade_type为JSAPI时，必须

        $res= (new wxpay($this->wx))->pay($data);
        return $res;
    }

    /**
     * 订单查询
     * User: Reborn
     * 2019/4/23 10:05:25
     * @return array
     */
    public function wxquery()
    {
        $res= (new wxpay($this->wx))->orderQuery('00220190423304164');
        return $res;
    }

    /**
     * 订单退款
     * User: Reborn
     * 2019/4/23 10:27:29
     * @return array
     */
    public function refund()
    {
        $data['nonce_str']          = time() . mt_rand(10000,99999);//订单编号
        $data['out_trade_no']       = '00220190423304164';//订单号
        $data['out_refund_no']      = time() . mt_rand(10000,99999);//退款单号
        $data['total_fee']          = 1;//单位分
        $data['refund_fee']         = 1;//单位分
        $data['notify_url']         = "https://" . $_SERVER['HTTP_HOST'] . '/small/wxreturn';//异步回调地址
        $data['refund_desc']        = '商品缺货';//退款描述
        //$data['certurl']            ='./../extend/'.$cert['cert']; //线上路径
        //$data['keyurl']             ='./../extend/'.$cert['cert'];//线上路径
        $data['certurl']            ='D:/phpStudySetup/PHPTutorial/WWW/X/think/extend/cert/test/apiclient_key.pem';//windows 本地
        $data['keyurl']             ='D:/phpStudySetup/PHPTutorial/WWW/X/think/extend/cert/test/apiclient_key.pem';//windows 本地

        $res= (new wxpay($this->wx))->orderRefund($data);
        return $res;
    }
}