<?php
namespace Org\Net;
class Curl{

	const HEADER_NORMAL = 'NORMAL';
    
    private $headers = array(
    	'NORMAL'=>array(
	        'Accept:text/html,application/xhtml+xml,application/xml,*/*;q=0.8',
	        'Accept-Charset:utf-8;q=0.7,*;q=0.3',
	        'Accept-Encoding:gzip,deflate,sdch',
	        'Accept-Language:zh-CN,zh;q=0.8',
	        'Connection:keep-alive',       
	    ),
	    'XML'	=>array(
	    	'Content-type: text/xml', 
	    ),
    );
    
    private $fileCookie;
    
    private $header;
    
    private $useragent;
    
        
	public function __construct($identify,$header){
		$this->fileCookie	= C('COOKIE_JAR_FILE').'/'.$identify.'.cookies';
		if(isset($header)){
			$this->header	= $this->headers[$header];
		}else{
			$this->header	= false;
		}
		
		
		
	}
	
	public function post($url,$params,$json=false){
		$html = $this->curlPost($url,$params);
		if($json){
			return json_decode($html,true);
		}else{
			return $html;
		}
	}
	
	
	public function get($url,$json=false){
		$html = $this->curlPost($url);
		if($json){
			return json_decode($html,true);
		}else{
			return $html;
		}
	}
	
	public function file($addr) {
	    $curl 	= curl_init(); //启动一个curl会话
	    $path	= C('UPLOADS_DIR').'/curl/';
	    $file	= md5($addr).'.jpg';
	    
	    $fp = fopen($path.$file,'wb');
	    
	    curl_setopt($curl, CURLOPT_URL, $addr); //要访问的地址     
	   	curl_setopt($curl, CURLOPT_FILE,$fp);
	   	curl_setopt($curl, CURLOPT_FOLLLOWLOCATION,1);
	   	
	    $result = curl_exec($curl); //执行一个curl会话
	    fclose($fp);
	    curl_close($curl); //关闭curl
	    return $file;     
	}
	
	 /**
     * curl模拟登录的post方法
     * @param $url request地址
     * @param $header 模拟headre头信息
     * @return json
     */
    private function curlPost($url,$data) {
		$curl = curl_init(); //启动一个curl会话
		curl_setopt($curl, CURLOPT_URL, $url); //要访问的地址
		if($this->header){
			
			curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header); //设置HTTP头字段的数组
			
		}
		
		//curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent); //模拟用户使用的浏览器\
		//curl_setopt($curl, CURLOPT_HEADERFUNCTION,array($this,'curlHeader'));
		
		if(isset($data)){
			
			curl_setopt($curl, CURLOPT_POST, 1); //发送一个常规的Post请求
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data); //Post提交的数据包
		}
		
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); //设置超时限制防止死循环
		
		curl_setopt($curl, CURLOPT_COOKIEJAR,$this->fileCookie);
		curl_setopt($curl, CURLOPT_COOKIEFILE,$this->fileCookie);
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //获取的信息以文件流的形式返回
		
		if(strpos($url, "https") != false){
			curl_setopt($curl, CURLOPT_VERIFYHOST, 1);
			curl_setopt($curl, CURLOPT_VERIFYPEER, false);
		}
		
		$result = curl_exec($curl); //执行一个curl会话
		
		curl_close($curl); //关闭curl
		return $result;
	}   
}
?>