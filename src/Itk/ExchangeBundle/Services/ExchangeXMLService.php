<?php
/**
 * @file
 * Contains the ExchangeXMLService.
 *
 * Helper functions to import XML event documents.
 */

namespace Itk\ExchangeBundle\Services;

/**
 * Class ExchangeXMLService
 *
 * @package Itk\ExchangeBundle\Services
 */
class ExchangeXMLService {
  private $file;


  /**
   * @TODO
   */
  public function __construct($file) {
    $this->file = $file;
  }

  /**
   * dateStringToUnixTimestamp
   *
   * Change the date-string from "d-m-Y H:i:s" to a Unix timestamp.
   *
   * @param $dateString
   *   The date string to convert to unix timestamp.
   *   Formatted as "d-m-Y H:i:s"
   *
   * @return string
   *   Unix timestamp
   */
  private function dateStringToUnixTimestamp($dateString) {
    return \DateTime::createFromFormat('d-m-Y H:i:s', $dateString, new \DateTimeZone('Europe/Copenhagen'))->format('U');
  }

  /**
   * Import a XML file into the Redis
   *
   * This methods assumes a XML document formed as:
   *   <Events>
   *     <Event>
   *      <Eventname>{{ Name of event }}</Eventname>
   *      <Templatename>{{ Room id }}</Templatename>
   *      <Starttime>{{ Date formatted as "d-m-Y H:i:s" in Europe/Copenhagen timezone }}</Starttime>
   *      <Endtime>{{ Date formatted as "d-m-Y H:i:s" in Europe/Copenhagen timezone }}</Endtime>
   *     </Event>
   *     <Event>
   *     ...
   *   </Events>
   *
   * To improve performance, the XMLReader is used, instead of parsing the whole XML file.
   * Each Event node is then imported with simplexml to enable easy parsing.
   *
   * @param String $file
   *   Path to the file to read.
   *
   * @return array
   *   The imported data.
   */
  public function importXmlFile() {
    // The array of imported data.
    $data = array();

    // Initialize the XMLReader and load the file.
    $z = new \XMLReader;
    $z->open($this->file);

    // This is used for simplexml parsing.
    $doc = new \DOMDocument;

    // Move to the first Event node.
    // This is to ignore nodes that are not Event nodes.
    while ($z->read() && $z->name !== 'Event') {
      continue;
    }

    // Loop over all Event nodes.
    while ($z->name === 'Event') {
      // Get node as simplexml.
      $node = simplexml_import_dom($doc->importNode($z->expand(), TRUE));

      // Setup data for node.
      $arr = array(
        'event_name' => trim($node->Eventname->__toString()),
        'room_id' => trim($node->Templatename),
        'start_time' => $this->dateStringToUnixTimestamp(trim($node->Starttime)),
        'end_time' => $this->dateStringToUnixTimestamp(trim($node->Endtime)),
      );

      // Initialize array index for room-id if it does not exist.
      if (!isset($data[$arr['room_id']])) {
        $data[$arr['room_id']] = array();
      }
      // Save the event under the correct room in the return array.
      $data[$arr['room_id']][] = $arr;

      // Go to next Event.
      $z->next('Event');
    }

    return $data;
  }
}
