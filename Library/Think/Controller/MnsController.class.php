<?php
namespace Think\Controller;
use Think\Controller;

use Aliyun\Mns\QueueService;

class MnsController extends Controller {
  
  private $params = array();
  
  public function param($key, $value = '')
  {
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
    
    $queueName = substr($url, 0, strpos($url, '?'));
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