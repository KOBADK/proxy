# KOBA - Kalender og Booking API
This project is based on Symfony Rest Edition.

## Cron setup
To continually update calendar information from the xml files, add a cron job.
```shell
$ crontab -e
```

Add the following line:
```shell
*/1 * * * * path_to_php/php path_to_backend/app/console koba:calendar:update
```

## Initial Installation
```shell
$ composer install
$ php bin/console doctrine:migrations:migrate
$ bin/console fos:user:create --super-admin
```

## Setup JMS/JobQueueBundle
To process booking requests, the jms/job-queue-bundle is used. This requires supervisord.
To install see: http://jmsyst.com/bundles/JMSJobQueueBundle/master/installation

## Symfony Rest Edition
For the readme for the Symfony REST edition see README-Symfony-rest-edition.md.

The API accepts/returns json by default, but can also handle xml if the following url-parameter is set on requests:
```shell
?_format=xml
```

## Scrutinizer
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KOBADK/backend/badges/quality-score.png?b=development)](https://scrutinizer-ci.com/g/KOBADK/backend/?branch=development)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KOBADK/backend/badges/build.png?b=development)](https://scrutinizer-ci.com/g/KOBADK/backend/?branch=development)


