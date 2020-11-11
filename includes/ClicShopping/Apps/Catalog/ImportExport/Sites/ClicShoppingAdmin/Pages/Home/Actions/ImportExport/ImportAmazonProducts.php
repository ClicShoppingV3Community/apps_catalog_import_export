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
  /*
   *
   * https://webservices.amazon.com/paapi5/documentation/quick-start/using-sdk.html
pseudo : loic28@yahoo.fr
login : Yae8mr2o*
associate : figuresshop20-20
  https://partenaires.amazon.fr/home/account
   */

  namespace ClicShopping\Apps\Catalog\ImportExport\Sites\ClicShoppingAdmin\Pages\Home\Actions\ImportExport;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\ImportExport\Classes\ClicShoppingAdmin\ImportExportAdmin;

  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\Amazon\Configuration;

  use Amazon\ProductAdvertisingAPI\v1\ApiException;
  use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
  use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
  use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;
  use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
  use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;


  class ImportAmazonProducts extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;
    protected $lang;
    protected $hooks;
    protected $amazon;

    public function __construct()
    {
      $this->app = Registry::get('ImportExport');
      $this->lang = Registry::get('Language');

      $this->hooks = Registry::get('Hooks');

      if(!Registry::exists('Configuration')) {
        Registry::set('Configuration', new Configuration());
        $this->configuration = Registry::get('Configuration');
      }
    }

    /**
     * @return false
     */
    private function check()
    {
      if (!defined('CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS') || CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS == 'False') {
        return false;
      }
    }


    public function execute()
    {
      $this->check();

      $access_key = HTML::sanitize($_POST['access_key']);
      $secret_key = HTML::sanitize($_POST['secret_key']);
      $partner_tag = HTML::sanitize($_POST['partner_tag']); // figures05-21
      $region = HTML::sanitize($_POST['region']);
      $host = HTML::sanitize($_POST['amazon_host']);

      if (isset($_GET['ImportExport']) && $_GET['ImportAmazonProducts']) {
        $config = $this->amazon->getConfig();;
/*
        $config->setAccessKey($access_key);
        $config->setSecretKey($secret_key);
        $partnerTag = $partner_tag;
        $config->setHost($host); //webservices.amazon.com
        $config->setRegion($region); //'us-east-1'
        $apiInstance = new DefaultApi(new GuzzleHttp\Client(), $config);
*/

        $config = [];

        $this->configuration($config);




        if ($_GET['import'] == 'amazon') {
          $amazon_id_type = HTML::sanitize($_POST['amazon_id_type']);
          $item_id = HTML::sanitize($_POST['item_id']);

# Request initialization
          $searchIndex = 'All';
# Specify keywords
          $keyword = 'figures';
# Specify item count to be returned in search result
          $itemCount = 1;

          /*
           * Choose resources you want from SearchItemsResource enum
           * For more details, refer: https://webservices.amazon.com/paapi5/documentation/search-items.html#resources-parameter
           */
          $resources = [
            SearchItemsResource::ITEM_INFOTITLE,
            SearchItemsResource::OFFERSLISTINGSPRICE
          ];

# Forming the request
          $searchItemsRequest = new SearchItemsRequest();
          $searchItemsRequest->setSearchIndex($searchIndex);
          $searchItemsRequest->setKeywords($keyword);
          $searchItemsRequest->setItemCount($itemCount);
          $searchItemsRequest->setPartnerTag($partnerTag);
          $searchItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
          $searchItemsRequest->setResources($resources);

# Validating request
          $invalidPropertyList = $searchItemsRequest->listInvalidProperties();
          $length = count($invalidPropertyList);

          if ($length > 0) {
            echo 'Error forming the request', PHP_EOL;
            foreach ($invalidPropertyList as $invalidProperty) {
              echo $invalidProperty, PHP_EOL;
            }
            return;
          }

# Sending the request
          try {
            $searchItemsResponse = $apiInstance->searchItems($searchItemsRequest);

            echo 'API called successfully', PHP_EOL;
            echo 'Complete Response: ', $searchItemsResponse, PHP_EOL;

            # Parsing the response
            if ($searchItemsResponse->getSearchResult() != null) {
              echo 'Printing first item information in SearchResult:', PHP_EOL;
              $item = $searchItemsResponse->getSearchResult()->getItems()[0];
              if ($item !== null) {
                if ($item->getASIN() !== null) {
                  echo 'ASIN: ', $item->getASIN(), PHP_EOL;
                }
                if ($item->getDetailPageURL() !== null) {
                  echo 'DetailPageURL: ', $item->getDetailPageURL(), PHP_EOL;
                }
                if ($item->getItemInfo() !== null
                  and $item->getItemInfo()->getTitle() !== null
                  and $item->getItemInfo()->getTitle()->getDisplayValue() != null) {
                  echo 'Title: ', $item->getItemInfo()->getTitle()->getDisplayValue(), PHP_EOL;
                }
                if ($item->getOffers() !== null
                  and $item->getOffers() !== null
                  and $item->getOffers()->getListings() != null
                  and $item->getOffers()->getListings()[0]->getPrice() != null
                  and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() != null) {
                  echo 'Buying price: ', $item->getOffers()->getListings()[0]->getPrice()
                    ->getDisplayAmount(), PHP_EOL;
                }
              }
            }

            if ($searchItemsResponse->getErrors() !== null) {
              echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
              echo 'Error code: ', $searchItemsResponse->getErrors()[0]->getCode(), PHP_EOL;
              echo 'Error message: ', $searchItemsResponse->getErrors()[0]->getMessage(), PHP_EOL;
            }
          } catch (ApiException $exception) {
            echo 'Error calling PA-API 5.0!', PHP_EOL;
            echo 'HTTP Status Code: ', $exception->getCode(), PHP_EOL;
            echo 'Error Message: ', $exception->getMessage(), PHP_EOL;

            if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
              $errors = $exception->getResponseObject()->getErrors();

              foreach ($errors as $error) {
                echo 'Error Type: ', $error->getCode(), PHP_EOL;
                echo 'Error Message: ', $error->getMessage(), PHP_EOL;
              }
            } else {
              echo 'Error response body: ', $exception->getResponseBody(), PHP_EOL;
            }
          } catch (Exception $exception) {
            echo 'Error Message: ', $exception->getMessage(), PHP_EOL;
          }
  } else {
//**********************************
//*********** Bulk ************
//**********************************
          $delimiter = HTML::sanitize($_POST['delimiter']);
          $enclosure = HTML::sanitize($_POST['enclosure']);
          $escapechar = HTML::sanitize($_POST['escape']);

          if (!isset($_FILES['import_products']['tmp_name']) || is_uploaded_file($_FILES['import_products']['tmp_name']) === false) {
            $this->app->redirect('ImportExport&message=error_file');
          }

          ob_clean();

          header('Content-Type: text/plain; charset=UTF-8');

          $csv = file_get_contents($_FILES['import_products']['tmp_name']);

          $csv = ImportExportAdmin::csvDecode($csv, $delimiter, $enclosure, $escapechar, null);

          foreach ($csv as $row) {
            $fields = [
              'keywords',
              ];
//
// Insert inside products table
//
            foreach ($fields as $field) {
              if (isset($row[$field])) $data[$field] = $row[$field];
            }



# Request initialization
            $searchIndex = 'All';
# Specify keywords
            $keyword = $data['keywords'];
# Specify item count to be returned in search result
            $itemCount = 1;

/*
 * Choose resources you want from SearchItemsResource enum
 * For more details, refer: https://webservices.amazon.com/paapi5/documentation/search-items.html#resources-parameter
 */
            $resources = [
              SearchItemsResource::ITEM_INFOTITLE,
              SearchItemsResource::OFFERSLISTINGSPRICE
            ];

# Forming the request
            $searchItemsRequest = new SearchItemsRequest();
            $searchItemsRequest->setSearchIndex($searchIndex);
            $searchItemsRequest->setKeywords($keyword);
            $searchItemsRequest->setItemCount($itemCount);
            $searchItemsRequest->setPartnerTag($partnerTag);
            $searchItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
            $searchItemsRequest->setResources($resources);

# Validating request
            $invalidPropertyList = $searchItemsRequest->listInvalidProperties();
            $length = count($invalidPropertyList);

            if ($length > 0) {
              echo 'Error forming the request', PHP_EOL;
              foreach ($invalidPropertyList as $invalidProperty) {
                echo $invalidProperty, PHP_EOL;
              }
              return;
            }

# Sending the request
            try {
              $searchItemsResponse = $apiInstance->searchItems($searchItemsRequest);

              echo 'API called successfully', PHP_EOL;
              echo 'Complete Response: ', $searchItemsResponse, PHP_EOL;

# Parsing the response
              if ($searchItemsResponse->getSearchResult() != null) {
                echo 'Printing first item information in SearchResult:', PHP_EOL;
                $item = $searchItemsResponse->getSearchResult()->getItems()[0];

                if ($item != null) {
                  if ($item->getASIN() != null) {
                    echo 'ASIN: ', $item->getASIN(), PHP_EOL;
                  }

                  if ($item->getDetailPageURL() != null) {
                    echo 'DetailPageURL: ', $item->getDetailPageURL(), PHP_EOL;
                  }

                  if ($item->getItemInfo() != null
                    and $item->getItemInfo()->getTitle() != null
                    and $item->getItemInfo()->getTitle()->getDisplayValue() != null) {
                    echo 'Title: ', $item->getItemInfo()->getTitle()->getDisplayValue(), PHP_EOL;
                  }

                  if ($item->getOffers() != null
                    and $item->getOffers() != null
                    and $item->getOffers()->getListings() != null
                    and $item->getOffers()->getListings()[0]->getPrice() != null
                    and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() != null) {
                    echo 'Buying price: ', $item->getOffers()->getListings()[0]->getPrice()
                      ->getDisplayAmount(), PHP_EOL;
                  }
                }
              }

              if ($searchItemsResponse->getErrors() != null) {
                echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
                echo 'Error code: ', $searchItemsResponse->getErrors()[0]->getCode(), PHP_EOL;
                echo 'Error message: ', $searchItemsResponse->getErrors()[0]->getMessage(), PHP_EOL;
              }
            } catch (ApiException $exception) {
              echo 'Error calling PA-API 5.0!', PHP_EOL;
              echo 'HTTP Status Code: ', $exception->getCode(), PHP_EOL;
              echo 'Error Message: ', $exception->getMessage(), PHP_EOL;

              if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
                $errors = $exception->getResponseObject()->getErrors();

                foreach ($errors as $error) {
                  echo 'Error Type: ', $error->getCode(), PHP_EOL;
                  echo 'Error Message: ', $error->getMessage(), PHP_EOL;
                }
              } else {
                echo 'Error response body: ', $exception->getResponseBody(), PHP_EOL;
              }
            } catch (Exception $exception) {
              echo 'Error Message: ', $exception->getMessage(), PHP_EOL;
            }



















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

           ];


          }
        }
















        $this->hooks->call('ImportExportCategories', 'Save');

        Cache::clear('categories');
        Cache::clear('products-also_purchased');
        Cache::clear('products_related');
        Cache::clear('products_cross_sell');
        Cache::clear('upcoming');

        $this->app->redirect('ImportExport&message=success_product');
      }
    }
  }