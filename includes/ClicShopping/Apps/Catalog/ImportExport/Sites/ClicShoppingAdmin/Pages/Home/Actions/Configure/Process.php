<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\ImportExport\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  class Process extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_ImportExport = Registry::get('ImportExport');

      $current_module = $this->page->data['current_module'];

      $m = Registry::get('ImportExportAdminConfig' . $current_module);

      foreach ($m->getParameters() as $key) {
        $p = mb_strtolower($key);

        if (isset($_POST[$p])) {
          $CLICSHOPPING_ImportExport->saveCfgParam($key, $_POST[$p]);
        }
      }

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_ImportExport->getDef('alert_cfg_saved_success'), 'success', 'ImportExport');

      $CLICSHOPPING_ImportExport->redirect('Configure&module=' . $current_module);
    }
  }
