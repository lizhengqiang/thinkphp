<?php
namespace Org\Aliyun\Mns;

class Message extends Mqs{

	//发送消息到指定的消息队列
	public function SendMessage($queueName,$msgbody,$DelaySeconds=0,$Priority=8){
		$VERB = "POST";
        $CONTENT_BODY = $this->generatexml($msgbody,$DelaySeconds,$Priority);
        $CONTENT_MD5  = base64_encode(md5($CONTENT_BODY));
        $CONTENT_TYPE = $this->CONTENT_TYPE;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mqs-version' => $this->MQSHeaders
        );
        $RequestResource = "/" . $queueName . "/messages";
        $sign = $this->getSignature( $VERB, $CONTENT_MD5, $CONTENT_TYPE, $GMT_DATE, $CanonicalizedMQSHeaders, $RequestResource );
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
		//返回状态，正确返回ok和返回值数组,错误返回错误代码和错误原因数组！
		$msg=array();
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
	
	//接收指定的队列消息 
	public function ReceiveMessage($queue,$Second){
		$VERB = "GET";
        $CONTENT_BODY = "";
        $CONTENT_MD5 = base64_encode( md5( $CONTENT_BODY ) );
        $CONTENT_TYPE = $this->CONTENT_TYPE;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mqs-version' => $this->MQSHeaders
        );
        $RequestResource = "/" . $queue . "/messages?waitseconds=".$Second;
        $sign = $this->getSignature( $VERB, $CONTENT_MD5, $CONTENT_TYPE, $GMT_DATE, $CanonicalizedMQSHeaders, $RequestResource );
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
		//返回状态，正确返回ok和返回值数组,错误返回错误代码和错误原因数组！
		$msg=array();
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
	
	//删除已经被接收过的消息
	public function DeleteMessage($queueName,$ReceiptHandle){
		$VERB = "DELETE";
        $CONTENT_BODY = "";
        $CONTENT_MD5 = base64_encode( md5( $CONTENT_BODY ) );
        $CONTENT_TYPE = $this->CONTENT_TYPE;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mqs-version' => $this->MQSHeaders
        );
		$RequestResource = "/" . $queueName . "/messages?ReceiptHandle=".$ReceiptHandle;
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
        }else{
			$msg['state']="ok";
		}
		return $msg;
	}
	
	//查看消息，但不改变消息状态（是否被查看或接收）
	public function PeekMessage($queuename){
		$VERB = "GET";
        $CONTENT_BODY = "";
        $CONTENT_MD5 = base64_encode(md5($CONTENT_BODY));
        $CONTENT_TYPE = $this->CONTENT_TYPE;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mqs-version' => $this->MQSHeaders
        );
        $RequestResource = "/" . $queuename . "/messages?peekonly=true";
        $sign = $this->getSignature( $VERB, $CONTENT_MD5, $CONTENT_TYPE, $GMT_DATE, $CanonicalizedMQSHeaders, $RequestResource );
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
		//返回状态，正确返回ok和返回内容数组,错误返回错误代码和错误原因数组！
		$msg=array();
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
	//修改未被查看消息时间，
	public function ChangeMessageVisibility($queueName,$ReceiptHandle,$visibilitytimeout){
	
		$VERB = "PUT";
        $CONTENT_BODY = "";
        $CONTENT_MD5 = base64_encode( md5( $CONTENT_BODY ) );
        $CONTENT_TYPE = $this->CONTENT_TYPE;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mqs-version' => $this->MQSHeaders
        );
		$RequestResource = "/" . $queueName . "/messages?ReceiptHandle=".$ReceiptHandle."&VisibilityTimeout=".$visibilitytimeout;
		
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
	private function generatexml($msgbody,$DelaySeconds=0,$Priority=8){
		header('Content-Type: text/xml;');  
		$dom = new DOMDocument("1.0", "utf-8");
		$dom->formatOutput = TRUE; 
		$root = $dom->createElement("Message");//创建根节点
		$dom->appendchild($root);
		$price=$dom->createAttribute("xmlns"); 
		$root->appendChild($price); 
		$priceValue = $dom->createTextNode('http://mqs.aliyuncs.com/doc/v1/'); 
		$price->appendChild($priceValue); 
		
		$msg=array('MessageBody'=>$msgbody,'DelaySeconds'=>$DelaySeconds,'Priority'=>$Priority);
		foreach($msg as $k=>$v){ 
			$msg = $dom->createElement($k);  
			$root->appendChild($msg);  
			$titleText = $dom->createTextNode($v);  
			$msg->appendChild($titleText);  
		}
		return $dom->saveXML();  
	}
}