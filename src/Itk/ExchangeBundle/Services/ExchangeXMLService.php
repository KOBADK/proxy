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
class ExchangeXMLService
{
    private $rcFile;
    private $dssFile;

    /**
     * Constructor
     *
     * @param string $rcFile
     *   Path to the RC xml file.
     * @param string $dssFile
     *   Path to the DSS xml file.
     */
    public function __construct($rcFile, $dssFile)
    {
        $this->rcFile = $rcFile;
        $this->dssFile = $dssFile;
    }

    /**
     * dateStringToUnixTimestamp
     *
     * Change the date-string from "m-d-Y H:i:s" to a Unix timestamp.
     *
     * @param $dateString
     *   The date string to convert to unix timestamp.
     *   Formatted as "m-d-Y H:i:s"
     *
     * @return string
     *   Unix timestamp
     */
    private function dateStringToUnixTimestamp($dateString)
    {
        return \DateTime::createFromFormat(
            'm-d-Y H:i:s',
            $dateString,
            new \DateTimeZone('Europe/Copenhagen')
        )->format('U');
    }

    /**
     * Import a XML file into the Redis
     *
     * This methods assumes a XML document formed as:
     *   <Events>
     *     <Event>
     *      <Eventname>{{ Name of event }}</Eventname>
     *      <Templatename>{{ Room id }}</Templatename>
     *      <Starttime>{{ Date formatted as "m-d-Y H:i:s" in Europe/Copenhagen timezone }}</Starttime>
     *      <Endtime>{{ Date formatted as "m-d-Y H:i:s" in Europe/Copenhagen timezone }}</Endtime>
     *     </Event>
     *     <Event>
     *     ...
     *   </Events>
     *
     * To improve performance, the XMLReader is used, instead of parsing the whole XML file.
     * Each Event node is then imported with simplexml to enable easy parsing.
     *
     * @return array
     *   The imported data.
     */
    public function importRcXmlFile()
    {
        // The array of imported data.
        $data = array();

        // Initialize the XMLReader and load the file.
        $z = new \XMLReader;
        $z->open($this->rcFile);

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
            $node = simplexml_import_dom($doc->importNode($z->expand(), true));

            // Setup data for node.
            $arr = array(
                'event_id' => '',
                'event_name' => trim($node->Eventname->__toString()),
                'resource_id' => trim($node->Templatename),
                'start_time' => $this->dateStringToUnixTimestamp(
                    trim($node->Starttime)
                ),
                'end_time' => $this->dateStringToUnixTimestamp(
                    trim($node->Endtime)
                ),
                'start_time_readable' => "".$node->Starttime,
                'end_time_readable' => "".$node->Endtime,
            );

            // Initialize array index for room-id if it does not exist.
            if (!isset($data[$arr['resource_id']])) {
                $data[$arr['resource_id']] = array();
            }
            // Save the event under the correct room in the return array.
            $data[$arr['resource_id']][] = $arr;

            // Go to next Event.
            $z->next('Event');
        }

        return $data;
    }

    /**
     * Import a XML file into the Redis
     *
     * This methods assumes a XML document formed as:
     *   <Events>
     *     <Event>
     *      <EventID>{{ Event id }}</EventID>
     *      <Eventname>{{ Name of event }}</Eventname>
     *      <Roomname>{{ Room id }}</Roomname>
     *      <Starttime>{{ Date formatted as "m-d-Y H:i:s" in Europe/Copenhagen timezone }}</Starttime>
     *      <Endtime>{{ Date formatted as "m-d-Y H:i:s" in Europe/Copenhagen timezone }}</Endtime>
     *     </Event>
     *     <Event>
     *     ...
     *   </Events>
     *
     * To improve performance, the XMLReader is used, instead of parsing the whole XML file.
     * Each Event node is then imported with simplexml to enable easy parsing.
     *
     * @return array
     *   The imported data.
     */
    public function importDssXmlFile()
    {
        // The array of imported data.
        $data = array();

        // Initialize the XMLReader and load the file.
        $z = new \XMLReader;
        $z->open($this->dssFile);

        // This is used for simplexml parsing.
        $doc = new \DOMDocument;

        // Move to the first Event node.
        // This is to ignore nodes that are not Event nodes.
        while ($z->read() && $z->name !== 'Event') {
            continue;
        }

        $now = time();

        // Loop over all Event nodes.
        while ($z->name === 'Event') {
            // Get node as simplexml.
            $node = simplexml_import_dom($doc->importNode($z->expand(), true));

            $startTime = $this->dateStringToUnixTimestamp(
                trim($node->Starttime)
            );
            $endTime = $this->dateStringToUnixTimestamp(trim($node->Endtime));

            if ($endTime < $now) {
                // Go to next Event.
                $z->next('Event');
                continue;
            }

            // Setup data for node.
            $arr = array(
                'event_id' => trim($node->EventID),
                'event_name' => trim($node->Eventname->__toString()),
                'resource_id' => trim($node->Roomname->__toString()),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'start_time_readable' => "".$node->Starttime,
                'end_time_readble' => "".$node->Endtime,
            );

            // Initialize array index for room-id if it does not exist.
            if (!isset($data[$arr['resource_id']])) {
                $data[$arr['resource_id']] = array();
            }
            // Save the event under the correct room in the return array.
            $data[$arr['resource_id']][] = $arr;

            // Go to next Event.
            $z->next('Event');
        }

        return $data;
    }
}
