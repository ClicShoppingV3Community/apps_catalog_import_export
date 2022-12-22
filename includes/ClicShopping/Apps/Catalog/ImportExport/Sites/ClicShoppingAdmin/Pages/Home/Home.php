<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\ImportExport\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\ImportExport\ImportExport;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_ImportExport = new ImportExport();
      Registry::set('ImportExport', $CLICSHOPPING_ImportExport);

      $this->app = Registry::get('ImportExport');
      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
