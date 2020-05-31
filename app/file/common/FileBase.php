<?php
/**
 * Created by PhpStorm.
 * User: itwri
 * Date: 2020/1/9
 * Time: 21:30
 */

namespace app\file\common;


use app\file\service\UploadService;
use Jasmine\App;
use Jasmine\helper\Config;
use Jasmine\library\Controller;

class FileBase extends Controller
{
    /**
     * @var UploadService|null
     */
    protected $UploadService = null;

    /**
     * FileBase constructor.
     * @param App|null $app
     * @throws \Exception
     */
    function __construct(?App $app = null)
    {
        parent::__construct($app);

        $this->UploadService = new UploadService();


        $key = $this->request()->get('key','');
        $sign = $this->request()->get('sign','');
        $timestamp = $this->request()->get('timestamp','');
        $ip = $this->getRequest()->ip();

        /**
         * 密钥不正确
         */
        if(($secret = Config::get('auth.keys.'.$key))){
            throw new \Exception($this->error(3001));
        }

        /**
         * 签名错误
         */
        if($sign != $this->getSignature(array_merge($this->getRequest()->only(['timestamp','key']),['ip'=>$ip]),$timestamp,$secret)){
            throw new \Exception($this->error(3002));
        }
    }

    /**
     * 生成签名方法 AccessKey + params + time + SecretKey
     * @param array $data 数据
     * @param int|string $timestamp 请求时间
     * @param string $secret
     * @return mixed
     */
    function getSignature($data, $timestamp, $secret)
    {
        if (!$data) {
            return md5($timestamp . $secret);
        }
        ksort($data);
        $arr = array();
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $arr[] = $k . '=' .  rawurlencode(implode(",", $v));
            }else{
                $arr[] = $k . '=' .  rawurlencode($v);
            }
        }
        //params + time + app_secret
        $signStr = implode('&', $arr) . $timestamp . $secret;
        $sign = md5($signStr);
        return strtoupper($sign);
    }

    /**
     * @return UploadService|null
     * itwri 2020/3/20 23:56
     */
    function getUploadService(){
        return $this->UploadService;
    }
}