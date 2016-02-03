<?php
namespace Think\Session\Driver;

use Memcached as MemcachedResource;

class Memcached
{
    protected $lifeTime    = 3600;
    protected $sessionName = '';
    protected $handle      = null;
    protected $prefix      = '';

    /**
     * 打开Session
     * @access public
     * @param string $savePath
     * @param mixed $sessName
     * @return bool
     */
    public function open($savePath, $sessName)
    {
        if (!extension_loaded('memcached')) {
            E(L('_NOT_SUPPORT_') . ':memcached');
        }
        
        $options = array(
            'timeout'    => C('SESSION_TIMEOUT') ? C('SESSION_TIMEOUT') : 1,
            'persistent' => C('SESSION_PERSISTENT') ? C('SESSION_PERSISTENT') : 0,
        );

        $options = array_merge(array(
            'servers'     => C('MEMCACHED_SERVER') ?: null,
            'lib_options' => C('MEMCACHED_LIB') ?: null,
        ), $options);
        
        $this->prefix = C('SESSION_PREFIX') ? C('SESSION_PREFIX') : $this->prefix;
        $this->lifeTime = C('SESSION_EXPIRE') ? C('SESSION_EXPIRE') : $this->lifeTime;
        


        $this->handle = new MemcachedResource(sha1($options));
        
        
        
        if(count($this->handle->getServerList()) == 0){
          _log(json_encode($options), 'open', 'Session::Memcached', 'T');
          $options['servers'] && $this->handle->addServers($options['servers']);
          $options['lib_options'] && $this->handle->setOptions($options['lib_options']);
        }else{
        }
    
        return true;
    }

    /**
     * 关闭Session
     * @access public
     */
    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        $this->handle->quit();
        $this->handle = null;
        return true;
    }

    /**
     * 读取Session
     * @access public
     * @param string $sessID
     * @return mixed
     */
    public function read($sessID)
    {
        _log($this->prefix.$this->sessionName . $sessID, 'read', 'Session::Memcached', 'T');
        $sessData = $this->handle->get($this->prefix.$this->sessionName . $sessID);
        _log($sessData, 'read', 'Session::Memcached', 'T');
        $sessData = $this->prefix . '|' . serialize(json_decode($sessData, true));
        return $sessData;
    }

    /**
     * 写入Session
     * @access public
     * @param string $sessID
     * @param String $sessData
     */
    public function write($sessID, $sessData)
    {
        $sessData = json_encode(unserialize(substr($sessData, strpos($sessData, '|') + 1)));
        return $this->handle->set($this->prefix.$this->sessionName . $sessID, $sessData, $this->lifeTime);
    }

    /**
     * 删除Session
     * @access public
     * @param string $sessID
     */
    public function destroy($sessID)
    {
        return $this->handle->delete($this->prefix.$this->sessionName . $sessID);
    }

    /**
     * Session 垃圾回收
     * @access public
     * @param string $sessMaxLifeTime
     * @return bool
     */
    public function gc($sessMaxLifeTime)
    {
        return true;
    }
}
