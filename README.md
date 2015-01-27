#KOBA - Kalender og Booking API

##Symfony Rest Edition
For the readme for the Symfony REST edition see README-Symfony-rest-edition.md.

The API can be tested from the documentation that is generated. Visit:
<pre>
  [server_address]/doc
</pre>

The API accepts/returns json by default, but can also handle xml if the following url-parameter is set on requests:
<pre>
?_format=xml
</pre>

##Testing
To run symfony tests:
<pre>
$ php bin/phpunit -c app
</pre>

Documentation for testing in Symfony see:
<pre>
http://symfony.com/doc/current/book/testing.html
</pre>

## Tunnel (only for testing!)
Append following line to vendor/jameslarmes/PhpEws/NTLMSoapClient.php after line 84:
<pre>
curl_setopt($this->ch, CURLOPT_PROXY, 'socks5://localhost:8080');
</pre>

Run the following within the vagrant:
<pre>
ssh -D 8080 -f -C -q -N deploy@namor.aakb.dk
</pre>
