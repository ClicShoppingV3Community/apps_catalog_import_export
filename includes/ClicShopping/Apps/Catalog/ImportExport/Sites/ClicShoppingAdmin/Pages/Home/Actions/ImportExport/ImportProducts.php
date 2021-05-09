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
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\ImportExport\Classes\ClicShoppingAdmin\ImportExportAdmin;
  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  class ImportProducts extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function execute()
    {
      $this->app = Registry::get('ImportExport');

      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (isset($_GET['ImportExport']) && $_GET['import'] == 'products') {
        $delimiter = HTML::sanitize($_POST['delimiter']);
        $enclosure = HTML::sanitize($_POST['enclosure']);
        $escapechar = HTML::sanitize($_POST['escape']);

        if (isset($_POST['import_all_products'])) {
          $import_all_products = true;
        } else {
          $import_all_products = false;
        }

        if (isset($_POST['import_products_quick_update'])) {
          $import_products_quick_update = true;
        } else {
          $import_products_quick_update = false;
        }

        if (isset($_POST['import_products_quick_update_amazon'])) {
          $import_products_quick_update_amazon = true;
        } else {
          $import_products_quick_update_amazon = false;
        }

        if (!isset($_FILES['import_products']['tmp_name']) || is_uploaded_file($_FILES['import_products']['tmp_name']) === false) {
          $this->app->redirect('ImportExport&message=error_file');
        }

        ob_clean();

        header('Content-Type: text/plain; charset=UTF-8');

        $csv = file_get_contents($_FILES['import_products']['tmp_name']);

        $csv = ImportExportAdmin::csvDecode($csv,$delimiter, $enclosure, $escapechar, null);

        $languages = $CLICSHOPPING_Language->getLanguages();
        $error = false;

        if (\is_array($csv)){
            foreach ($csv as $row) {
              if ($import_all_products === true) {
// Set new products data
                $fields = [
                  'products_id',
                  'categories_id',
                  'parent_id',
                  'has_children',
                  'products_quantity',
                  'products_model',
                  'products_image',
                  'products_ean',
                  'products_sku',
                  'products_image_zoom',
                  'products_price',
                  'products_date_added',
                  'products_last_modified',
                  'products_date_available',
                  'products_weight',
                  'products_price_kilo',
                  'products_status',
                  'products_tax_class_id',
                  'manufacturers_id',
                  'products_ordered',
                  'products_percentage',
                  'products_view',
                  'orders_view',
                  'suppliers_id',
                  'products_archive',
                  'products_min_qty_order',
                  'products_dimension_width',
                  'products_price_comparison',
                  'products_dimension_height',
                  'products_dimension_depth',
                  'products_length_class_id',
                  'admin_user_name',
                  'products_volume',
                  'products_quantity_unit_id',
                  'products_only_online',
                  'products_image_medium',
                  'products_image_small',
                  'products_weight_class_id',
                  'products_cost',
                  'products_handling',
                  'products_packaging',
                  'products_sort_order',
                  'products_quantity_alert',
                  'products_only_shop',
                  'products_download_filename',
                  'products_download_public',
                  'products_type',
                  'language_id',
                  'products_name',
                  'products_description',
                  'products_url',
                  'products_viewed',
                  'products_head_title_tag',
                  'products_head_desc_tag',
                  'products_head_keywords_tag',
                  'products_head_tag',
                  'products_shipping_delay',
                  'products_description_summary',
                  'products_asin'
                ];

//
// Insert inside products table
//
                foreach ($fields as $field) {
                  if (isset($row[$field])) $data[$field] = $row[$field];
                }

                if (empty($data['products_date_added'])) {
                  $products_date_added = 'now()';
                } else {
                  $products_date_added = $data['products_date_added'];
                }

                $sql_data_array = [
                  'parent_id' => (int)$data['parent_id'],
                  'has_children' => (int)$data['has_children'],
                  'products_quantity' => (int)$data['products_quantity'],
                  'products_model' => substr($data['products_model'], 0, 255),
                  'products_image' => substr($data['products_image'], 0, 255),
                  'products_ean' => substr($data['products_ean'], 0, 15),
                  'products_sku' => $data['products_sku'],
                  'products_image_zoom' => substr($data['products_image_zoom'], 0, 255),
                  'products_price' => (float)$data['products_price'],
                  'products_date_added' => $products_date_added,
                  'products_last_modified' => 'now()',
                  'products_date_available' => (empty($data['products_date_available']) ? 'null' : "'" . $data['products_date_available'] . "'"),
                  'products_weight' => (float)$data['products_weight'],
                  'products_price_kilo' => $data['products_price_kilo'],
                  'products_status' => (int)$data['products_status'],
                  'products_tax_class_id' => (int)$data['products_tax_class_id'],
                  'manufacturers_id' => (int)$data['manufacturers_id'],
                  'products_ordered' => (int)$data['products_ordered'],
                  'products_percentage' => (int)$data['products_percentage'],
                  'products_view' => (int)$data['products_view'],
                  'orders_view' => (int)$data['orders_view'],
                  'suppliers_id' => (int)$data['suppliers_id'],
                  'products_archive' => (int)$data['products_archive'],
                  'products_min_qty_order' => (int)$data['products_min_qty_order'],
                  'products_price_comparison' => $data['products_price_comparison'],
                  'products_dimension_width' => (float)$data['products_dimension_width'],
                  'products_dimension_height' => (float)$data['products_dimension_height'],
                  'products_dimension_depth' => (float)$data['products_dimension_depth'],
                  'products_length_class_id' => (int)$data['products_length_class_id'],
                  'admin_user_name' => substr($data['admin_user_name'], 0, 64),
                  'products_volume' => substr($data['products_volume'], 0, 50),
                  'products_quantity_unit_id' => (int)$data['products_quantity_unit_id'],
                  'products_only_online' => (int)$data['products_only_online'],
                  'products_image_medium' => substr($data['products_image_medium'], 0, 255),
                  'products_image_small' => substr($data['products_image_small'], 0, 255),
                  'products_weight_class_id' => (int)$data['products_weight_class_id'],
                  'products_cost' => (float)$data['products_cost'],
                  'products_handling' => (float)$data['products_handling'],
                  'products_packaging' => (int)$data['products_packaging'],
                  'products_sort_order' => (int)$data['products_sort_order'],
                  'products_quantity_alert' => (int)$data['products_quantity_alert'],
                  'products_only_shop' => (int)$data['products_only_shop'],
                  'products_download_filename' => substr($data['products_download_filename'], 0, 255),
                  'products_download_public' => (int)$data['products_download_public'],
                  'products_type' => substr($data['products_type'], 0, 20),
                  'products_asin' => substr($data['products_asin'], 0, 255),
                ];

                $Qcheck = $this->app->db->get('products', 'products_id', ['products_id' => $data['products_id']]);

                if ($Qcheck->fetch()) {
                  $insert_sql_data = ['products_id' => $data['products_id']];
                  $this->app->db->save('products', $sql_data_array, $insert_sql_data);
//
// products_description
//
                  $Qcheck = $this->app->db->get('products_description', 'products_id', ['products_id' => $data['products_id']]);

                  if ($Qcheck->fetch()) {
                    $sql_data_array = [
                      'products_name' => substr($data['products_name'], 0, 255),
                      'products_description' => $data['products_description'],
                      'products_url' => substr($data['products_url'], 0, 255),
                      'products_viewed' => (int)$data['products_viewed'],
                      'products_head_title_tag' => substr($data['products_head_title_tag'], 0, 255),
                      'products_head_desc_tag' => substr($data['products_head_desc_tag'], 0, 255),
                      'products_head_keywords_tag' => substr($data['products_head_keywords_tag'], 0, 255),
                      'products_head_tag' => substr($data['products_head_tag'], 0, 255),
                      'products_shipping_delay' => $data['products_shipping_delay'],
                      'products_description_summary' => $data['products_description_summary'],
                    ];

                    $insert_sql_data = [
                      'products_id' => (int)$data['products_id'],
                      'language_id' => (int)$data['language_id']
                    ];

                    $this->app->db->save('products_description', $sql_data_array, $insert_sql_data);
                  }
                } else {
                  //
                  // Insert new products
                  //
                  $this->app->db->save('products', $sql_data_array);

                  $Qproduts = $this->app->db->prepare('select products_id
                                                        from :table_products
                                                        order by products_id desc
                                                        limit 1
                                                       ');
                  $Qproduts->execute();
                  $products_id = $Qproduts->valueInt('products_id');
//
// products_to_categoriess
//
                  $sql_array = [
                    'products_id' => (int)$products_id,
                    'categories_id' => (int)$data['categories_id']
                  ];

                  $this->app->db->save('products_to_categories', $sql_array);
//
// products_description
//
                  for ($i = 0, $n = \count($languages); $i < $n; $i++) {
                    if ((int)$data['language_id'] === $languages[$i]['id']) {
                      $sql_data_array = [
                        'language_id' => (int)$data['language_id'],
                        'products_name' => substr($data['products_name'], 0, 255),
                        'products_description' => $data['products_description'],
                        'products_url' => substr($data['products_url'], 0, 255),
                        'products_head_title_tag' => substr($data['products_head_title_tag'], 0, 255),
                        'products_head_desc_tag' => substr($data['products_head_desc_tag'], 0, 255),
                        'products_head_keywords_tag' => substr($data['products_head_keywords_tag'], 0, 255),
                        'products_head_tag' => substr($data['products_head_tag'], 0, 255),
                        'products_shipping_delay' => $data['products_shipping_delay'],
                        'products_description_summary' => $data['products_description_summary'],
                      ];

                      $insert_sql_data = [
                        'products_id' => (int)$products_id,
                        'language_id' => (int)$languages[$i]['id']
                      ];

                      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

                      $this->app->db->save('products_description', $sql_data_array);
                    } else {
                      $sql_data_array = [
                        'products_name' => substr($data['products_name'], 0, 255),
                        'products_url' => substr($data['products_url'], 0, 255),
                        'products_id' => (int)$products_id,
                        'language_id' => (int)$languages[$i]['id']
                      ];

                      $this->app->db->save('products_description', $sql_data_array);
                    }
                  }
                }
              } elseif ($import_products_quick_update === true) {
                $fields = [
                  'products_id',
                  'products_quantity',
                  'products_model',
                  'products_sku',
                  'products_ean',
                  'products_status',
                  'products_asin',
                  'admin_user_name'
                ];

                foreach ($fields as $field) {
                  if (isset($row[$field])) $data[$field] = $row[$field];
                }

                $Qcheck = $this->app->db->get('products', 'products_id', ['products_id' => $data['products_id']]);

                if ($Qcheck->fetch()) {
                  $data['products_last_modified'] =  'now()';
                  $data['admin_user_name'] = AdministratorAdmin::getUserAdmin();

                  $sql_data_array = [
                    'products_quantity' => (int)$data['products_quantity'],
                    'products_model' => substr($data['products_model'], 0, 255),
                    'products_sku' => $data['products_sku'],
                    'products_ean' => substr($data['products_ean'], 0, 15),
                    'products_status' => (int)$data['products_status'],
                    'products_last_modified' => 'now()',
                    'admin_user_name' => $data['admin_user_name'],
                    'products_asin' => substr($data['products_asin'], 0, 255),
                  ];

                  $update_array = ['products_id' => $data['products_id']];

                  $this->app->db->save('products', $sql_data_array, $update_array);
                }
              } elseif  ($import_products_quick_update_amazon === true) {
                $fields = [
                  'products_id',
                  'products_sku',
                  'products_quantity',
                  'products_ean',
                  'products_status',
                  'admin_user_name',
                  'products_asin'
                ];

                foreach ($fields as $field) {
                  if (isset($row[$field])) $data[$field] = $row[$field];
                }

                $QcheckSku = $this->app->db->prepare('select products_id,
                                                             products_sku
                                                      from :table_products
                                                      where products_sku = :products_sku                
                                                     ');

                $QcheckSku->bindValue(':products_sku', $data['products_sku']);
                $QcheckSku->execute();

                if ($QcheckSku->fetch()) {
                   if (isset($data['products_sku']) && !isset($data['products_id'])) {
                   $sql_data_array = [
                     'products_last_modified' => 'now()',
                     'admin_user_name' => AdministratorAdmin::getUserAdmin(),
                     'products_quantity' => $data['products_quantity']
                   ];

                   $update_array = ['products_sku' => $data['products_sku']];

                   $this->app->db->save('products', $sql_data_array, $update_array);
                  }
                } else {
                  if (isset($data['products_id'])) {
                    $Qcheck = $this->app->db->get('products', 'products_id', ['products_id' => $data['products_id']]);

                    if ($Qcheck->fetch()) {
                      $sql_data_array = [
                        'products_quantity' => $data['products_quantity'],
                        'products_last_modified' => 'now()',
                        'admin_user_name' => AdministratorAdmin::getUserAdmin(),
                        'products_sku' => $data['products_sku'],
                        'products_ean' => $data['products_ean'],
                        'products_status' => $data['products_status'],
                        'products_asin' => $data['products_asin']
                      ];

                      $update_array = ['products_id' => $data['products_id']];

                      $this->app->db->save('products', $sql_data_array, $update_array);
                    }
                  }
                }
              }
          }
        } else {
          echo '<div class="alert alert-warning">Import Error</div>';
        }

        $CLICSHOPPING_Hooks->call('ImportExportCategories', 'Save');

        Cache::clear('categories');
        Cache::clear('products-also_purchased');
        Cache::clear('products_related');
        Cache::clear('products_cross_sell');
        Cache::clear('upcoming');
      }

      if ($error === false) {
        $this->app->redirect('ImportExport&message=success_product');
      } else {
        $this->app->redirect('ImportExport&message=error_product');
      }
    }
  }