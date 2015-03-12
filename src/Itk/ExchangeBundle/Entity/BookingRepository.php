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

  /**
   * Get start datetime formatted for a vCard.
   *
   * Complete date plus hours, minutes and seconds: YYYYMMDDThhmmssTZD (eg 19970716T192030+0100)
   *   T = Separator between date and time
   *   TZD  = time zone designator (Z or +hh:mm or -hh:mm)
   *   See http://www.w3.org/TR/NOTE-datetime where the separators have been removed
   *
   * @TODO: Use common date format!
   */
  public function getStartDatetimeForVcard() {
    return $this->startDateTime->format('Ymd\THis\Z');
  }

  /**
   * Get end datetime formatted for a vCard.
   *
   * Complete date plus hours, minutes and seconds: YYYYMMDDThhmmssTZD (eg 19970716T192030+0100)
   *   T = Separator between date and time
   *   TZD  = time zone designator (Z or +hh:mm or -hh:mm)
   *   See http://www.w3.org/TR/NOTE-datetime where the separators have been removed
   *
   * @TODO: Use common date format!
   */
  public function getEndDatetimeForVcard() {
    return $this->endDateTime->format('Ymd\THis\Z');
  }
}
