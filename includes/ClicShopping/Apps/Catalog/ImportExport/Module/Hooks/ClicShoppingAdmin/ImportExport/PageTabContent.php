<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\ImportExport\Module\Hooks\ClicShoppingAdmin\ImportExport;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Catalog\ImportExport\ImportExport as ImportExportApp;

  class PageTabContent implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('ImportExportApp')) {
        Registry::set('ImportExport', new ImportExportApp());
      }

      $this->app = Registry::get('ImportExport');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/page_tab_content');
    }

    public function display()
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
/*
      if (!\defined('CLICSHOPPING_APP_CATEGORIES_CT_STATUS') || CLICSHOPPING_APP_CATEGORIES_CT_STATUS == 'False') {
        return false;
      }
*/
      $content = '<!-- start Amazon hook -->';

      $tab_title = 'Amazon Import';
      $title = 'Amazon Import';
      $configure_button = 'button';

//      $content .= '</div>';
      $content .= '<!-- End Amazon hook -->';

      $output = <<<EOD
<!-- ######################## -->
<!--  Start Amazon Hooks      -->
<!-- ######################## -->

<div class="tab-pane" id="section_importExport_content">
  <div class="mainTitle">
    <span class="col-md-10">************************{$title}</span>
    <span class="col-md-2 text-end">*************************{$configure_button}</span>
  </div>
  {$content}
</div>

<script>
$('#section_importExport_content').appendTo('#importExportTab .tab-content');
$('#importExportTab .nav-tabs').append('    <li class="nav-item"><a data-bs-target="#section_importExport_content" role="tab" data-bs-toggle="tab" class="nav-link">{$tab_title}</a></li>');
</script>
<!-- ######################## -->
<!--  End Amazon App      -->
<!-- ######################## -->

EOD;

      return $output;
    }
  }
