<?php

/**
 * Silex Application helper for Symfony Console
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\Util\Silex;

use Silex\Application;
use Symfony\Component\Console\Helper\Helper;

class SilexAppHelper extends Helper
{
  /**
   * @var Application
   */
  protected $app;

  public function __construct(Application $app)
  {
    $this->app = $app;
  }

  /**
   * @return Application
   */
  public function getApp()
  {
    return $this->app;
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'silexApp';
  }
}