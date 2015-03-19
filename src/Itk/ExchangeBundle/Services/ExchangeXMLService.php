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
    return \DateTime::createFromFormat('d-m-Y H:i:s', $s, new \DateTimeZone('Europe/Copenhagen'));
  }

  public function parseXmlFile() {
    $data = array();

    $z = new \XMLReader;
    $z->open('test.xml');

    $doc = new \DOMDocument;

    // Move to the first Event node.
    while ($z->read() && $z->name !== 'Event') {
      continue;
    }

    // Loop over all Event nodes.
    while ($z->name === 'Event') {
      // Get node as simplexml.
      $node = simplexml_import_dom($doc->importNode($z->expand(), TRUE));

      // Setup data for node.
      $arr = array(
        'event-name' => trim($node->Eventname->__toString()),
        'room-id' => trim($node->Templatename),
        'start-time' => $this->createUnixTimestamp(trim($node->Starttime)),
        'start-time-from-file' => trim($node->Starttime),
        'end-time' => $this->createUnixTimestamp(trim($node->Endtime)),
        'end-time-from-file' => trim($node->Endtime)
      );

      // Save the event under the correct room.
      if (!isset($data[$arr['room-id']])) {
        $data[$arr['room-id']] = array();
      }
      $data[$arr['room-id']][] = $arr;

      // Go to next Event.
      $z->next('Event');
    }

    return $data;
  }
}
