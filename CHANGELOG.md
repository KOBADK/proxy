#KOBA CHANGELOG

#v1.1.5

* Added readable times fields for dss and rc, to enable easier debugging.
* Added cache expire to dss and rc entries.

#v1.1.4

* Added "?_format=json" to callback calls, to fix issue with calls not being handled correctly with Drupal 8 sites.

#v1.1.3

* Fixed how bookings are confirmed.

#v1.1.2

* Added base64 encoding/decoding of serialized data in mail body

#v1.1.1

* Fixed regular expression to allow newlines in body (cf. http://php.net/manual/en/reference.pcre.pattern.modifiers.php, PCRE_DOTALL)

#1.1.0

* Added Alias to resources.
* Removed unimplemented option SAFE_TITLE.
* Increase waits between retrying jobs. Changed to exponential strategy.
* Differentiate between double bookings and not created bookings.

#v1.0.0

First release
