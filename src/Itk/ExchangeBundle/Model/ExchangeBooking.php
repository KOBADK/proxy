<?php
/**
 * Created by PhpStorm.
 * User: cableman
 * Date: 16/04/15
 * Time: 11:20
 */

namespace Itk\ExchangeBundle\Model;


class ExchangeBooking {

  private $id;
  private $changeKey;
  private $subject;
  private $start;
  private $end;
  private $body;

  function __construct($id, $changeKey, $subject = '', $start = 0, $end = 0, $body = NULL) {
    $this->id = $id;
    $this->changeKey = $changeKey;
    $this->subject = $subject;
    $this->start = $start;
    $this->end = $end;
    $this->body = $body;
  }

  /**
   * @return null
   */
  public function getBody() {
    return $this->body;
  }

  /**
   * @param null $body
   */
  public function setBody($body) {
    $this->body = $body;
  }

  /**
   * @return mixed
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param mixed $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return mixed
   */
  public function getChangeKey() {
    return $this->changeKey;
  }

  /**
   * @param mixed $changeKey
   */
  public function setChangeKey($changeKey) {
    $this->changeKey = $changeKey;
  }

  /**
   * @return mixed
   */
  public function getSubject() {
    return $this->subject;
  }

  /**
   * @param mixed $subject
   */
  public function setSubject($subject) {
    $this->subject = $subject;
  }

  /**
   * @return mixed
   */
  public function getStart() {
    return $this->start;
  }

  /**
   * @param mixed $start
   */
  public function setStart($start) {
    $this->start = $start;
  }

  /**
   * @return mixed
   */
  public function getEnd() {
    return $this->end;
  }

  /**
   * @param mixed $end
   */
  public function setEnd($end) {
    $this->end = $end;
  }
}
