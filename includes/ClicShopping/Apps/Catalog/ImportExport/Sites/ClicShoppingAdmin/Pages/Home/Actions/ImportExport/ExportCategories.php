<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\ImportExport\Sites\ClicShoppingAdmin\Pages\Home\Actions\ImportExport;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\ImportExport\Classes\ClicShoppingAdmin\ImportExportAdmin;

  class ExportCategories extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function execute()
    {
      $this->app = Registry::get('ImportExport');

      if (isset($_GET['ImportExport'], $_GET['export']) && $_GET['export'] == 'categories') {
        $delimiter = HTML::sanitize($_POST['delimiter']);
        $enclosure = HTML::sanitize($_POST['enclosure']);
        $escapechar = HTML::sanitize($_POST['escape']);
        $language_id = HTML::sanitize($_POST['language']);
        $lineEnding = HTML::sanitize($_POST['lineEnding']);
        $output = HTML::sanitize($_POST['output']);

        $csv = [];

        $Qcategories = $this->app->db->prepare('select c.*,
                                                        cd.* 
                                                 from :table_categories c,
                                                      :table_categories_description cd
                                                 where c.categories_id = cd.categories_id
                                                 and language_id = :language_id                                                 
                                                ');
        $Qcategories->bindInt('language_id', $language_id);
        $Qcategories->execute();

        while ($Qcategories->fetch()) {
          $csv[] = [
            'categories_id' => $Qcategories->valueInt('categories_id'),
            'categories_image' => $Qcategories->value('categories_image'),
            'parent_id' => (int)$Qcategories->valueInt('parent_id'),
            'sort_order' => (int)$Qcategories->valueInt('sort_order'),
            'last_modified' => $Qcategories->value('last_modified'),
            'virtual_categories' => (int)$Qcategories->valueInt('virtual_categories'),
            'status' => (int)$Qcategories->valueInt('status'),
            'customers_group_id' => (int)$Qcategories->valueInt('customers_group_id'),
//            'google_taxonomy_id' => (int)$Qcategories->valueInt('google_taxonomy_id'),
            'language_id' => (int)$Qcategories->valueInt('language_id'),
            'categories_name' => $Qcategories->value('categories_name'),
            'categories_description' => $Qcategories->value('categories_description'),
            'categories_head_title_tag' => $Qcategories->value('categories_head_title_tag'),
            'categories_head_desc_tag' => $Qcategories->value('categories_head_desc_tag'),
            'categories_head_keywords_tag' => $Qcategories->value('categories_head_keywords_tag')
          ];
        }

        ob_clean();

        if ($output == 'screen') {
          header('Content-Type: text/plain; charset=utf-8');
        } else {
          header('Content-Type: application/csv; charset=utf-8');
          header('Content-Disposition: attachment; filename=categories-' . $language_id . '.csv');
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