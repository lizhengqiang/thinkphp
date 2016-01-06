<?php
namespace Org\Util;

/*
 * Copyright ? 1998 - 2014 Tencent. All Rights Reserved. 腾讯公司 版权所有
 */

class XingeApp {
	
	const DEVICE_ALL = 0;
	const DEVICE_BROWSER = 1;
	const DEVICE_PC = 2;
	const DEVICE_ANDROID = 3;
	const DEVICE_IOS = 4;
	const DEVICE_WINPHONE = 5;
	
	const IOSENV_PROD = 1;
	const IOSENV_DEV = 2;
	
	public function __construct($accessId, $secretKey)
	{
		if(!$accessId || !$secretKey){
			return array('ret_code'=>-1, 'err_msg'=>'appkey and secretkey required');
		}
		$this->accessId = $accessId;
		$this->secretKey = $secretKey;
	}
	public function __destruct(){}
	
	/**
	 * 推送消息给单个设备
	 */
	public function  PushSingleDevice($deviceToken, $message, $environment=0)
	{
		$ret = array('ret_code'=>-1, 'err_msg'=>'message not valid');
		if (!($message instanceof Message) && !($message instanceof MessageIOS)) return $ret;
		if(!$message->isValid()) return $ret;
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['expire_time'] = $message->getExpireTime();
		$params['send_time'] = $message->getSendTime();
		if($message instanceof Message) $params['multi_pkg'] = $message->getMultiPkg();
		$params['device_token'] = $deviceToken;
		$params['message_type'] = $message->getType();
		$params['message'] = $message->toJson();
		$params['timestamp'] = time();
		$params['environment'] = $environment;

		return $this->callRestful(self::RESTAPI_PUSHSINGLEDEVICE, $params);
	}
	
	/**
	 * 推送消息给单个账户
	 */
	public function  PushSingleAccount($deviceType, $account, $message, $environment=0)
	{
		$ret = array('ret_code'=>-1);
		if (!is_int($deviceType) || $deviceType<0 || $deviceType >5) 
		{
			$ret['err_msg'] = 'deviceType not valid';
			return $ret;
		}
		if (!is_string($account) || empty($account))
		{
			$ret['err_msg'] = 'account not valid';
			return $ret;
		}
		if (!($message instanceof Message) && !($message instanceof MessageIOS))
		{
			$ret['err_msg'] = 'message is not android nor ios';
			return $ret;
		}
		if($message instanceof MessageIOS)
		{
			if($environment!=XingeApp::IOSENV_DEV && $environment!=XingeApp::IOSENV_PROD)
			{
				$ret['err_msg'] = "ios message environment invalid";
				return $ret;
			}
		}
		if (!$message->isValid())
		{
			$ret['err_msg'] = 'message not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['expire_time'] = $message->getExpireTime();
		$params['send_time'] = $message->getSendTime();
		if($message instanceof Message)
			$params['multi_pkg'] = $message->getMultiPkg();
		$params['device_type'] = $deviceType;
		$params['account'] = $account;
		$params['message_type'] = $message->getType();
		$params['message'] = $message->toJson();
		$params['timestamp'] = time();
		$params['environment'] = $environment;

		return $this->callRestful(self::RESTAPI_PUSHSINGLEACCOUNT, $params);
	}
	
	/**
	 * 推送消息给APP所有设备
	 */
	public function  PushAllDevices($deviceType, $message, $environment=0)
	{
		$ret = array('ret_code'=>-1, 'err_msg'=>'message not valid');
		if (!is_int($deviceType) || $deviceType<0 || $deviceType >5)
		{
			$ret['err_msg'] = 'deviceType not valid';
			return $ret;
		}
		if (!($message instanceof Message) && !($message instanceof MessageIOS)) return $ret;
		if(!$message->isValid()) return $ret;
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['expire_time'] = $message->getExpireTime();
		$params['send_time'] = $message->getSendTime();
		if($message instanceof Message) $params['multi_pkg'] = $message->getMultiPkg();
		$params['device_type'] = $deviceType;
		$params['message_type'] = $message->getType();
		$params['message'] = $message->toJson();
		$params['timestamp'] = time();
		$params['environment'] = $environment;
	
		return $this->callRestful(self::RESTAPI_PUSHALLDEVICE, $params);
	}
	
	/**
	 * 推送消息给指定tags的设备
	 * 若要推送的tagList只有一项，则tagsOp应为OR
	 */
	public function  PushTags($deviceType, $tagList, $tagsOp, $message, $environment=0)
	{
		$ret = array('ret_code'=>-1, 'err_msg'=>'message not valid');
		if (!is_int($deviceType) || $deviceType<0 || $deviceType >5) 
		{
			$ret['err_msg'] = 'deviceType not valid';
			return $ret;
		}
		if (!is_array($tagList) || empty($tagList)) 
		{
			$ret['err_msg'] = 'tagList not valid';
			return $ret;
		}
		if (!is_string($tagsOp) || ($tagsOp!='AND' && $tagsOp!='OR')) 
		{
			$ret['err_msg'] = 'tagsOp not valid';
			return $ret;
		}
		if (!($message instanceof Message) && !($message instanceof MessageIOS)) return $ret;
		if(!$message->isValid()) return $ret;
		
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['expire_time'] = $message->getExpireTime();
		$params['send_time'] = $message->getSendTime();
		if($message instanceof Message) $params['multi_pkg'] = $message->getMultiPkg();
		$params['device_type'] = $deviceType;
		$params['message_type'] = $message->getType();
		$params['tags_list'] = json_encode($tagList);
		$params['tags_op'] = $tagsOp;
		$params['message'] = $message->toJson();
		$params['timestamp'] = time();
		$params['environment'] = $environment;

		return $this->callRestful(self::RESTAPI_PUSHTAGS, $params);
	}
	
	/**
	 * 查询消息推送状态
	 * @param array $pushIdList pushId(string)数组
	 */
	public function  QueryPushStatus($pushIdList)
	{
		$ret = array('ret_code'=>-1);
		$idList = array();
		if (!is_array($pushIdList) || empty($pushIdList)) 
		{
			$ret['err_msg'] = 'pushIdList not valid';
			return $ret;
		}
		foreach ($pushIdList as $pushId)
		{
			$idList[] = array('push_id'=>$pushId);
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['push_ids'] = json_encode($idList);
		$params['timestamp'] = time();

		return $this->callRestful(self::RESTAPI_QUERYPUSHSTATUS, $params);
	}
	
	/**
	 * 查询应用覆盖的设备数
	 */
	public function  QueryDeviceCount()
	{
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['timestamp'] = time();

		return $this->callRestful(self::RESTAPI_QUERYDEVICECOUNT, $params);
	}
	
	/**
	 * 查询应用标签
	 */
	public function  QueryTags($start=0, $limit=100)
	{
		$ret = array('ret_code'=>-1);
		if (!is_int($start) || !is_int($limit))
		{
			$ret['err_msg'] = 'start or limit not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['start'] = $start;
		$params['limit'] = $limit;
		$params['timestamp'] = time();
	
		return $this->callRestful(self::RESTAPI_QUERYTAGS, $params);
	}
	
	/**
	 * 查询标签下token数量
	 */
	public function  QueryTagTokenNum($tag)
	{
		$ret = array('ret_code'=>-1);
		if (!is_string($tag))
		{
			$ret['err_msg'] = 'tag is not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['tag'] = $tag;
		$params['timestamp'] = time();
	
		return $this->callRestful(self::RESTAPI_QUERYTAGTOKENNUM, $params);
	}
	
	/**
	 * 查询token的标签
	 */
	public function  QueryTokenTags($deviceToken)
	{
		$ret = array('ret_code'=>-1);
		if (!is_string($deviceToken))
		{
			$ret['err_msg'] = 'deviceToken is not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['device_token'] = $deviceToken;
		$params['timestamp'] = time();
	
		return $this->callRestful(self::RESTAPI_QUERYTOKENTAGS, $params);
	}
	
	/**
	 * 取消定时发送
	 */
	public function  CancelTimingPush($pushId)
	{
		$ret = array('ret_code'=>-1);
		if (!is_string($pushId) || empty($pushId))
		{
			$ret['err_msg'] = 'pushId not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['push_id'] = $pushId;
		$params['timestamp'] = time();
	
		return $this->callRestful(self::RESTAPI_CANCELTIMINGPUSH, $params);
	}
	
	//json转换为数组
	protected function json2Array($json){
		$json=stripslashes($json);
		return json_decode($json,true);
	}
	
	protected function callRestful($url, $params)
	{
		$paramsBase = new ParamsBase($params);
		$sign = $paramsBase->generateSign(RequestBase::METHOD_POST, $url,  $this->secretKey);
		$params['sign'] = $sign;
		
		$requestBase = new RequestBase();
		$ret = $this->json2Array($requestBase->exec($url, $params, RequestBase::METHOD_POST));
		
		return $ret;
	}

	private function ValidateToken($token)
	{
		if(intval($this->accessId) >= 2200000000) {
            return strlen($token) == 64;
		} else {
            return (strlen($token) == 40 || strlen($token) == 64);
		}
	}

	public function InitParams() {
		
		$params = array();
        $params['access_id'] = $this->accessId;
        $params['timestamp'] = time();

        return $params;
	}    

	public function BatchSetTag($tagTokenPairs) 
	{
		$ret = array('ret_code' => -1);

		foreach ($tagTokenPairs as $pair) {
			if (!($pair instanceof TagTokenPair)) {
				$ret['err_msg'] = 'tag-token pair type error!';
				return $ret;
			}
	        if (!$this->ValidateToken($pair->token)) {
	        	$ret['err_msg'] = sprintf("invalid token %s", $pair->token);
	        	return $ret;
	        }
	    }
	    $params = $this->InitParams();

	    $tag_token_list = array();
	    foreach ($tagTokenPairs as $pair) {
	    	array_push($tag_token_list, array($pair->tag, $pair->token));
	    }
	    $params['tag_token_list'] = json_encode($tag_token_list); 
	   
	   	return $this->callRestful(self::RESTAPI_BATCHSETTAG, $params); 
	}

	public function BatchDelTag($tagTokenPairs) 
	{
		$ret = array('ret_code' => -1);

		foreach ($tagTokenPairs as $pair) {
			if (!($pair instanceof TagTokenPair)) {
				$ret['err_msg'] = 'tag-token pair type error!';
				return $ret;
			}
	        if (!$this->ValidateToken($pair->token)) {
	        	$ret['err_msg'] = sprintf("invalid token %s", $pair->token);
	        	return $ret;
	        }
	    }
	    $params = $this->InitParams();

	    $tag_token_list = array();
	    foreach ($tagTokenPairs as $pair) 	{
	    	array_push($tag_token_list, array($pair->tag, $pair->token));
	    }
	    $params['tag_token_list'] = json_encode($tag_token_list); 
	   
	   	return $this->callRestful(self::RESTAPI_BATCHDELTAG, $params); 
	}


	public $accessId = ''; //应用的接入Id
	public $secretKey = ''; //应用的skey
	
	const RESTAPI_PUSHSINGLEDEVICE = 'http://openapi.xg.qq.com/v2/push/single_device';
	const RESTAPI_PUSHSINGLEACCOUNT = 'http://openapi.xg.qq.com/v2/push/single_account';
	const RESTAPI_PUSHALLDEVICE = 'http://openapi.xg.qq.com/v2/push/all_device';
	const RESTAPI_PUSHTAGS = 'http://openapi.xg.qq.com/v2/push/tags_device';
	const RESTAPI_QUERYPUSHSTATUS = 'http://openapi.xg.qq.com/v2/push/get_msg_status';
	const RESTAPI_QUERYDEVICECOUNT = 'http://openapi.xg.qq.com/v2/application/get_app_device_num';
	const RESTAPI_QUERYTAGS = 'http://openapi.xg.qq.com/v2/tags/query_app_tags';
	const RESTAPI_CANCELTIMINGPUSH = 'http://openapi.xg.qq.com/v2/push/cancel_timing_task';
	const RESTAPI_BATCHSETTAG = 'http://openapi.xg.qq.com/v2/tags/batch_set';
	const RESTAPI_BATCHDELTAG = 'http://openapi.xg.qq.com/v2/tags/batch_del';
    const RESTAPI_QUERYTOKENTAGS = 'http://openapi.xg.qq.com/v2/tags/query_token_tags';
    const RESTAPI_QUERYTAGTOKENNUM = 'http://openapi.xg.qq.com/v2/tags/query_tag_token_num';
	
}

class TagTokenPair {

	public function __construct($tag, $token)
	{
		$this->tag = strval($tag);
        $this->token = strval($token);
	}
	public function __destruct(){}

	public $tag;
	public $token;
}

class Message {

	public function __construct()
	{
		$this->m_acceptTimes = array();
		$this->m_multiPkg = 0;
		$this->m_raw = "";
	}
	public function __destruct(){}

	public function setTitle($title)
	{
		$this->m_title = $title;
	}
	public function setContent($content)
	{
		$this->m_content = $content;
	}
	public function setExpireTime($expireTime)
	{
		$this->m_expireTime = $expireTime;
	}
	public function getExpireTime()
	{
		return $this->m_expireTime;
	}
	public function setSendTime($sendTime)
	{
		$this->m_sendTime = $sendTime;
	}
	public function getSendTime()
	{
		return $this->m_sendTime;
	}
	public function addAcceptTime($acceptTime)
	{
		$this->m_acceptTimes[] = $acceptTime;
	}
	public function acceptTimeToJson()
	{
		$ret = array();
		foreach ($this->m_acceptTimes as $acceptTime)
		{
			$ret[] = $acceptTime->toArray();
		}
		return $ret;
	}
	/**
	 * 消息类型
	 * @param int $type 1：通知 2：透传消息
	 */
	public function setType($type)
	{
		$this->m_type = $type;
	}
	public function getType()
	{
		return $this->m_type;
	}
	public function setMultiPkg($multiPkg)
	{
		$this->m_multiPkg = $multiPkg;
	}
	public function getMultiPkg()
	{
		return $this->m_multiPkg;
	}
	public function setStyle($style)
	{
		$this->m_style = $style;
	}
	public function setAction($action)
	{
		$this->m_action = $action;
	}
	public function setCustom($custom)
	{
		$this->m_custom = $custom;
	}
	public function setRaw($raw)
	{
		$this->m_raw = $raw;
	}

	public function toJson()
	{
		if(!empty($this->m_raw)) return $this->m_raw;
		$ret = array();
		if ($this->m_type == self::TYPE_NOTIFICATION)
		{
			$ret['title'] = $this->m_title;
			$ret['content'] = $this->m_content;
			$ret['accept_time'] = $this->acceptTimeToJson();
			$ret['builder_id'] = $this->m_style->getBuilderId();
			$ret['ring'] = $this->m_style->getRing();
			$ret['vibrate'] = $this->m_style->getVibrate();
			$ret['clearable'] = $this->m_style->getClearable();
			$ret['n_id'] = $this->m_style->getNId();
			$ret['action'] = $this->m_action->toJson();
		}
		else if($this->m_type == self::TYPE_MESSAGE)
		{
			$ret['title'] = $this->m_title;
			$ret['content'] = $this->m_content;
			$ret['accept_time'] = $this->acceptTimeToJson();
		}
		$ret['custom_content'] = $this->m_custom;
		return json_encode($ret);
	}

	public function isValid()
	{
		if (is_string($this->m_raw) && !empty($this->raw)) return true;
		if(!isset($this->m_title))
			$this->m_title = "";
		else if(!is_string($this->m_title) || empty($this->m_title)) 
			return false;
		if(!isset($this->m_content))
			$this->m_content = "";
		else if(!is_string($this->m_content) || empty($this->m_content)) 
			return false;
		if(!is_int($this->m_type) || $this->m_type<self::TYPE_NOTIFICATION || $this->m_type>self::TYPE_MESSAGE) return false;
		if(!is_int($this->m_multiPkg) || $this->m_multiPkg<0 || $this->m_multiPkg>1) return false;
		if($this->m_type == self::TYPE_NOTIFICATION)
		{
			if(!($this->m_style instanceof Style) || !($this->m_action instanceof ClickAction))
				return false;
			if(!$this->m_style->isValid() || !$this->m_action->isValid())
				return false;
		}
		if (isset($this->m_expireTime))
		{
			if(!is_int($this->m_expireTime) || $this->m_expireTime>3*24*60*60)
				return false;
		}
		else
		{
			$this->m_expireTime = 0;
		}

		if(isset($this->m_sendTime))
		{
			if(strtotime($this->m_sendTime)===false) return false;
		}
		else
		{
			$this->m_sendTime = "2013-12-19 17:49:00";
		}

		foreach ($this->m_acceptTimes as $value)
		{
			if(!($value instanceof TimeInterval) || !$value->isValid())
				return false;
		}

		if(isset($this->m_custom))
		{
			if(!is_array($this->m_custom))
				return false;
		}
		else
		{
			$this->m_custom = array();
		}

		return true;
	}

	private $m_title;
	private $m_content;
	private $m_expireTime;
	private $m_sendTime;
	private $m_acceptTimes;
	private $m_type;
	private $m_multiPkg;
	private $m_style;
	private $m_action;
	private $m_custom;
	private $m_raw;
	
	const TYPE_NOTIFICATION  = 1;
	const TYPE_MESSAGE = 2;
}

class MessageIOS
{
	public function setExpireTime($expireTime)
	{
		$this->m_expireTime = $expireTime;
	}
	public function getExpireTime()
	{
		return $this->m_expireTime;
	}
	public function setSendTime($sendTime)
	{
		$this->m_sendTime = $sendTime;
	}
	public function getSendTime()
	{
		return $this->m_sendTime;
	}
	public function addAcceptTime($acceptTime)
	{
		$this->m_acceptTimes[] = $acceptTime;
	}
	public function acceptTimeToJson()
	{
		$ret = array();
		foreach ($this->m_acceptTimes as $acceptTime)
		{
			$ret[] = $acceptTime->toArray();
		}
		return $ret;
	}
	public function setCustom($custom)
	{
		$this->m_custom = $custom;
	}
	public function setRaw($raw)
	{
		$this->m_raw = $raw;
	}
	public function setAlert($alert)
	{
		$this->m_alert = $alert;
	}
	public function setBadge($badge)
	{
		$this->m_badge = $badge;
	}
	public function setSound($sound)
	{
		$this->m_sound = $sound;
	}
	public function getType()
	{
		return 0;
	}
	public function toJson()
	{
		if(!empty($this->m_raw)) return $this->m_raw;
		$ret = $this->m_custom;
		$aps = array();
		$ret['accept_time'] = $this->acceptTimeToJson();
		$aps['alert'] = $this->m_alert;
		if(isset($this->m_badge)) $aps['badge'] = $this->m_badge;
		if(isset($this->m_sound))$aps['sound'] = $this->m_sound;
		$ret['aps'] = $aps;
		return json_encode($ret);
	}
	
	public function isValid()
	{
		if (is_string($this->m_raw) && !empty($this->raw)) return true;
		if (isset($this->m_expireTime))
		{
			if(!is_int($this->m_expireTime) || $this->m_expireTime>3*24*60*60)
				return false;
		}
		else
		{
			$this->m_expireTime = 0;
		}
	
		if(isset($this->m_sendTime))
		{
			if(strtotime($this->m_sendTime)===false) return false;
		}
		else
		{
			$this->m_sendTime = "2014-03-13 12:00:00";
		}
	
		foreach ($this->m_acceptTimes as $value)
		{
			if(!($value instanceof TimeInterval) || !$value->isValid())
				return false;
		}
	
		if(isset($this->m_custom))
		{
			if(!is_array($this->m_custom))
				return false;
		}
		else
		{
			$this->m_custom = array();
		}
		if(!isset($this->m_alert)) return false;
		if(!is_string($this->m_alert) && !is_array($this->m_alert))
			return false;
		if(isset($this->m_badge))
		{
			if (!is_int($this->m_badge))
				return false;
		}
		if(isset($this->m_sound))
		{
			if (!is_string($this->m_sound))
				return false;
		}
	
		return true;
	}
	
	
	private $m_expireTime;
	private $m_sendTime;
	private $m_acceptTimes;
	private $m_custom;
	private $m_raw;
	private $m_alert;
	private $m_badge;
	private $m_sound;
}

class ClickAction {
	/**
	 * 动作类型
	 * @param int $actionType 1打开activity或app本身，2打开url，3打开Intent
	 */
	public function setActionType($actionType) {
		$this->m_actionType = $actionType;
	}

	public function setUrl($url) {
		$this->m_url = $url;
	}

	public function setComfirmOnUrl($comfirmOnUrl) {
		$this->m_confirmOnUrl = $comfirmOnUrl;
	}

	public function setActivity($activity) {
		$this->m_activity = $activity;
	}

	public function setIntent($intent) {
		$this->m_intent = $intent;
	}

	public function toJson()
	{
		$ret = array();
		$ret['action_type'] = $this->m_actionType;
		$ret['browser'] = array('url'=>$this->m_url, 'confirm'=>$this->m_confirmOnUrl);
		$ret['activity'] = $this->m_activity;
		$ret['intent'] = $this->m_intent;
		return $ret;
	}

	public function isValid()
	{
		if (!isset($this->m_actionType)) $this->m_actionType = 1;
		if (!is_int($this->m_actionType)) return false;
		if ($this->m_actionType<self::TYPE_ACTIVITY || $this->m_actionType>self::TYPE_INTENT)
			return false;

		if($this->m_actionType == self::TYPE_ACTIVITY)
		{
			if (!isset($this->m_activity))
			{
				$this->m_activity = "";
				return true;
			}
			if (is_string($this->m_activity) && !empty($this->m_activity))
				return true;
			return false;
		}

		if($this->m_actionType == self::TYPE_URL)
		{
			if (is_string($this->m_url) && !empty($this->m_url) &&
			is_int($this->m_confirmOnUrl) &&
			$this->m_confirmOnUrl>=0 && $this->m_confirmOnUrl<=1
			)
				return true;
			return false;
		}

		if($this->m_actionType == self::TYPE_INTENT)
		{
			if (is_string($this->m_intent) && !empty($this->m_intent))
				return true;
			return false;
		}

	}

	private $m_actionType;
	private $m_url;
	private $m_confirmOnUrl;
	private $m_activity;
	private $m_intent;
	
	const TYPE_ACTIVITY = 1;
	const TYPE_URL = 2;
	const TYPE_INTENT = 3;
}

class Style {
	public function __construct($builderId, $ring=0, $vibrate=0, $clearable=1, $nId=0)
	{
		$this->m_builderId = $builderId;
		$this->m_ring = $ring;
		$this->m_vibrate = $vibrate;
		$this->m_clearable = $clearable;
		$this->m_nId = $nId;
	}
	public  function __destruct(){}

	public function getBuilderId()
	{
		return $this->m_builderId;
	}

	public function getRing()
	{
		return $this->m_ring;
	}

	public function getVibrate()
	{
		return $this->m_vibrate;
	}

	public function getClearable()
	{
		return $this->m_clearable;
	}
	
	public function getNId()
	{
		return $this->m_nId;
	}

	public function isValid()
	{
		if (!is_int($this->m_builderId) || !is_int($this->m_ring) ||
		!is_int($this->m_vibrate) || !is_int($this->m_clearable)
		)
			return false;
		if ($this->m_ring<0 || $this->m_ring>1) return false;
		if ($this->m_vibrate<0 || $this->m_vibrate>1) return false;
		if ($this->m_clearable<0 || $this->m_clearable>1) return false;

		return true;
	}

	private $m_builderId;
	private $m_ring;
	private $m_vibrate;
	private $m_clearable;
	private $m_nId;
}

class TimeInterval
{
	public function __construct($startHour, $startMin, $endHour, $endMin)
	{
		$this->m_startHour = $startHour;
		$this->m_startMin = $startMin;
		$this->m_endHour = $endHour;
		$this->m_endMin = $endMin;
	}
	public  function __destruct(){}
	public function toArray()
	{
		return  array(
				'start' => array('hour'=>strval($this->m_startHour), 'min'=>strval($this->m_startMin)),
				'end'   => array('hour'=>strval($this->m_endHour), 'min'=>strval($this->m_endMin))
		);
	}
	public function isValid()
	{
		if (!is_int($this->m_startHour) || !is_int($this->m_startMin) ||
		!is_int($this->m_endHour) || !is_int($this->m_endMin)
		)
			return false;

		if ($this->m_startHour>=0 && $this->m_startHour<=23 &&
		$this->m_startMin>=0 && $this->m_startMin<=59 &&
		$this->m_endHour>=0 && $this->m_endHour<=23 &&
		$this->m_endMin>=0 && $this->m_endMin<=59
		)
			return true;
		else
			return false;
	}
	private $m_startHour;
	private $m_startMin;
	private $m_endHour;
	private $m_endMin;
}

class ParamsBase
{

	/**
	 * @var array 当前传入的参数列表
	 */
	public $_params = array();

	/**
	 * 构造函数
	*/
	public function __construct($params)
	{
		if(!is_array($params)){
			return array();
		}
		foreach ($params as $key => $value) {
			//如果是非法的key值，则不使用这个key
			$this->_params[$key] = $value;
		}
	}

	public function set($k, $v){
		if(!isset($k) || !isset($v)){
			return;
		}
		$this->_params[$k] = $v;
	}

	/**
	 * 根据实例化传入的参数生成签名
	 */
	public function generateSign($method, $url, $secret_key)
	{
		//将参数进行升序排序
		$param_str = '';
		$method = strtoupper($method);
		$url_arr = parse_url($url);
		if(isset($url_arr['host']) && isset($url_arr['path'])){
			$url = $url_arr['host'].$url_arr['path'];
		}
		if(!empty($this->_params)){
			ksort($this->_params);
			foreach ($this->_params as $key => $value) {
				$param_str.=$key.'='.$value;
			}
		}
		//print $method.$url.$param_str.$secret_key."\n";
		return md5($method.$url.$param_str.$secret_key);
	}

}

class RequestBase{

	//get请求方式
	const METHOD_GET  = 'get';
	//post请求方式
	const METHOD_POST = 'post';

	/**
	 * 发起一个get或post请求
	 * @param $url 请求的url
	 * @param int $method 请求方式
	 * @param array $params 请求参数
	 * @param array $extra_conf curl配置, 高级需求可以用, 如
	 * $extra_conf = array(
	 *    CURLOPT_HEADER => true,
	 *    CURLOPT_RETURNTRANSFER = false
	 * )
	 * @return bool|mixed 成功返回数据，失败返回false
	 * @throws Exception
	 */
	public static function exec($url,  $params = array(), $method = self::METHOD_GET, $extra_conf = array())
	{
		$params = is_array($params)? http_build_query($params): $params;
		//如果是get请求，直接将参数附在url后面
		if($method == self::METHOD_GET)
		{
			$url .= (strpos($url, '?') === false ? '?':'&') . $params;
		}

		//默认配置
		$curl_conf = array(
				CURLOPT_URL => $url,  //请求url
				CURLOPT_HEADER => false,  //不输出头信息
				CURLOPT_RETURNTRANSFER => true, //不输出返回数据
				CURLOPT_CONNECTTIMEOUT => 3 // 连接超时时间
		);

		//配置post请求额外需要的配置项
		if($method == self::METHOD_POST)
		{
			//使用post方式
			$curl_conf[CURLOPT_POST] = true;
			//post参数
			$curl_conf[CURLOPT_POSTFIELDS] = $params;
		}

		//添加额外的配置
		foreach($extra_conf as $k => $v)
		{
			$curl_conf[$k] = $v;
		}

		$data = false;
		try
		{
			//初始化一个curl句柄
			$curl_handle = curl_init();
			//设置curl的配置项
			curl_setopt_array($curl_handle, $curl_conf);
			//发起请求
			$data = curl_exec($curl_handle);
			if($data === false)
			{
				throw new Exception('CURL ERROR: ' . curl_error($curl_handle));
			}
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}

		return $data;
	}
}



?>
