<?php
ini_set('memory_limit','150M');
require_once dirname(__FILE__).'/../../symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    // for compatibility / remove and enable only the plugins you want
    $this->enablePlugins(array('sfDoctrinePlugin'));
	$this->enablePlugins(array('sfThumbnailPlugin'));
	
  }
  static protected $zendLoaded = false;
 
  static public function registerZend()
  {
    if (self::$zendLoaded)
    {
      return;
    }

	$includePath = sfConfig::get('sf_lib_dir').'/vendor'.PATH_SEPARATOR.get_include_path();
	set_include_path($includePath);

    require_once sfConfig::get('sf_lib_dir').'/vendor/Zend/Loader.php';
    Zend_Loader::registerAutoload();
    self::$zendLoaded = true;
  }  

  static public function registerFluide()
  {
    require_once dirname(__FILE__).'/../lib/vendor/Fluide/Loader.php';
    spl_autoload_register(array('Fluide_Loader', 'loadClass'));
  }
}
