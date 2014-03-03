<?php

/**
 * Array utils
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\Util;

abstract class Arrays
{
  /**
   * Walks through array
   * @param $array
   * @param $callback callable function($path, $value)
   */
  public static  function walkArray($array, $callback, $iterator = null, $prefix = '')
  {
    if (is_null($iterator)) {
      $iterator = new \RecursiveArrayIterator($array);
    }

    while ($iterator->valid()) {

      if ($iterator->hasChildren()) {

        self::walkArray(null, $callback, $iterator->getChildren(), $prefix . '.' . $iterator->key());

      } else {
        call_user_func($callback,
          ltrim($prefix . '.' . $iterator->key(), '.'),
          $iterator->current());
      }

      $iterator->next();
    }
  }
} 