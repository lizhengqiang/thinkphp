<?php
namespace Think\Controller;
use Think\Controller;

use Aliyun\Mns\QueueService;

class MnsController extends Controller {
  
  private $params = array();
  
  public function initializeParams($mixed){
    $this->params = $mixed;
  }
  
  public function P($key, $value = ''){
    if($value === '') return $this->params[$key];
    $this->params[$key] = $value;
  }
  
  public function O($key, $value){
		$paths = explode('.', $key);
		$truth_key = $paths[0];
		if(count($paths) > 1){
			$json = $this->P($truth_key);
			if(!is_array($json)) $json = array();
			$temp = &$json;
			$first = true;
			//  路径迭代
			foreach($paths as $path){
				// 跳过第一个路径
				if($first){
					$first = false;
					continue;
				}
				// 处理最后一个路径
				if($path == end($paths)){
					// 读操作
					if(!isset($value))
						return $temp[$path];
					// 写操作
					$temp[$path] = $value;
					return $this->P($truth_key, $json);
				}else{
					// 迭代进入
					if(!is_array($temp[$path])){
						$temp[$path] = array();
					}
					$temp = &$temp[$path];
				}
			}
		}else{
			return $this->P($truth_key, $value);
		}
	}
	
	public function clear($params){
  	foreach(split(",", $params) as $param){
    	unset($this->params[$param]);
  	}
	}
  
  public function param($key, $value){
    if($value === '') return $this->params[$key];
    $this->params[$key] = $value;
  }
  
  public function end($errcode, $errmsg)
  {
    if(isset($errcode)) $this->param('errcode', $errcode);
    if(isset($errmsg)) $this->param('errmsg', $errmsg);
    $this->ajaxReturn($this->params);
    exit;
  }
  
  public $queueService;
  
  public function _initialize()
	{
  	$this->queueService = new QueueService(C('ALIYUN.ACCESS_ID'), C('ALIYUN.ACCESS_KEY'), C('ALIYUN.MNS')['queue']["endPoint"]);
	}
  
  // 向队列中发送一条将要执行某个请求的消息, 消息队列将回调我们控制器中的一个方法
  public function MessageCall($ctrl, $method, $params, $origin = '', $post = false){
    if($origin === ''){
      $url = createOpenUrl('/' . $ctrl . '/' . $method);
    }else{
      $url = $origin . '/' . $ctrl . '/' . $method;
    }
    if(strpos($url, '?') !== FALSE){
      $queueName = substr($url, 0, strpos($url, '?'));
    }else{
      $queueName = $url;
    }
    
    $queueName = str_replace('://', '-', $queueName);
    $queueName = str_replace('/', '-', $queueName);
    $queueName = str_replace('.', '-', $queueName);
    $queueName = str_replace('_', '-', $queueName);
    
    if($post){
      $messageBody = array(
        'tag' => $queueName,
        'method' => 'post',
        'url' => $url,
        'data' => urlencode($params),
      );
    }else{
      $messageBody = array(
        'tag' => $queueName,
        'method' => 'get',
        'url' => $url . '?' . http_build_query($params)
      );
    }
    
    
    $this->params[sha1(json_encode($messageBody))] = $messageBody; 
    $this->SendMessage($messageBody, $queueName);
  }
  
  
  
  
  public function SendMessage($messageBody, $queueName){
    $result = $this->queueService->sendMessage($messageBody, $queueName);
    $this->param("SendMessageResult", $result);
  }
  
  public function r204(){
    header("HTTP/1.1 204 No Content", true, 204);
  }

}