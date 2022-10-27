# KOBA - Kalender og Booking API
This project is based on Symfony Rest Edition.

## Cron setup
To continually update calendar information from the xml files, add a cron job.
<pre>
$ crontab -e
</pre>

Add the following line:
<pre>
*/1 * * * * path_to_php/php path_to_backend/bin/console koba:calendar:update
</pre>

## Initial Installation
<pre>
 $ composer install
 $ php app/console doctrine:migrations:migrate
</pre>

## Setup JMS/JobQueueBundle
To process booking requests, the jms/job-queue-bundle is used. This requires supervisord.
To install see: http://jmsyst.com/bundles/JMSJobQueueBundle/master/installation

## Symfony Rest Edition
For the readme for the Symfony REST edition see README-Symfony-rest-edition.md.

The API accepts/returns json by default, but can also handle xml if the following url-parameter is set on requests:
<pre>
?_format=xml
</pre>

## Scrutinizer
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KOBADK/backend/badges/quality-score.png?b=development)](https://scrutinizer-ci.com/g/KOBADK/backend/?branch=development)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KOBADK/backend/badges/build.png?b=development)](https://scrutinizer-ci.com/g/KOBADK/backend/?branch=development)


