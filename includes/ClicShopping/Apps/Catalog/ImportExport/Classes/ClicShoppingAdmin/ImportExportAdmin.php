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
  namespace ClicShopping\Apps\Catalog\ImportExport\Classes\ClicShoppingAdmin;

  class ImportExportAdmin
  {
    /**
     * @param array $array
     * @param string $delimiter
     * @param  $enclosure
     * @param  $escape
     * @param  $charset
     * @param string $eol
     * @return string|string[]|null
     */

    public static function csvEncode(array $array, string $delimiter = ',', $enclosure = '"', $escape = '"', $charset = 'utf-8', string $eol = "\r\n") {
      $output = '';

      // Collect columns
      $columns = [];

      foreach ($array as $row) {
        foreach (array_keys($row) as $column) {
          if (!\in_array($column, $columns)) $columns[] = $column;
        }
      }

      // Collect rows and order by column order
      foreach (array_keys($array) as $row) {
        $line = [];
        foreach ($columns as $column) {
          $line[$column] = isset($array[$row][$column]) ? $array[$row][$column] : '';
        }
        $array[$row] = $line;
      }

      // Prepend column header
      array_unshift($array, array_combine($columns, $columns));

      // Build output
      foreach ($array as $row) {
        foreach (array_keys($row) as $column) {
          if (strpbrk($row[$column], $delimiter . $enclosure . $escape."\r\n") !== false) {
            $row[$column] = $enclosure . str_replace($enclosure, $escape . $enclosure, $row[$column]) . $enclosure;
          }
        }

        $output .= implode($delimiter, $row) . $eol; // Don't use fputcsv as EOL and escape char can not be customized
      }

      return preg_replace('#(\r\n|\r|\n)#', $eol, $output);
    }

    /**
     * @param string $string
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param string $charset
     * @return array|bool
     */
    public static function csvDecode(string $string, string $delimiter = '', string $enclosure = '"', $escape = '"', $charset = 'utf-8') {

      $output = [];

      // Override line endings
      $ini_eol = ini_get('auto_detect_line_endings');
      ini_set('auto_detect_line_endings', true);

      $string = trim($string, "\r\n");

 // Auto-detect delimiter
      if (empty($delimiter)) {
        preg_match('#^.*$#m', $string, $matches);

        $array_delimitr = [
          ';',
          ',',
          "\t",
          '|',
          chr(124)
        ];

        foreach ($array_delimitr as $char) {
          if (str_contains($matches[0], $char)) {
            $delimiter = $char;
            break;
          }
        }

        if (empty($delimiter)) trigger_error('Unable to determine CSV delimiter', E_USER_ERROR);
      }

      // Decode CSV using temporary buffer for file handle
      $fp = fopen('php://temp', 'r+');
      fputs($fp, $string);
      rewind($fp);

      $line = 0;

      while ($row = fgetcsv($fp, 0, $delimiter, $enclosure, $escape)) {
        $line++;

        if (empty($headers)) {
          $headers = $row;
          continue;
        }

        if (\count($headers) != \count($row)) {
          trigger_error('Inconsistent amount of columns on line '. $line .' (Expected '. \count($headers) .' columns - Found '. \count($row) .')', E_USER_WARNING);
          return false;
        }

        $output[] = array_combine($headers, $row);
      }

      fclose($fp);

      ini_set('auto_detect_line_endings', $ini_eol);

      return $output;
    }

    /**
     * @return array
     */
    public static function delimiter($option = true): array
    {

      if ($option === true) {
        $array_delimiter = [
          ['id' => '', 'text' => 'Auto (Default)'],
          ['id' => ';', 'text' => ';'],
          ['id' => ',', 'text' => ','],
          ['id' => ' ', 'text' => 'tab'],
          ['id' => '|', 'text' => '|'],
        ];
      } else {
        $array_delimiter = [
          ['id' => ';', 'text' => ';'],
          ['id' => ',', 'text' => ','],
          ['id' => ' ', 'text' => 'tab'],
          ['id' => '|', 'text' => '|'],
        ];
      }

      return $array_delimiter;
    }

    /**
     * @return array
     */
    public static function enclosure(): array
    {
      $array_enclosure = array(
        ['id' => '"', 'text' => '" (default)'],
      );

      return $array_enclosure;
    }

    /**
     * @return array
     */
    public static function escape(): array
    {
      $array_escape = array(
        ['id' => '"', 'text' => '" (default)'],
        ['id' => '\\', 'text' => '\\'],
      );

      return $array_escape;
    }

    /**
     * @return array
     */
    public static function lineEnding(): array
    {
      $array_line_ending = array(
        ['id' => 'Win', 'text' => 'Win'],
        ['id' => 'Mac', 'text' => 'Mac'],
        ['id' => 'Linux', 'text' => 'Linux'],
      );

      return $array_line_ending;
    }

    /**
     * @return array
     */
    public static function output(): array
    {
      $array_line_ending = array(
        ['id' => 'file', 'text' => 'File'],
        ['id' => 'screen', 'text' => 'Screen'],
      );

      return $array_line_ending;
    }
  }