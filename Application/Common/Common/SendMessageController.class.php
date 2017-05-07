<?php
namespace  Common\Common;

/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2017/1/12
 * Time: 11:10
 */
class SendMessageController
{

    const  SUPPORT_COMPANY = 'http://manager.wxtxsms.cn';
    const  REMAIN_FEE = '/smsport/feePost.aspx';//获取费用
    const  BEFORE_MESSAGE = '/smsport/getDeliverPost.aspx';//获取上一行的信息
    const  REPORT = '/smsport/getReportPost.aspx';//获取报告
    const  SIGN_ABLE = '/smsport/signPost.aspx';//获取可用的签名
    const  SINGLE_SEND = '/smsport/sendPost.aspx';//签名和内容单独提交
    const  BIND_SEND = '/smsport/sendPostCustomSignature.aspx';//签名和内容一起提交
    const  INDIVIDUATE = '/smsport/sendPostInd.aspx ';//个性化短信发送


    private  $sendUrl ;
    private  $userName = 'jnht1';
    private  $password  = 'jnht@1';
    public   $message = array();
    public   $returnMessage;
    public   $result  ;
    public   $curlTimeOut = 5;



    public function __construct()
    {
        $this->message['uid'] = $this->userName;
        $this->message['upsd'] = MD5($this->password);

    }


    /*发送*/

    public function send()
    {
        $ch = curl_init();


        curl_setopt ($ch, CURLOPT_URL, $this->sendUrl);
        curl_setopt ($ch, CURLOPT_POST, 1);
        if($this->message != ''){

            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->message );
        }


        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $this->curlTimeOut);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        $this->result = $file_contents;

        return $this;

    }



    /*设置电话
     * @param  array $phoneArray 电话号码的一维数组
     * @return  object  $this
     * */
    public function setPhone($phoneArray)
    {

        if($phoneArray)
        {
            $phone = implode(',',$phoneArray);
        }
        $this->message['sendtele'] = $phone;


        return $this;
    }

    /*
     * 匹配错信息
     * */

    public function errorMessage()
    {
        $flag = explode(',',$this->result);

        switch($flag[0])
        {
            case 'success':
                $this->returnMessage = '发送成功';
                break;
            case 'error01':
                $this->returnMessage = '提交方式不正确,请用POST方式提交';
                break;
            case 'error02':
                $this->returnMessage = '参数输入不完整';
                  break;
            case 'error03':
                $this->returnMessage = 'IP认证失败';
                break;
            case 'error04':
                $this->returnMessage = '用户名、密码格式不正确，用户名密码不正确，用户被禁用';
                break;
            case 'error05':
                $this->returnMessage = '号码数量超出1000个';
                break;
            case 'error06':
                $this->returnMessage = '内容不符合规则';
                break;
            case 'error07':
                $this->returnMessage = '内容超长，超出450字';
                break;
            case 'error08':
                $this->returnMessage = '所用签名不对';
                break;
            case 'error09':
                $this->returnMessage = '余额不足';
                break;
            case 'error10':
                $this->returnMessage = '获取异常，请联系客服';
                break;
            default:

        }

        return $this->returnMessage;


    }
    /*
     * 设置curl 超时时间
     * @param string $timeOut 时间
     *
     *  */
    
     public function setCurlConnectTime($timeOut)
     {
         $this->curlTimeOut = $timeOut;
         return $this;
     }

    /*设置发送时间
     *@param string $time 时间
     *
     */
    public function  setSendTime($time)
    {

        $this->message['sendtime'] = $time;
        return $this;

    }

    /*单独发送信息
     * @param string $sign 签名
     * @param string $sendMessage 短信的内容     *
     * */
    public function singleMessage($sign,$sendMessage)
    {

        $this->sendUrl = self::SUPPORT_COMPANY.self::SINGLE_SEND;
        $this->message['sign']= $sign;
        $this->message['msg'] = $sendMessage;

        return $this;

    }
    /*
     * 短信内容和签名一起交,短信内容，包含签名
     * @param string $sign 签名
     * @param string $sendMessage 短信的内容
     * */

    public function bindMessage($sign,$sendMessage)
    {
        $this->sendUrl = self::SUPPORT_COMPANY.self::BIND_SEND;
        $this->message['Msg']= '【'.$sign.'】'.$sendMessage;

        return $this;
    }

    public function getReport()
    {
        $this->sendUrl = self::SUPPORT_COMPANY.self::REPORT;
        return $this;

    }
    /*
     * 个性化发送短信
     * @param string $sendMessage #line#电话号码#column#内容#line#电话号码#column#内容，
     * #line#1861**5112#column#您本次上网密码：226787，仅限本次上网使用，有效期为90秒
     * @param int  $sendCount 短信条数
     * @param string $sign 签名
     *  */

    public function individuateMessage($sign,$sendCount,$sendMessage)
    {
        $this->sendUrl = self::SUPPORT_COMPANY.self::INDIVIDUATE;
        $this->message['send_count']= $sendCount;
        $this->message['msg_param']= $sendMessage;
        $this->message['sign']= $sign;
        return $this;
    }

    /*获取费用还有多少
    */
    public function  getRemainFee()
    {
        $this->sendUrl = self::SUPPORT_COMPANY.self::REMAIN_FEE;
        return $this;

    }








}