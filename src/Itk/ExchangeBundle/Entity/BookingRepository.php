<?php
/**
 * @file
 * Contains the booking repository.
 */

namespace Itk\ExchangeBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class BookingRepository
 * @package Itk\ExchangeBundle\Entity
 */
class BookingRepository extends EntityRepository {
  CONST DATAFORMAT = 'Ymd\THis\Z';

  /**
   * Get start datetime formatted for a vCard.
   *
   * Complete date plus hours, minutes and seconds: YYYYMMDDThhmmssTZD (eg 19970716T192030+0100)
   *   T = Separator between date and time
   *   TZD  = time zone designator (Z or +hh:mm or -hh:mm)
   *   See http://www.w3.org/TR/NOTE-datetime where the separators have been removed
   */
  public function getStartDatetimeForVcard() {
    return date($this->startTime, self::DATAFORMAT);
  }

  /**
   * Get end datetime formatted for a vCard.
   *
   * Complete date plus hours, minutes and seconds: YYYYMMDDThhmmssTZD (eg 19970716T192030+0100)
   *   T = Separator between date and time
   *   TZD  = time zone designator (Z or +hh:mm or -hh:mm)
   *   See http://www.w3.org/TR/NOTE-datetime where the separators have been removed
   */
  public function getEndDatetimeForVcard() {
    return date($this->endTime, self::DATAFORMAT);
  }
}
