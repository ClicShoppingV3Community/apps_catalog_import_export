<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\ImportExport\Sites\ClicShoppingAdmin\Pages\Home\Actions\ImportExport;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\ImportExport\Classes\ClicShoppingAdmin\ImportExportAdmin;

  class ExportProducts extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function execute()
    {
      $this->app = Registry::get('ImportExport');

      $CLICSHOPPING_Db = Registry::get('Db');

      if (isset($_GET['ImportExport']) && $_GET['export'] == 'products') {
        $delimiter = HTML::sanitize($_POST['delimiter']);
        $enclosure = HTML::sanitize($_POST['enclosure']);
        $escapechar = HTML::sanitize($_POST['escape']);
        $language_id = HTML::sanitize($_POST['language']);
        $lineEnding = HTML::sanitize($_POST['line_ending']);
        $output = HTML::sanitize($_POST['output']);

        $csv = [];

        $Qproducts = $CLICSHOPPING_Db->prepare('select p.*,
                                                       pd.* 
                                                from :table_products p,
                                                      :table_products_description pd
                                                 where p.products_id = pd.products_id
                                                 and language_id = :language_id                                                 
                                                ');
        $Qproducts->bindInt('language_id', $language_id);
        $Qproducts->execute();

        while ($Qproducts->fetch()) {
          $Qcategories = $CLICSHOPPING_Db->prepare('select categories_id
                                                    from :table_products_to_categories
                                                    where products_id = :products_id
                                                    limit 1                                              
                                                  ');
          $Qcategories->bindInt('products_id', $Qproducts->valueInt('products_id'));
          $Qcategories->execute();

          if (empty($Qcategories->valueInt('categories_id'))) {
            $categories_id = 0;
          } else {
            $categories_id = $Qcategories->valueInt('categories_id');
          }

          $products_description = str_replace('<br />', '<br>', $Qproducts->value('products_description'));
          $products_description_summary = str_replace('<br />', '<br>', $Qproducts->value('products_description_summary'));

          $csv[] = [
            'products_id' => $Qproducts->valueInt('products_id'),
            'categories_id' => $categories_id,
            'parent_id' => $Qproducts->valueInt('parent_id'),
            'has_children' => $Qproducts->valueInt('has_children'),
            'products_quantity' => $Qproducts->valueInt('products_quantity'),
            'products_model' => $Qproducts->value('products_model'),
            'products_image' => $Qproducts->value('products_image'),
            'products_ean' => $Qproducts->value('products_ean'),
            'products_sku' => $Qproducts->value('products_sku'),
            'products_image_zoom' => $Qproducts->value('products_image_zoom'),
            'products_price' => $Qproducts->valueDecimal('products_price'),
            'products_date_added' => $Qproducts->value('products_date_added'),
            'products_last_modified' => $Qproducts->value('products_last_modified'),
            'products_date_available' => $Qproducts->value('products_date_available'),
            'products_weight' => $Qproducts->valueDecimal('products_weight'),
            'products_price_kilo' => $Qproducts->value('products_price_kilo'),
            'products_status' => $Qproducts->valueInt('products_status'),
            'products_tax_class_id' => $Qproducts->valueInt('products_tax_class_id'),
            'manufacturers_id' => $Qproducts->valueInt('manufacturers_id'),
            'products_ordered' => $Qproducts->valueInt('products_ordered'),
            'products_percentage' => $Qproducts->valueInt('products_percentage'),
            'products_view' => $Qproducts->valueInt('products_view'),
            'orders_view' => $Qproducts->valueInt('orders_view'),
            'suppliers_id' => $Qproducts->valueInt('products_percentage'),
            'products_archive' => $Qproducts->valueInt('products_archive'),
            'products_min_qty_order' => $Qproducts->valueInt('products_min_qty_order'),
            'products_price_comparison' => $Qproducts->valueInt('products_price_comparison'),
            'products_dimension_width' => $Qproducts->valueDecimal('products_dimension_width'),
            'products_dimension_height' => $Qproducts->valueDecimal('products_dimension_height'),
            'products_dimension_depth' => $Qproducts->valueDecimal('products_dimension_depth'),
            'products_length_class_id' => $Qproducts->valueInt('products_length_class_id'),
            'admin_user_name' => $Qproducts->valueInt('admin_user_name'),
            'products_volume' => $Qproducts->value('products_volume'),
            'products_quantity_unit_id' => $Qproducts->valueInt('products_quantity_unit_id'),
            'products_only_online' => $Qproducts->valueInt('products_only_online'),
            'products_image_medium' => $Qproducts->value('products_image_medium'),
            'products_image_small' => $Qproducts->value('products_image_small'),
            'products_weight_class_id' => $Qproducts->valueInt('products_weight_class_id'),
            'products_cost' => $Qproducts->valueDecimal('products_cost'),
            'products_handling' => $Qproducts->valueDecimal('products_handling'),
            'products_packaging' => $Qproducts->valueInt('products_packaging'),
            'products_sort_order' => $Qproducts->valueInt('sort_order'),
            'products_quantity_alert' => $Qproducts->valueInt('products_quantity_alert'),
            'products_only_shop' => $Qproducts->valueInt('products_only_shop'),
            'products_download_filename' => $Qproducts->valueInt('products_download_filename'),
            'products_download_public' => $Qproducts->valueInt('products_download_public'),
            'products_type' => $Qproducts->valueInt('products_type'),

            'language_id' => $Qproducts->valueInt('language_id'),
            'products_name' => $Qproducts->value('products_name'),
            'products_description' => $products_description,
            'products_url' => $Qproducts->value('products_url'),
            'products_viewed' => $Qproducts->value('products_viewed'),
            'products_head_title_tag' => $Qproducts->value('products_head_title_tag'),
            'products_head_desc_tag' => $Qproducts->value('products_head_desc_tag'),
            'products_head_keywords_tag' => $Qproducts->value('products_head_keywords_tag'),
            'products_head_tag' => $Qproducts->value('products_head_tag'),
            'products_shipping_delay' => $Qproducts->value('products_shipping_delay'),
            'products_description_summary' => $products_description_summary
          ];
        }

        ob_clean();

        if ($output == 'screen') {
          header('Content-Type: text/plain; charset=utf-8');
        } else {
          header('Content-Type: application/csv; charset=utf-8');
          header('Content-Disposition: attachment; filename=products-' . $language_id . '.csv');
        }

        switch($lineEnding) {
          case 'Linux':
            echo ImportExportAdmin::csvEncode($csv, $delimiter, $enclosure, $escapechar, null, "\r");
            break;
          case 'Mac':
            echo ImportExportAdmin::csvEncode($csv, $delimiter, $enclosure, $escapechar, null, "\n");
            break;
          case 'Win':
          default:
            echo ImportExportAdmin::csvEncode($csv, $delimiter, $enclosure, $escapechar, null, "\r\n");
            break;
        }

        exit;
      }
    }
  }