# KOBA - Kalender og Booking API
This project is based on Symfony Rest Edition.

## Initial Installation
<pre>
 $ composer install
 $ php app/console doctrine:database:create
 $ php app/console doctrine:schema:update --force
</pre>

## Symfony Rest Edition
For the readme for the Symfony REST edition see README-Symfony-rest-edition.md.

The API can be tested from the documentation that is generated. Visit:
<pre>
  [server_address]/doc
</pre>

The API accepts/returns json by default, but can also handle xml if the following url-parameter is set on requests:
<pre>
?_format=xml
</pre>

## Tunnel (only for testing!)
Append following line to vendor/jameslarmes/PhpEws/NTLMSoapClient.php after line 84:
<pre>
curl_setopt($this->ch, CURLOPT_PROXY, "http://127.0.0.1:8080/");
curl_setopt($this->ch, CURLOPT_PROXYTYPE, 7);
</pre>

Run the following within the vagrant:
<pre>
ssh -D 8080 -f -C -q -N deploy@namor.aakb.dk
</pre>

## Scrutinizer
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KOBADK/backend/badges/quality-score.png?b=development)](https://scrutinizer-ci.com/g/KOBADK/backend/?branch=development)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KOBADK/backend/badges/build.png?b=development)](https://scrutinizer-ci.com/g/KOBADK/backend/?branch=development)



## APIs

### /api

All /api calls should have the get parameter ApiKey defined or will get access denied.

#### GET /api/resources/{groupID = default}
Gets all resources for groupID and ApiKey.

#### GET /api/bookings
Gets all bookings made with the given ApiKey.

#### POST /api/bookings
Create a new booking.

Request body:
<pre>
{
  id (string): *,
  starttime (unix timestamp): *,
  endtime (unix timestamp): *,
  description (string): *,
  summary (string): *,
  name (string): *,
  mail (string): *,
  tlf (string): *
}
</pre>

Response:
?
