<?php

  namespace ClicShopping\Apps\Catalog\ImportExport\Classes\ClicShoppingAdmin\Amazon;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;

  use DiDom\Document;
  use DiDom\Query;
  use DiDom\Errors;
  /**
   *
   * @author      Junaid
   * @category    Admin
   * @package     amazon-crawler/includes
   * @version     1.0
   */
  class Crawler
  {
    /**
     * create curl handle.
     */
    function __construct()
    {
      $this->document = new Document();
      $this->query = new Query();
      $this->errors = new Errors();
    }

    /*
    *  Return rl content
    * @$url : $url, website url
    * @params : $argument_title : paramters about title search
    * @return : $html, result of the contentparse_str
    * private
    */
    private function getHTML(string $url)
    {

      $html = HTTP::getResponse([
        'url' => $url,
      ]);

      return $html;
    }

    /**
     * simply return page content when loged in
     * or where login is not required.
     */
    public function getContent(string $url)
    {
      $html = $this->getHTML($url);

      if ($html === false) {
        return false;
      }

      $element = $this->document->loadHtml($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

      return $element;
    }
  }// class