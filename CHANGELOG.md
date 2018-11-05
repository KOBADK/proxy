# KOBA CHANGELOG

## 2.0.2

* Removed uncaught exception cluttering the logs.

## 2.0.1

* Updated bundles.

## 2.0.0

* Upgrade to Symfony 3.4 and php 7.2.
* Switch from fork of jms job queue bundle back to master.
* Added Doctrine Migrations.
* Added attendee to booking to fix issue with outlook365 stmp.

# v1.2.0

* Changed how bookings are matched. Allow non-matching IcalUid, if both subject and clientBookingId match.

# v1.1.11

* Fixed ldap search.

# v1.1.10

* Added follow redirect to curl.
* Added impersonation to getItem calls.

## 1.1.9

* Added koba_job_id to callback requests.

## v1.1.8

* Changed the booking mails start/end time to be in UTC (see https://itkdev.atlassian.net/browse/SUPPORT-583)

## v1.1.7

* New mode to mix RC and free/busy bookings.

## v1.1.6

* Changed how results are filtered when getting booking events.

## v1.1.5

* Added readable times fields for dss and rc, to enable easier debugging.
* Added cache expire to dss and rc entries.

## v1.1.4

* Added "?_format=json" to callback calls, to fix issue with calls not being handled correctly with Drupal 8 sites.

## v1.1.3

* Fixed how bookings are confirmed.

## v1.1.2

* Added base64 encoding/decoding of serialized data in mail body

## v1.1.1

* Fixed regular expression to allow newlines in body (cf. http://php.net/manual/en/reference.pcre.pattern.modifiers.php, PCRE_DOTALL)

## 1.1.0

* Added Alias to resources.
* Removed unimplemented option SAFE_TITLE.
* Increase waits between retrying jobs. Changed to exponential strategy.
* Differentiate between double bookings and not created bookings.

## v1.0.0

* First release
