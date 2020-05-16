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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\ImportExport\Classes\ClicShoppingAdmin\ImportExportAdmin;

  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_language = Registry::get('Language');

  $CLICSHOPPING_ImportExport = Registry::get('ImportExport');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $array_language = $CLICSHOPPING_language->getAllLanguage();

   if (isset($_GET['message'])) {
     if ($_GET['message'] == 'error_file') {
?>
    <div class="alert alert-warning" role="alert">
      <?php echo $CLICSHOPPING_ImportExport->getDef('error_must_select_file_to_upload'); ?>
    </div>
<?php
     } elseif ($_GET['message'] == 'error_decode') {
?>
       <div class="alert alert-warning" role="alert">
         <?php echo $CLICSHOPPING_ImportExport->getDef('error_failed_decoding_csv'); ?>
       </div>
<?php
     } elseif ($_GET['message'] == 'success_product') {
?>
       <div class="alert alert-success" role="alert">
         <?php echo $CLICSHOPPING_ImportExport->getDef('success_import_products_csv'); ?>
       </div>
<?php
     } else {
?>
       <div class="alert alert-success" role="alert">
         <?php echo $CLICSHOPPING_ImportExport->getDef('success_import_csv'); ?>
       </div>
<?php
     }
   }
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading">
            <?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/comparison_export.gif', $CLICSHOPPING_ImportExport->getDef('heading_title'), '40', '40'); ?>
          </span>
          <span class="col-md-8 pageHeading">
            <?php echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('heading_title'); ?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div id="categoriesTabs" style="overflow: auto;">
    <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">
      <li class="nav-item">
        <?php echo '<a href="#tab1" role="tab" data-toggle="tab" class="nav-link active">' . $CLICSHOPPING_ImportExport->getDef('tab_categories') . '</a>'; ?>
      </li>
      <li class="nav-item">
        <?php echo '<a href="#tab2" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_ImportExport->getDef('tab_products') . '</a>'; ?>
      </li>


      <li class="nav-item">
        <?php echo '<a href="#tab3" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_ImportExport->getDef('tab_import_product_amazon') . '</a>'; ?>
      </li>

    </ul>
    <div class="tabsClicShopping">
      <div class="tab-content">
        <?php
        // -------------------------------------------------------------------
        //          Import
        // -------------------------------------------------------------------
        ?>
        <div class="tab-pane active" id="tab1">
          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <?php echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_import_categories'); ?>
                </div>
                <div class="card-body">
                  <?php echo HTML::form('import_category', $CLICSHOPPING_ImportExport->link('ImportExport&ImportCategories&import=categories'), 'post', 'enctype="multipart/form-data"'); ?>
                  <div>
                    <?php echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_csv_file'); ?>
                    <?php echo HTML::fileField('import_categories', 'id="file_import_categories" accept=".csv"'); ?>
                  </div>
                  <div class="separator"></div>
                  <div class="row" id="categories_import_delimiter">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_delimiter');
                        $array_delimiter = ImportExportAdmin::delimiter();
                        echo HTML::selectField('delimiter', $array_delimiter);
                      ?>
                    </span>
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_enclosure');
                        $array_enclosure = ImportExportAdmin::enclosure();
                        echo HTML::selectField('enclosure', $array_enclosure);
                      ?>
                    </span>
                  </div>
                  <div class="separator"></div>
                  <div class="row" id="categories_import_escape">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_escape_caracter');
                        $array_escape = ImportExportAdmin::escape();
                        echo HTML::selectField('escape', $array_escape);
                      ?>
                    </span>
                  </div>
                  <div class="separator"></div>
                  <div class="text-md-center">
                    <?php echo HTML::button($CLICSHOPPING_ImportExport->getDef('text_import'), null, null, 'success'); ?>
                  </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <?php echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_export_categories'); ?>
                </div>
                <div class="card-body">
                  <?php echo HTML::form('export_category', $CLICSHOPPING_ImportExport->link('ImportExport&ExportCategories&export=categories')); ?>
                  <div class="separator"></div>
                  <div class="row" id="categories_export_delimiter">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_delimiter');
                        $array_delimiter = ImportExportAdmin::delimiter(false);
                        echo HTML::selectField('delimiter', $array_delimiter);
                      ?>
                    </span>
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_enclosure');
                        $array_enclosure = ImportExportAdmin::enclosure();
                        echo HTML::selectField('enclosure', $array_enclosure);
                      ?>
                    </span>
                  </div>
                  <div class="separator"></div>
                  <div class="row" id="categories_export_escape">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_escape_caracter');
                        $array_escape = ImportExportAdmin::escape();
                        echo HTML::selectField('escape', $array_escape);
                      ?>
                    </span>
                    <span class="col-md-6">
                    <?php
                      echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_line_ending');
                      $array_line_ending = ImportExportAdmin::lineEnding();
                      echo HTML::selectField('line_ending', $array_line_ending);
                    ?>
                    </span>
                  </div>
                  <div class="separator"></div>

                  <div class="row" id="categories_export_escape">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_output');
                        $array_output = ImportExportAdmin::output();
                        echo HTML::selectField('output', $array_output);
                      ?>
                    </span>

                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_language');
                        echo HTML::selectField('language', $array_language);
                      ?>
                    </span>
                  </div>
                  <div class="separator"></div>
                  <div class="text-md-center">
                    <?php echo HTML::button($CLICSHOPPING_ImportExport->getDef('text_export'), null, null, 'primary'); ?>
                  </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="separator"></div>
          <div class="alert alert-info" role="alert">
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_ImportExport->getDef('title_help_description')) . ' ' . $CLICSHOPPING_ImportExport->getDef('title_help_description'); ?></div>
            <div class="separator"></div>
            <div class="row">
              <div class="col-md-12">
                <blockquote>
                  <ul>
                    <?php echo $CLICSHOPPING_ImportExport->getDef('text_description'); ?>
                  </ul>
                </blockquote>
              </div>
            </div>
          </div>
        </div>

        <?php
        // -------------------------------------------------------------------
        //       Product
        // -------------------------------------------------------------------
        ?>

        <div class="tab-pane" id="tab2">
          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <?php echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_import_product'); ?>
                </div>
                <div class="card-body">
                  <?php echo HTML::form('importproducts', $CLICSHOPPING_ImportExport->link('ImportExport&ImportProducts&import=products'), 'post', 'enctype="multipart/form-data"'); ?>
                  <div>
                    <?php echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_csv_file'); ?>
                    <?php echo HTML::fileField('import_products', 'id="file_import_products" accept=".csv"'); ?>
                  </div>
                  <div class="separator"></div>
                  <div class="row" id="products_import_delimiter">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_delimiter');
                        $array_delimiter = ImportExportAdmin::delimiter();
                        echo HTML::selectField('delimiter', $array_delimiter);
                      ?>
                    </span>
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_enclosure');
                        $array_enclosure = ImportExportAdmin::enclosure();
                        echo HTML::selectField('enclosure', $array_enclosure);
                      ?>
                    </span>
                  </div>
                  <div class="separator"></div>
                  <div class="row" id="products_import_escape">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_escape_caracter');
                        $array_escape = ImportExportAdmin::escape();
                        echo HTML::selectField('escape', $array_escape);
                      ?>
                    </span>
                  </div>
                  <div class="separator"></div>
                  <div class="row" id="products_import_output_quick_update">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_all_import_quick_update') . ' ';
                        echo HTML::checkboxField('import_all_products', false);
                      ?>
                    </span>
                    <span class="col-md-6" id="products_import_input_field_quick_update">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_import_quick_update') . ' ';
                        echo HTML::checkboxField('import_products_quick_update', false);
                      ?>
                    </span>
                  </div>
                  <div class="separator"></div>
                  <div class="text-md-center">
                    <?php echo HTML::button($CLICSHOPPING_ImportExport->getDef('text_import'), null, null, 'success'); ?>
                  </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <?php echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_export_products'); ?>
                </div>
                <div class="card-body">
                  <?php echo HTML::form('export_products', $CLICSHOPPING_ImportExport->link('ImportExport&ExportProducts&export=products'), 'post'); ?>
                  <div class="separator"></div>
                  <div class="row" id="products_export_delimiter">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_delimiter');
                        $array_delimiter = ImportExportAdmin::delimiter(false);
                        echo HTML::selectField('delimiter', $array_delimiter);
                      ?>
                    </span>
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_enclosure');
                        $array_enclosure = ImportExportAdmin::enclosure();
                        echo HTML::selectField('enclosure', $array_enclosure);
                      ?>
                      </span>
                  </div>
                  <div class="separator"></div>

                  <div class="row" id="products_export_escape">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_escape_caracter');
                        $array_escape = ImportExportAdmin::escape();
                        echo HTML::selectField('escape', $array_escape);
                      ?>
                    </span>
                    <span class="col-md-6" id="products_export_line_ending">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_line_ending');
                        $array_line_ending = ImportExportAdmin::lineEnding();
                        echo HTML::selectField('line_ending', $array_line_ending);
                      ?>
                    </span>
                  </div>
                  <div class="separator"></div>

                  <div class="row" id="products_export_output">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_output');
                        $array_output = ImportExportAdmin::output();
                        echo HTML::selectField('output', $array_output);
                      ?>
                    </span>
                    <span class="col-md-6" id="products_export_language">
                      <?php
                      echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_language');
                      echo HTML::selectField('language', $array_language);
                      ?>
                    </span>
                  </div>
                  <div class="separator"></div>


                  <div class="row" id="products_export_output_quick_update">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_all_export_quick_update') . ' ';
                        echo HTML::checkboxField('export_all_products', null, false);
                      ?>
                    </span>
                    <span class="col-md-6" id="products_export_inputfield_quick_update">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_export_quick_update') . ' ';
                        echo HTML::checkboxField('export_quick_update', null, false);
                      ?>
                    </span>
                  </div>
                  <div class="separator"></div>

                  <div class="text-md-center">
                    <?php echo HTML::button($CLICSHOPPING_ImportExport->getDef('text_export'), null, null, 'primary'); ?>
                  </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>


        <?php
          // -------------------------------------------------------------------
          //       Product
          // -------------------------------------------------------------------
        ?>

        <div class="tab-pane" id="tab3">
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                  <?php echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_import_amazon_ley'); ?>
                </div>
                <div class="card-body">
                  <div class="col-md-12">
                    <div class="row">
                      <span class="col-md-4">
                          <span class="col-md-4"><?php echo $CLICSHOPPING_ImportExport->getDef('text_amazon_access_key'); ?></span>
                          <span><?php echo HTML::inputField('amazon_access_key', $access_key); ?></span>
                      </span>
                      <span class="col-md-4">
                          <span class="col-md-4"><?php echo $CLICSHOPPING_ImportExport->getDef('text_amazon_secret_key'); ?></span>
                          <span><?php echo HTML::inputField('amazon_secret_key', $secret_key); ?></span>
                      </span>
                      <span class="col-md-4">
                          <span class="col-md-4"><?php echo $CLICSHOPPING_ImportExport->getDef('text_amazon_asociate_id'); ?></span>
                          <span><?php echo HTML::inputField('amazon_associate_id', $associate_id); ?></span>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="separator"></div>

          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <?php echo HTML::form('importProductsAmazon', $CLICSHOPPING_ImportExport->link('ImportExport&ImportAmazonProducts&import=amazon'), 'post', 'enctype="multipart/form-data"'); ?>

                <div class="card-header">
                  <?php echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_import_products_amazon'); ?>
                </div>
                <div class="card-body">
                  <div class="col-md-12">
                    <div class="row">
                      <span class="col-md-4"><?php echo $CLICSHOPPING_ImportExport->getDef('text_amazon_id_type'); ?></span>
                      <span><?php echo HTML::inputField('amazon_id_type', null, 'placeholder="ASIN"'); ?></span>
                    </div>
                    <div class="separator"></div>
                    <div class="row">
                      <span class="col-md-4"><?php echo $CLICSHOPPING_ImportExport->getDef('text_amazon_item_id'); ?></span>
                      <span><?php echo HTML::inputField('amazon_item_Id', null, 'placeholder="B00BGO0Q9O"'); ?></span>
                    </div>
                   </div>
                  <div class="separator"></div>
                  <div class="text-md-center">
                    <?php echo HTML::button($CLICSHOPPING_ImportExport->getDef('text_import'), null, null, 'success'); ?>
                  </div>
                </div>
                </form>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <?php echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_import_bulk_products_amazon'); ?>
                </div>
                <div class="card-body">
                  <?php echo HTML::form('importBulkProductsAmazon', $CLICSHOPPING_ImportExport->link('ImportExport&ImportAmazonProducts&import=amazon_bulk'), 'post', 'enctype="multipart/form-data"'); ?>
                  <div>
                    <?php echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_csv_file'); ?>
                    <?php echo HTML::fileField('bukl_import_categories', 'id="bulk_file_import_categories" accept=".csv"'); ?>
                  </div>


                  <div class="row" id="products_amazon_import_delimiter">
                    <div class="separator"></div>
                    <div class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_delimiter');
                        $array_delimiter = ImportExportAdmin::delimiter();
                        echo HTML::selectField('delimiter', $array_delimiter);
                      ?>
                    </div>
                    <div class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_enclosure');
                        $array_enclosure = ImportExportAdmin::enclosure();
                        echo HTML::selectField('enclosure', $array_enclosure);
                      ?>
                    </div>
                  </div>
                  <div class="separator"></div>
                  <div class="row" id="products_amazon_import_escape">
                    <span class="col-md-6">
                      <?php
                        echo '&nbsp;' . $CLICSHOPPING_ImportExport->getDef('text_escape_caracter');
                        $array_escape = ImportExportAdmin::escape();
                        echo HTML::selectField('escape', $array_escape);
                      ?>
                    </span>
                  </div>
                  <div class="separator"></div>
                  <div class="text-md-center">
                    <?php echo HTML::button($CLICSHOPPING_ImportExport->getDef('text_import'), null, null, 'primary'); ?>
                  </div>
                  </form>
                </div>
              </div>
            </div>
          </div>



        <?php
          //***********************************
          // extension
          //***********************************
          //echo $CLICSHOPPING_Hooks->output('ImportExport', 'PageTab', null, 'display');


/*

          //https://www.sitepoint.com/amazon-product-api-exploration-lets-build-a-product-search/

*/
        ?>
      </div>
    </div>
  </div>
</div>
