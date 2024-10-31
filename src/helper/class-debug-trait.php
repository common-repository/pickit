<?php
namespace Ecomerciar\Pickit\Helper;

/**
 * Database Trait
 */
trait DebugTrait {

  public static function log($log){
    if (self::get_option('debug')!= 'no'){
      if (is_array($log) || is_object($log)) {
          self::log_debug(print_r($log, true));
      } else {
          self::log_debug($log);
      }
    }
  }

}
