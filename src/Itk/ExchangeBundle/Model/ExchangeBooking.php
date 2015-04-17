<?php
/**
 * Created by PhpStorm.
 * User: cableman
 * Date: 16/04/15
 * Time: 11:20
 */

namespace Itk\ExchangeBundle\Model;


class ExchangeBooking {
  const BOOKING_TYPE_KOBA = 'KOBA';
  const BOOKING_TYPE_SAFE_TITLE = 'SAFE TITLE';
  const BOOKING_TYPE_FREE_BUSY = 'FREE/BUSY';

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
    $this->type = self::BOOKING_TYPE_FREE_BUSY;
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
   *
   * @return $this
   */
  public function setBody($body) {
    $this->body = $body;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param mixed $id
   *
   * @return $this
   */
  public function setId($id) {
    $this->id = $id;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getChangeKey() {
    return $this->changeKey;
  }

  /**
   * @param mixed $changeKey
   *
   * @return $this
   */
  public function setChangeKey($changeKey) {
    $this->changeKey = $changeKey;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getSubject() {
    return $this->subject;
  }

  /**
   * @param mixed $subject
   *
   * @return $this
   */
  public function setSubject($subject) {
    $this->subject = $subject;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getStart() {
    return $this->start;
  }

  /**
   * @param mixed $start
   *
   * @return $this
   */
  public function setStart($start) {
    $this->start = $start;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getEnd() {
    return $this->end;
  }

  /**
   * @param mixed $end
   *
   * @return $this
   */
  public function setEnd($end) {
    $this->end = $end;

    return $this;
  }

  /**
   * @return $this
   */
  public function setTypeKoba() {
    $this->type = self::BOOKING_TYPE_KOBA;

    return $this;
  }

  /**
   * @return $this
   */
  public function setTypeSafeTitle() {
    $this->type = self::BOOKING_TYPE_SAFE_TITLE;

    return $this;
  }

  /**
   * @return $this
   */
  public function setTypeFreeBusy() {
    $this->type = self::BOOKING_TYPE_FREE_BUSY;

    return $this;
  }
}
