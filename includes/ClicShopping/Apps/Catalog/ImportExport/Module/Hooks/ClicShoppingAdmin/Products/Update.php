<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\ImportExport\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\ImportExport\Products as ProductsApp;

  class Update implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
    }

    public function execute()
    {
      if (!\defined('CLICSHOPPING_APP_SUPPLIERS_CS_STATUS') || CLICSHOPPING_APP_SUPPLIERS_CS_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Update']) && isset($_GET['Products']) && isset($_POST['products_asin'])) { {
          if (!empty($_POST['products_asin'])) {
            $id = HTML::sanitize($_GET['pID']);

            If (isset($_POST['products_asin'])) {
              $products_asin = '';
            } else {
              $products_asin = HTML::sanitize($_POST['products_asin']);
            }

            $sql_data_array = ['products_asin' => $products_asin];

            $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
          }
        }
      }
    }
  }