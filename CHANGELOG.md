#KOBA changelog

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
