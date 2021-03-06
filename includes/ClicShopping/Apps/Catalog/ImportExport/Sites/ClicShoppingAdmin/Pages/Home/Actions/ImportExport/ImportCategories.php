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

  class ImportCategories extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function execute()
    {
      $this->app = Registry::get('ImportExport');

      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (isset($_GET['ImportExport']) && isset($_GET['import']) && $_GET['export'] == 'categories') {
        $delimiter = HTML::sanitize($_POST['delimiter']);
        $enclosure = HTML::sanitize($_POST['enclosure']);
        $escapechar = HTML::sanitize($_POST['escape']);

        if (!isset($_FILES['import_categories']['tmp_name']) || is_uploaded_file($_FILES['import_categories']['tmp_name']) === false) {
          $this->app->redirect('ImportExport&Error=error_file');
        }

        ob_clean();

        header('Content-Type: text/plain; charset=UTF-8');

        $csv = file_get_contents($_FILES['import_categories']['tmp_name']);

        $csv = ImportExportAdmin::csvDecode($csv,$delimiter, $enclosure, $escapechar, null);

        $languages = $CLICSHOPPING_Language->getLanguages();

        if (\is_array($csv)) {
          foreach ($csv as $row) {
// Set new category data
            $fields = [
              'categories_id',
              'categories_image',
              'parent_id',
              'sort_order',
              'date_added',
              'last_modified',
              'status',
              'customers_group_id',
  //            'google_taxonomy_id',
              'language_id',
              'categories_name',
              'categories_description',
              'categories_head_title_tag',
              'categories_head_desc_tag',
              'categories_head_keywords_tag'
            ];
//
// Insert inside categories table
//
            foreach ($fields as $field) {
              if (isset($row[$field])) $data[$field] = $row[$field];
            }

            if (empty($data['date_added'])) {
              $date_added = 'now()';
            } else {
              $date_added = $data['date_added'];
            }

            $sql_data_array = [
              'categories_image' => $data['categories_image'],
              'parent_id' => (int)$data['parent_id'],
              'sort_order' => (int)$data['sort_order'],
              'last_modified' => 'now()',
              'date_added' => $date_added,
              'status' => (int)$data['status'],
              'customers_group_id' => (int)$data['customers_group_id'],
  //            'google_taxonomy_id' => (int)$data['google_taxonomy_id'],
            ];

            $Qcheck = $this->app->db->get('categories', 'categories_id', ['categories_id' => $data['categories_id'], 'parent_id' => $data['parent_id']]);

            if ($data['parent_id']) {
              if ($Qcheck->fetch()) {
                $insert_sql_data = ['categories_id' => $data['categories_id']];
                $this->app->db->save('categories', $sql_data_array, $insert_sql_data);
//
// update inside categories description table
//
                $Qcheck = $this->app->db->get('categories', 'categories_id', ['categories_id' => $data['categories_id']]);

                if ($Qcheck->fetch()) {
                  $sql_data_array = [
                    'categories_name' => substr($data['categories_name'], 0, 255),
                    'categories_description' => $data['categories_description'],
                    'categories_head_title_tag' => substr($data['categories_head_title_tag'], 0, 255),
                    'categories_head_desc_tag' => substr($data['categories_head_desc_tag'], 0, 255),
                    'categories_head_keywords_tag' => substr($data['categories_head_keywords_tag'], 0, 255),
                  ];

                  $insert_sql_data = [
                    'categories_id' => (int)$data['categories_id'],
                    'language_id' => (int)$data['language_id']
                  ];

                  $this->app->db->save('categories_description', $sql_data_array, $insert_sql_data);
                }
              } else {
//
// Insert new categories
//
                 $this->app->db->save('categories', $sql_data_array);

                  $Qcategories = $this->app->db->prepare('select categories_id
                                                         from :table_categories
                                                         order by categories_id desc
                                                         limit 1
                                                       ');
                  $Qcategories->execute();
                  $categories_id = $Qcategories->valueInt('categories_id');

//
// categories description
//
                for ($i = 0, $n = \count($languages); $i < $n; $i++) {
                  if ((int)$data['language_id'] == $languages[$i]['id']) {
                    $sql_data_array = [
                      'language_id' => (int)$data['language_id'],
                      'categories_name' => substr($data['categories_name'], 0, 255),
                      'categories_description' => $data['categories_description'],
                      'categories_head_title_tag' => substr($data['categories_head_title_tag'], 0, 255),
                      'categories_head_desc_tag' => substr($data['categories_head_desc_tag'], 0, 255),
                      'categories_head_keywords_tag' => substr($data['categories_head_keywords_tag'], 0, 255),
                    ];

                    $insert_sql_data = [
                      'categories_id' => (int)$categories_id,
                      'language_id' => (int)$languages[$i]['id']
                    ];

                    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

                    $this->app->db->save('categories_description', $sql_data_array);
                  } else {
                    $sql_data_array = [
                      'categories_id' => (int)$categories_id,
                      'language_id' => (int)$languages[$i]['id'],
                      'categories_name' => substr($data['categories_name'], 0, 255),
                     ];

                    $this->app->db->save('categories_description', $sql_data_array);
                  }
                }
              }
            }

            $CLICSHOPPING_Hooks->call('ImportExportCategories', 'Save');
          }
        } else {
          echo '<div class="alert alert-warning">Import Error</div>';
        }


        Cache::clear('categories');
      }

      $this->app->redirect('ImportExport&message=success_import_csv');
    }
  }