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

  namespace ClicShopping\Apps\Catalog\ImportExport\Classes\ClicShoppingAdmin\Amazon;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
  use Amazon\ProductAdvertisingAPI\v1\Configuration;
  use Amazon\ProductAdvertisingAPI\v1\ApiException;
  use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
  use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
  use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
  use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;

    class Amazon
  {
    private $db;
    private string $key;
    private string $secretKey;
    private string $region;
    private string $host;
    private string $partnerTag;

      public function __construct()
    {
      $this->db = Registry::get('Db');

      $this->key = HTML::sanitize($_POST['access_key']);
      $this->secretKey = HTML::sanitize($_POST['secret_key']);
      $this->region  = HTML::sanitize($_POST['region']);
      $this->host  = HTML::sanitize($_POST['host']);
      $this->partnerTag = HTML::sanitize($_POST['partner_tag']);
    }

    public function getConfig()
    {
      $config = new Configuration();

      $config->setAccessKey($this->key);
      $config->setSecretKey($this->secretKey);

      # Please add your partner tag (store/tracking id) here
      $partnerTag = $this->partnerTag;

/*
* PAAPI host and region to which you want to send request
* For more details refer:
* https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
*/
      $config->setHost($this->host);
      $config->setRegion($this->region);

      $apiInstance = new DefaultApi(
      /*
       * If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
       * This is optional, `GuzzleHttp\Client` will be used as default.
       */
        new GuzzleHttp\Client(),
        $config
      );
    }
  }