<?php
namespace Aliyun\Mns;


/**
 * mqs.class.php 	消息队列服务MQS 
 *
 * $Author: 	徐阳(xybb501@aliyun.com)
 * $Date: 		2014-07-30
 */
 
/* ---阿里Mqs消息--- */
class Mqs{

	public $AccessKey		= '';
	public $AccessSecret	= '';
	public $CONTENT_TYPE	= 'text/xml;utf-8';
	public $MQSHeaders		= '2015-06-06';
	public $queueownerid	= '';
	public $mqsurl			= '';
	
	
	function __construct(){
		$this->AccessKey	= C('ALIYUN.ACCESS_KEY');
		$this->AccessSecret = C('ALIYUN.ACCESS_SECRET');
		$this->queueownerid	= C('ALIYUN.OWNER_ID');
		$this->mqsurl		= C('ALIYUN.MNS.URL');
	}
	
	//curl 操作	 受保护的方法
	protected function requestCore( $request_uri, $request_method, $request_header, $request_body = "" ){
        if( $request_body != "" ){
            $request_header['Content-Length'] = strlen( $request_body );
        }
        $_headers = array(); foreach( $request_header as $name => $value )$_headers[] = $name . ": " . $value;
		//post最大数据为1024，如果大于就要加下面这句话不然数据会多返回一个 HTTP/1.1 100 Continue "
        //http://www.cnblogs.com/zhengyun_ustc/p/100continue.html
        $_headers[] = "Expect:";
        $request_header = $_headers;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request_method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        $res = curl_exec($ch);
        curl_close($ch);
        return $data = explode("\r\n\r\n",$res);
    }
	//获取错误Handle  受保护的方法
	protected function errorHandle($headers){
        preg_match('/HTTP\/[\d]\.[\d] ([\d]+) /', $headers, $code);
        if($code[1]){
            if( $code[1] / 100 > 1 && $code[1] / 100 < 4 ) return false;
            else return $code[1];
        }
    }
	//签名函数	受保护的方法
	protected function getSignature( $VERB, $CONTENT_MD5, $CONTENT_TYPE, $GMT_DATE, $CanonicalizedMQSHeaders = array(), $CanonicalizedResource = "/" ){
        $order_keys = array_keys( $CanonicalizedMQSHeaders );
        sort( $order_keys );
        $x_mqs_headers_string = "";
        foreach( $order_keys as $k ){
            $x_mqs_headers_string .= join( ":", array( strtolower($k), $CanonicalizedMQSHeaders[ $k ] . "\n" ) );
        }
        $string2sign = sprintf(
            "%s\n%s\n%s\n%s\n%s%s",
            $VERB,
            $CONTENT_MD5,
            $CONTENT_TYPE,
            $GMT_DATE,
            $x_mqs_headers_string,
            $CanonicalizedResource
        );
        $sig = base64_encode(hash_hmac('sha1',$string2sign,$this->AccessSecret,true));
        return "MQS " . $this->AccessKey . ":" . $sig;
    }
	//获取时间 受保护的方法
	protected function getGMTDate(){
        date_default_timezone_set("UTC");
        return date('D, d M Y H:i:s', time()) . ' GMT';
    }
	//解析xml	受保护的方法
	protected function getXmlData($strXml){
		$pos = strpos($strXml, 'xml');
		if ($pos) {
			$xmlCode=simplexml_load_string($strXml,'SimpleXMLElement', LIBXML_NOCDATA);
			$arrayCode=$this->get_object_vars_final($xmlCode);
			return $arrayCode ;
		} else {
			return '';
		}
	}
	//解析obj	受保护的方法
	protected function get_object_vars_final($obj){
		if(is_object($obj)){
			$obj=get_object_vars($obj);
		}
		if(is_array($obj)){
			foreach ($obj as $key=>$value){
				$obj[$key]=$this->get_object_vars_final($value);
			}
		}
		return $obj;
	}
	
}


