<?php
/**
 * @file
 * Contains the ExchangeXMLService.
 */

namespace Itk\ExchangeBundle\Services;

/**
 * Class ExchangeXMLService
 *
 * @package Itk\ExchangeBundle\Services
 */
class ExchangeXMLService {
  private function createUnixTimestamp($s) {
    return \DateTime::createFromFormat("d-m-Y H:i:s", $s)->format('U');
  }

  public function parseXMLFile() {
    $data = array();

    $z = new \XMLReader;
    $z->open('test.xml');

    $doc = new \DOMDocument;

    // move to the first <product /> node
    while ($z->read() && $z->name !== 'Event') {}

    // now that we're at the right depth, hop to the next <product/> until the end of the tree
    while ($z->name === 'Event') {
      // either one should work
      //$node = new \SimpleXMLElement($z->readOuterXML());
      $node = simplexml_import_dom($doc->importNode($z->expand(), TRUE));

      $arr = array(
        "event-name" => trim($node->Eventname->__toString()),
        "room-id" => trim($node->Templatename),
        "start-time" => $this->createUnixTimestamp(trim($node->Starttime)),
        "end-time" => $this->createUnixTimestamp(trim($node->Endtime))
      );

      if (!isset($data[$arr["room-id"]])) {
        $data[$arr["room-id"]] = array();
      }
      $data[$arr["room-id"]][] = $arr;

      // go to next <product />
      $z->next('Event');
    }

    return $data;
  }
}
