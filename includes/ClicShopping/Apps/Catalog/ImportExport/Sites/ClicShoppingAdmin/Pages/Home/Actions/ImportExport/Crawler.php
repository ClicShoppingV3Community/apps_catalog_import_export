<?php

//https://github.com/Imangazaliev/DiDOM
  namespace ClicShopping\Apps\Catalog\ImportExport\Sites\ClicShoppingAdmin\Pages\Home\Actions\ImportExport;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Cache;
  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  use ClicShopping\Apps\Catalog\ImportExport\Classes\ClicShoppingAdmin\Amazon\Crawler as CrawlerClass;

  class Crawler extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;
    protected $productsAdmin;
    protected mixed $db;
    protected mixed $lang;

    public function execute()
    {
      $this->app = Registry::get('ImportExport');
      $this->productsAdmin = Registry::get('ProductsAdmin');
      $this->db = Registry::get('Db');
      $this->lang = Registry::get('Language');

      if (!Registry::exists('CrawlerClass')) {
        Registry::set('CrawlerClass', new CrawlerClass());
      }

      $crawler	=	Registry::get('CrawlerClass');

      set_time_limit(0);

      if (!empty($_POST['navito_url']) && isset($_POST['navito_checkbox'])) {
        $base_url = HTML::sanitize($_POST['amazon_url']);
        $css_product_name_navito = HTML::sanitize($_POST['css_product_name']);
        $css_price_navito = HTML::sanitize($_POST['css_price']);
        $css_products_descriptiton_navito = HTML::sanitize($_POST['css_products_descriptiton']);
        $document_navito = $crawler->getContent($base_url);

//*******************************
//Navito
//*******************************
        $parameters_content_navito = $css_product_name_navito;

        if ($document_navito->has($parameters_content_navito)) {
          $content_navito = $document_navito->first($parameters_content_navito); // content of the div
          $products_name_navito = $content_navito->firstChild()->text();
          var_dump($products_name_navito);
//          $products_name = $content->child(1)->text()); //geek mania
          $error = false;
        } else {
          $error = true;
        }

// price
        $parameters_content_navito = $css_price_navito;

        if ($document_navito->has($parameters_content_navito)) {
          $content = $document_navito->first($parameters_content_navito); // content of the div
          $products_price_navito = $content->firstChild()->text();
//          $products_price = (float)$products_price;
          $products_price_navito = str_replace('$', '', $products_price_navito);
          $products_price_navito = str_replace('€', '', $products_price_navito);
          var_dump($products_price_navito);
        }


//description
        $parameters_content_navito = $css_products_descriptiton_navito;

        if ($document_navito->has($parameters_content_navito)) {
          $content_navito = $document_navito->first($parameters_content_navito);
          //$products_description_navito = $content_navito->firstChild()->text();
          $products_description_navito = $content_navito->innerHtml();

//          $products_description = str_replace('-', '', $products_description);
          var_dump($products_description_navito);
        }
      }

//*******************************
//Amazon
//*******************************
//      if (!empty($_POST['amazon_url']) && isset($_POST['amazon_checkbox'])) {
      if (!empty($_POST['amazon_url'])) {

        $base_url = HTML::sanitize($_POST['amazon_url']);
        $css_product_name_amazon = HTML::sanitize($_POST['css_product_name_amazon']);
        $css_price_amazon = HTML::sanitize($_POST['css_price_amazon']);
        $css_products_descriptiton_amazon = HTML::sanitize($_POST['css_products_descriptiton_amazon']);
        $document_amazon = $crawler->getContent($base_url);
        $parameters_content_amazon = $css_product_name_amazon;

// products name
        if ($document_amazon->has($parameters_content_amazon)) {
          $content_amazon = $document_amazon->first($parameters_content_amazon); // content of the div
          $products_name_amazon = $content_amazon->firstChild()->text();
          var_dump($products_name_amazon);

          //          $products_name = $content->child(1)->text()); //geek mania
          $error = false;
        } else {
          $error = true;
        }

// price
        $parameters_content_amazon = $css_price_amazon;

        if ($document_amazon->has($parameters_content_amazon)) {
          $content_amazon = $document_amazon->first($parameters_content_amazon); // content of the div
          $products_price_amazon = $content_amazon->firstChild()->text();
          //          $products_price = (float)$products_price;
          $products_price_amazon = str_replace('$', '', $products_price_amazon);
          $products_price_amazon = str_replace('€', '', $products_price_amazon);
          var_dump($products_price_amazon);
        }

//description
        $parameters_content_amazon = $css_products_descriptiton_amazon;

        if ($document_amazon->has($parameters_content_amazon)) {
          $content_amazon = $document_amazon->first($parameters_content_amazon);
          //$products_description_amazon = $content_amazon->Child(3)->text(); //amazon
          $products_description_amazon = $content_amazon->innerHtml();
          var_dump($products_description_amazon);
        }
      }




      exit;



//var_dump($products_image);
// all images with the extension png
//var_dump($document->find('img[src$=png]'));

      if ($error === true) {
        echo 'export info - Erreur sur le nom du produit ou vous avez sélectionné plusieurs categories, Veuillez revenir sur votre page précédente';
      } else {
        $category_id = HTML::sanitize($_POST['category_id'][0]);
        $products_sku = HTML::sanitize($_POST['product_sku']) ?? '';

        if(!empty($products_sku)) {
          $products_model = CONFIGURATION_PREFIX_MODEL . $products_sku;
        } else {
          $products_model = '';
        }

// display information
        echo 'export info';
        echo '<br><br>';
        echo 'products name : ' . $products_name;
        echo '<br><br>';
        echo 'categories_id : ' . $category_id ?? '';
        echo '<br><br>';
        echo 'product model : ' . $products_model ?? '';
        echo '<br><br>';
        echo 'product sku : ' . $products_sku ?? '';
        echo '<br><br>';
        echo 'product description : ' . $products_description ?? '';
        echo '<br><br>';
        echo 'product price : ' . $products_price ?? '';
        echo '<br><br>';
        echo 'Veuillez mettre à jour votre fiche produit, l\'activer et mettre à jour vos groupes produits';
exit;
//********************
//product insert
//*******************



        $sql_data_array = [
          'products_model' => $products_model,
          'products_sku' => $products_sku,
          'products_price' => (float)$products_price,
          'products_status' =>  0,
          'parent_id' => (int)$category_id,
          'products_quantity' => 1,
          'products_ean'=> $products_sku,
          'products_view' => 1,
          'orders_view' => 1,
          'products_weight' => 0,
          'products_tax_class_id' => 1,
          'products_cost' => 0,

          'admin_user_name' => AdministratorAdmin::getUserAdmin()
        ];

        $insert_sql_data = ['products_date_added' => 'now()'];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->db->save('products', $sql_data_array);

        $products_id = $this->db->lastInsertId();
//*******************
//product description
//*******************
        $languages = $this->lang->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];

          $sql_data_array = [
            'products_name' => HTML::sanitize($products_name),
            'products_description' => $products_description,
          ];

          $insert_sql_data = [
            'products_id' => (int)$products_id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $this->db->save('products_description', $sql_data_array);
        } //end for

//********************
//product categorie insert
//*******************
         $Qproducts = $this->app->db->prepare('select products_id
                                              from :table_products
                                              order by products_id desc
                                              limit 1
                                            ');
        $Qproducts->execute();

        $products_id = $Qproducts->valueInt('products_id');

        $sql_array = [
          'products_id' => (int)$products_id,
          'categories_id' => (int)$category_id
        ];

        $this->app->db->save('products_to_categories', $sql_array);
       }

        Cache::clear('categories');
        Cache::clear('products-also_purchased');
        Cache::clear('products_related');
        Cache::clear('products_cross_sell');
        Cache::clear('upcoming');

        exit();

        $this->app->redirect('ImportExport&message=success_import_csv');
    }
  }
