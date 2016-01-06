<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think\Cache\Driver;
use Think\Cache;
defined('THINK_PATH') or exit();
/**
 * Mouge Memcached 缓存
 */
class Mougememcached extends Cache {

  /**
   * 架构函数
   * @param array $options 缓存参数
   * @access public
   */
  function __construct($options=array()) {

      $options = array_merge(array(
        'host' => C('MOUGE_MEMCACHED_HOST') ? C('MOUGE_MEMCACHED_HOST') : 'memcached.muni.pub',
        'persistent' => false,
      ),$options);
      
      $options['root'] = 'http://' . $options['host'];
      
      $this->options = $options;
      
  }

  /**
   * 读取缓存
   * @access public
   * @param string $name 缓存变量名
   * @return mixed
   */
  public function get($name) {
    // 计数
    N('cache_read',1);
    // 读取
    $origin = file_get_contents($this->options['root'] . '/' . $this->options['prefix'].$name);
    dump($this->options['root'] . '/' . $this->options['prefix'].$name);
    dump($origin);
    $json   = json_decode($origin, true);
    return $json["data"];
  }

  /**
   * 写入缓存
   * @access public
   * @param string $name 缓存变量名
   * @param mixed $value  存储数据
   * @param integer $expire  有效时间（秒）
   * @return boolean
   */
  public function set($name, $value, $expire = null) {
    N('cache_write',1);
    if(is_null($expire)) {
      $expire  =  $this->options['expire'];
    }
    
    $name = $this->options['prefix'].$name;
    dump(json_encode(array(
      'key' => $name,
      'value' => $value,
      'expire' => "".$expire,
    )));
    dump($this->options['root']);
    dump(file_post_contents($this->options['root'] , json_encode(array(
      'key' => $name,
      'value' => $value,
      'expire' => $expire,
    ))));
   
    return true;
  }

  /**
   * 删除缓存
   * @access public
   * @param string $name 缓存变量名
   * @return boolean
   */
  public function rm($name, $ttl = false) {
      $name = $this->options['prefix'].$name;
      file_delete_contents($this->options['root'] . '/' . $name);
      return true;
  }

  /**
   * 清除缓存
   * @access public
   * @return boolean
   */
  public function clear() {
      return true;
  }
}