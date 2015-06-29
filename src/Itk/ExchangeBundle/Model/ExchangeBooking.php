<?php
/**
 * Created by PhpStorm.
 * User: cableman
 * Date: 16/04/15
 * Time: 11:20
 */

namespace Itk\ExchangeBundle\Model;


class ExchangeBooking {
  public static $type_koba = 'KOBA';
  public static $type_safe_title = 'SAFE_TITLE';
  public static $type_free_busy = 'FREE_BUSY';

  private $id;
  private $changeKey;
  private $subject;
  private $start;
  private $end;
  private $body;
  private $type;

  public function __construct($id, $changeKey, $subject = '', $start = 0, $end = 0, $body = NULL) {
    $this->id = $id;
    $this->changeKey = $changeKey;
    $this->subject = $subject;
    $this->start = $start;
    $this->end = $end;
    $this->type = self::$type_free_busy;
    $this->body = $body;
  }

  /**
   * @return mixed
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
   * @return string
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @return $this
   */
  public function setTypeKoba() {
    $this->type = self::$type_koba;

    return $this;
  }

  /**
   * @return $this
   */
  public function setTypeSafeTitle() {
    $this->type = self::$type_safe_title;

    return $this;
  }

  /**
   * @return $this
   */
  public function setTypeFreeBusy() {
    $this->type = self::$type_free_busy;

    return $this;
  }
}
