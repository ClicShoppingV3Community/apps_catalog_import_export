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

  class ProductsContentTab1  implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/products_content_tab1');
    }

    /**
     * @return string
     */
    private function getAsin(): string
    {
      if (isset($_GET['pID'])) {
        $pID = HTML::sanitize($_GET['pID']);

        $Qproducts = $this->app->db->prepare('select products_asin
                                              from :table_products
                                              where products_id = :products_id
                                            ');
        $Qproducts->bindInt(':products_id', HTML::sanitize($pID));

        $Qproducts->execute();

        $result = $Qproducts->value('products_asin');
      } else {
        $result = '';
      }

      return $result;
    }

    public function installDb()
    {
      $Qcheck = $this->app->db->query("show columns from :table_products like 'products_asin'");

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
ALTER TABLE :table_products ADD products_asin VARCHAR(255) NULL AFTER products_type;
EOD;

        $this->app->db->exec($sql);
      }
    }

    /**
     * @return string
     */
    public function display(): string
    {
      if (!\defined('CLICSHOPPING_APP_IMPORT_EXPORT_IE_STATUS') || CLICSHOPPING_APP_IMPORT_EXPORT_IE_STATUS == 'False') {
        return false;
      }

      static::installDb();

      $products_asin = $this->getAsin();

      $content = '<!-- Link trigger modal -->';

      $content .= '<div class="col-md-12" id="asin_amazon">';
      $content .= '<div class="form-group row">';
      $content .= '<label for="' . $this->app->getDef('text_products_asin') . '" class="col-5 col-form-label">' . $this->app->getDef('text_products_asin') . '</label>';
      $content .= '<div class="col-md-5">';
      $content .= HTML::inputField('products_asin', $products_asin, 'id="products_asin"') ;
      $content .= '</div>';
      $content .= '</div>';
      $content .= '</div>';

      $output = <<<EOD
<!-- ######################## -->
<!--  Start Supplier Hooks      -->
<!-- ######################## -->
<script>
$('#tab1ContentRow4Option').append(
    '{$content}'
);
</script>
<!-- ######################## -->
<!--  End Supplier App      -->
<!-- ######################## -->

EOD;
      return $output;
    }
  }