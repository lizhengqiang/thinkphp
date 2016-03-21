<?php
namespace Aliyun\Mns;


use AliyunMNS\Client;
use AliyunMNS\Constants;
use AliyunMNS\AsyncCallback;
use AliyunMNS\Model\QueueAttributes;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Exception\QueueAlreadyExistException;
use AliyunMNS\Exception\BatchSendFailException;
use AliyunMNS\Exception\BatchDeleteFailException;
use AliyunMNS\Requests\CreateQueueRequest;
use AliyunMNS\Requests\GetQueueAttributeRequest;
use AliyunMNS\Requests\SetQueueAttributeRequest;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Requests\BatchSendMessageRequest;
use AliyunMNS\Requests\BatchReceiveMessageRequest;
use AliyunMNS\Requests\BatchPeekMessageRequest;
use AliyunMNS\Model\SendMessageRequestItem;
use AliyunMNS\Requests\ListQueueRequest;

class QueueService
{

    private $client;

    public function __construct($accessId, $accessKey, $endPoint)
    {
        require(LIB_PATH . 'GuzzleHttp/Psr7/functions_include.php');
        require(LIB_PATH . 'GuzzleHttp/Promise/functions_include.php');
        require(LIB_PATH . 'GuzzleHttp/functions_include.php');
        $this->client = new Client($endPoint, $accessId, $accessKey);
    }

    public function sendMessage($messageBody, $queueName)
    {
        // create queue
        $request = new CreateQueueRequest($queueName);
        try {
            $res = $this->client->createQueue($request);
        } catch (MnsException $e) {
            //return false;
        } catch (QueueAlreadyExistException $e) {
            //return false;
        }

        $queue = $this->client->getQueueRef($queueName);

        // send message
        if (is_array($messageBody)) {
            $messageBody = json_encode($messageBody);
        }

        // as the messageBody will be automatically encoded
        // the MD5 is calculated for the encoded body
        $bodyMD5 = md5(base64_encode($messageBody));
        $request = new SendMessageRequest($messageBody);
        try {
            $res = $queue->sendMessage($request);
            return $res;
        } catch (MnsException $e) {
            return $e;
        }
    }
}

?>