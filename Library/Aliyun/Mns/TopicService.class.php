<?php
namespace Aliyun\Mns;


use AliyunMNS\Client;
use AliyunMNS\Model\SubscriptionAttributes;
use AliyunMNS\Requests\PublishMessageRequest;
use AliyunMNS\Requests\CreateTopicRequest;
use AliyunMNS\Exception\MnsException;

class TopicService{
  
  private $client;
  
  public function __construct($accessId, $accessKey, $endPoint){
    $this->client = new Client($endPoint, $accessId, $accessKey);
  }
  
  public function quickResubscribe($endPoint, $topicName){
    $subscriptionName = str_replace('://', '-', $endPoint);
    $subscriptionName = str_replace('/', '-', $subscriptionName);
    $subscriptionName = str_replace('.', '-', $subscriptionName);
    $this->unsubscribe($subscriptionName, $topicName);
    return $this->subscribe($subscriptionName, $endPoint, $topicName);
  }
  
  public function quickSubscribe($endPoint, $topicName){
    $subscriptionName = str_replace('://', '-', $endPoint);
    $subscriptionName = str_replace('/', '-', $subscriptionName);
    $subscriptionName = str_replace('.', '-', $subscriptionName);
    return $this->subscribe($subscriptionName, $endPoint, $topicName);
  }
  
  public function subscribe($subscriptionName, $endPoint, $topicName, $strategy = '', $contentFormat = 'SIMPLIFIED'){
    // 1. create topic
    $request = new CreateTopicRequest($topicName);
    try
    {
        $res = $this->client->createTopic($request);
    }
    catch (MnsException $e)
    {
        return false;
    }
    $topic = $this->client->getTopicRef($topicName);

    // 2. subscribe
    $attributes = new SubscriptionAttributes($subscriptionName, $endPoint, $strategy, $contentFormat );
    
    try
    {
        $topic->subscribe($attributes);
    }
    catch (MnsException $e)
    {
      echo $e;
        return $e;
    }
    
  }
  
  public function unsubscribe($subscriptionName, $topicName){
    
   
    $topic = $this->client->getTopicRef($topicName);
    
    // 5. unsubscribe
    try
    {
        $topic->unsubscribe($subscriptionName);
    }
    catch (MnsException $e)
    {
        return false;
    }
  }
  
  public function publishMessage($messageBody, $topicName){
    // 1. create topic
    $request = new CreateTopicRequest($topicName);
    try
    {
        $res = $this->client->createTopic($request);
    }
    catch (MnsException $e)
    {
        return false;
    }
    $topic = $this->client->getTopicRef($topicName);
    // 3. send message
    if(is_array($messageBody)){
      $messageBody = json_encode($messageBody);
    }
    // as the messageBody will be automatically encoded
    // the MD5 is calculated for the encoded body
    $bodyMD5 = md5(base64_encode($messageBody));
    $request = new PublishMessageRequest($messageBody);
    try
    {
        $res = $topic->publishMessage($request);
    }
    catch (MnsException $e)
    {
        return false;
    }

    
  }
}
?>