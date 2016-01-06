<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Think\Log\Driver;
use Aliyun\Sls\Aliyun_Sls_Client;
use Aliyun\Sls\Models\Aliyun_Sls_Models_LogItem;
use Aliyun\Sls\Models\Aliyun_Sls_Models_PutLogsRequest;
class Sls {
  
    protected $endpoint = 'cn-hangzhou.sls.aliyuncs.com'; // 选择与上面步骤创建Project所属区域匹配的Endpoint
    protected $accessKeyId = '*';        // 使用你的阿里云访问秘钥AccessKeyId
    protected $accessKey = '*';             // 使用你的阿里云访问秘钥AccessKeySecret
    protected $project = '*';                  // 上面步骤创建的项目名称
    protected $logstore = '*';                // 上面步骤创建的日志库名称
    
    protected $config  =   array(
        'log_time_format'   =>  ' c ',
        'log_file_size'     =>  2097152,
        'log_path'          =>  '',
    );

    // 实例化并传入参数
    public function __construct($config=array()){
      
        $this->client = new Aliyun_Sls_Client($this->endpoint, $this->accessKeyId, $this->accessKey);
        trace("sls", "调试");
        $this->config   =   array_merge($this->config,$config);
    }

    /**
     * 日志写入接口
     * @access public
     * @param string $log 日志信息
     * @param string $destination  写入目标
     * @return void
     */
    public function write($log,$destination='') {
        $topic = "";
        $source = "";
        $logitems = array();
        $logItem = new Aliyun_Sls_Models_LogItem();
        $logItem->setTime(time());
        $logItem->setContents($log);
        array_push($logitems, $logItem);
        $req = new Aliyun_Sls_Models_PutLogsRequest($this->project, $this->logstore, $topic, $source, $logitems);
        
        $res = $this->client->putLogs($req);
        dump($res);
        trace($res, "调试");
/*
        $now = date($this->config['log_time_format']);
        if(empty($destination))
            $destination = $this->config['log_path'].date('y_m_d').'.log';
        //检测日志文件大小，超过配置大小则备份日志文件重新生成
        if(is_file($destination) && floor($this->config['log_file_size']) <= filesize($destination) )
              rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
        error_log("[{$now}] ".$_SERVER['REMOTE_ADDR'].' '.$_SERVER['REQUEST_URI']."\r\n{$log}\r\n", 3,$destination);
*/
    }
}
