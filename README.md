# KOBA - Kalender og Booking API
This project is based on Symfony Rest Edition.

## Cron setup
To continually update calendar information from the xml files, add a cron job.
<pre>
$ crontab -e
</pre>

Add the following line:
<pre>
*/1 * * * * path_to_php/php path_to_backend/app/console koba:calendar:update
</pre>

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


