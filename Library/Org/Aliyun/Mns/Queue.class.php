<?php
namespace Org\Aliyun\Mns;

/* ---阿里Mqs消息列队--- */
class Queue extends Mqs{
	//创建一个新的消息队列。
	public function Createqueue($queueName,$parameter=array()){
		//默认值规定好
		$queue=array('DelaySeconds'=>0,'MaximumMessageSize'=>65536,'MessageRetentionPeriod'=>345600,'VisibilityTimeout'=>30,'PollingWaitSeconds'=>30);
		foreach($queue as $k=>$v){ 
			foreach($parameter as $x=>$y){ 
				if($k==$x){	$queue[$k]=$y;	}		//修改默认值
			}
		}
		$VERB = "PUT";
        $CONTENT_BODY = $this->generatequeuexml($queue);
        $CONTENT_MD5 = base64_encode( md5( $CONTENT_BODY ) );
        $CONTENT_TYPE = $this->CONTENT_TYPE;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mqs-version' => $this->MQSHeaders
        );
		$RequestResource = "/" . $queueName;
		
        $sign = $this->getSignature($VERB,$CONTENT_MD5,$CONTENT_TYPE,$GMT_DATE,$CanonicalizedMQSHeaders,$RequestResource);
		
		$headers = array(
            'Host' => $this->queueownerid.".".$this->mqsurl,
            'Date' => $GMT_DATE,
            'Content-Type' => $CONTENT_TYPE,
            'Content-MD5' => $CONTENT_MD5
        );
        foreach( $CanonicalizedMQSHeaders as $k => $v){
            $headers[ $k ] = $v;
        }
        $headers['Authorization'] = $sign;
		$request_uri = 'http://' . $this->queueownerid .'.'. $this->mqsurl . $RequestResource;
		$data=$this->requestCore($request_uri,$VERB,$headers,$CONTENT_BODY);
		//返回状态，正确返回ok,错误返回错误代码！
		$error = $this->errorHandle($data[0]);
        if($error){
			$msg['state']=$error;
			$msg['msg']=$this->getXmlData($data[1]);
        }else{
			$msg['state']="ok";
		}
		return $msg;
	}
	
	//修改消息队列的属性。
	public function Setqueueattributes($queueName,$parameter=array()){
		//默认值规定好
		$queue=array('DelaySeconds'=>0,'MaximumMessageSize'=>65536,'MessageRetentionPeriod'=>345600,'VisibilityTimeout'=>30,'PollingWaitSeconds'=>30);
		foreach($queue as $k=>$v){ 
			foreach($parameter as $x=>$y){ 
				if($k==$x){	$queue[$k]=$y;	}		//修改默认值
			}
		}
		$VERB = "PUT";
        $CONTENT_BODY = $this->generatequeuexml($queue);
        $CONTENT_MD5 = base64_encode( md5( $CONTENT_BODY ) );
        $CONTENT_TYPE = $this->CONTENT_TYPE;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mqs-version' => $this->MQSHeaders
        );
		$RequestResource = "/" . $queueName . "?metaoverride=true";
		
        $sign = $this->getSignature($VERB,$CONTENT_MD5,$CONTENT_TYPE,$GMT_DATE,$CanonicalizedMQSHeaders,$RequestResource);
		
		$headers = array(
            'Host' => $this->queueownerid.".".$this->mqsurl,
            'Date' => $GMT_DATE,
            'Content-Type' => $CONTENT_TYPE,
            'Content-MD5' => $CONTENT_MD5
        );
        foreach( $CanonicalizedMQSHeaders as $k => $v){
            $headers[ $k ] = $v;
        }
        $headers['Authorization'] = $sign;
		$request_uri = 'http://' . $this->queueownerid .'.'. $this->mqsurl . $RequestResource;
		$data=$this->requestCore($request_uri,$VERB,$headers,$CONTENT_BODY);
		//返回状态，正确返回ok,错误返回错误代码！
		$error = $this->errorHandle($data[0]);
        if($error){
			$msg['state']=$error;
			$msg['msg']=$this->getXmlData($data[1]);
        }else{
			$msg['state']="ok";
			$msg['msg']=$this->getXmlData($data[1]);
		}
		return $msg;
	}
	
	//获取消息队列的属性
	public function Getqueueattributes($queueName){
		$VERB = "GET";
        $CONTENT_BODY = "" ;
        $CONTENT_MD5 = base64_encode( md5( $CONTENT_BODY ) );
        $CONTENT_TYPE = $this->CONTENT_TYPE;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mqs-version' => $this->MQSHeaders
        );
		$RequestResource = "/" . $queueName;
		
        $sign = $this->getSignature($VERB,$CONTENT_MD5,$CONTENT_TYPE,$GMT_DATE,$CanonicalizedMQSHeaders,$RequestResource);
		
		$headers = array(
            'Host' => $this->queueownerid.".".$this->mqsurl,
            'Date' => $GMT_DATE,
            'Content-Type' => $CONTENT_TYPE,
            'Content-MD5' => $CONTENT_MD5
        );
        foreach( $CanonicalizedMQSHeaders as $k => $v){
            $headers[ $k ] = $v;
        }
        $headers['Authorization'] = $sign;
		$request_uri = 'http://' . $this->queueownerid .'.'. $this->mqsurl . $RequestResource;
		$data=$this->requestCore($request_uri,$VERB,$headers,$CONTENT_BODY);
		//返回状态，正确返回ok,错误返回错误代码！
		$error = $this->errorHandle($data[0]);
        if($error){
			$msg['state']=$error;
			$msg['msg']=$this->getXmlData($data[1]);
        }else{
			$msg['state']="ok";
			$msg['msg']=$this->getXmlData($data[1]);
		}
		return $msg;
	}
	
	//删除一个已创建的消息队列。
	public function Deletequeue($queueName){
		$VERB = "DELETE";
        $CONTENT_BODY = "" ;
        $CONTENT_MD5 = base64_encode( md5( $CONTENT_BODY ) );
        $CONTENT_TYPE = $this->CONTENT_TYPE;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mqs-version' => $this->MQSHeaders
        );
		$RequestResource = "/" . $queueName;
		
        $sign = $this->getSignature($VERB,$CONTENT_MD5,$CONTENT_TYPE,$GMT_DATE,$CanonicalizedMQSHeaders,$RequestResource);
		
		$headers = array(
            'Host' => $this->queueownerid.".".$this->mqsurl,
            'Date' => $GMT_DATE,
            'Content-Type' => $CONTENT_TYPE,
            'Content-MD5' => $CONTENT_MD5
        );
        foreach( $CanonicalizedMQSHeaders as $k => $v){
            $headers[ $k ] = $v;
        }
        $headers['Authorization'] = $sign;
		$request_uri = 'http://' . $this->queueownerid .'.'. $this->mqsurl . $RequestResource;
		$data=$this->requestCore($request_uri,$VERB,$headers,$CONTENT_BODY);
		//返回状态，正确返回ok,错误返回错误代码！
		$error = $this->errorHandle($data[0]);
        if($error){
			$msg['state']=$error;
			$msg['msg']=$this->getXmlData($data[1]);
        }else{
			$msg['state']="ok";
		}
		return $msg;
	}
	
	//获取多个消息队列列表
	public function ListQueue($prefix='',$number='',$marker=''){
		$VERB = "GET";
    $CONTENT_BODY = "" ;
    $CONTENT_MD5 = base64_encode( md5( $CONTENT_BODY ) );
    $CONTENT_TYPE = $this->CONTENT_TYPE;
    $GMT_DATE = $this->getGMTDate();
    $CanonicalizedMQSHeaders = array(
        'x-mqs-version' => $this->MQSHeaders,
    );
		
		if($prefix!=''){$CanonicalizedMQSHeaders['x-mqs-prefix'] = $prefix;	}
		if($number!=''){$CanonicalizedMQSHeaders['x-mqs-ret-number'] = $number;	}
		if($marker!=''){$CanonicalizedMQSHeaders['x-mqs-marker'] = $marker;	}
		
		$RequestResource = "/";
    $sign = $this->getSignature($VERB,$CONTENT_MD5,$CONTENT_TYPE,$GMT_DATE,$CanonicalizedMQSHeaders,$RequestResource);
		$headers = array(
        'Host' => $this->queueownerid.".".$this->mqsurl,
        'Date' => $GMT_DATE,
        'Content-Type' => $CONTENT_TYPE,
        'Content-MD5' => $CONTENT_MD5
    );
    foreach( $CanonicalizedMQSHeaders as $k => $v){
        $headers[ $k ] = $v;
    }
    $headers['Authorization'] = $sign;
		$request_uri = 'http://' . $this->queueownerid .'.'. $this->mqsurl . $RequestResource;
		$data=$this->requestCore($request_uri,$VERB,$headers,$CONTENT_BODY);
		//返回状态，正确返回ok,错误返回错误代码！
		$error = $this->errorHandle($data[0]);
    if($error){
			$msg['state']=$error;
			$msg['msg']=$this->getXmlData($data[1]);
    }else{
			$msg['state']="ok";
			$msg['msg']=$this->getXmlData($data[1]);
		}
    return $msg;
	}	
	//数据转换到xml
	private function generatequeuexml($queue=array()){
		header('Content-Type: text/xml;');  
		$dom = new DOMDocument("1.0", "utf-8");
		$dom->formatOutput = TRUE; 
		$root = $dom->createElement("Queue");//创建根节点
		$dom->appendchild($root);
		$price=$dom->createAttribute("xmlns"); 
		$root->appendChild($price); 
		$priceValue = $dom->createTextNode('http://mqs.aliyuncs.com/doc/v1/'); 
		$price->appendChild($priceValue); 
		
		foreach($queue as $k=>$v){ 
			$queue = $dom->createElement($k);  
			$root->appendChild($queue);  
			$titleText = $dom->createTextNode($v);  
			$queue->appendChild($titleText);  
		}
		return $dom->saveXML();  
	}
	
}