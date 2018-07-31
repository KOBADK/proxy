<?php

namespace Koba\MainBundle\Retry;

use JMS\JobQueueBundle\Entity\Job;
use JMS\JobQueueBundle\Retry\RetryScheduler;

class FixedIntervalRetryScheduler implements RetryScheduler
{
    protected $interval;

    /**
     * FixedIntervalRetryScheduler constructor.
     * @param string $interval
     */
    public function __construct($interval)
    {
        $this->interval = $interval;
    }

    public function scheduleNextRetry(Job $originalJob)
    {
        return new \DateTime($this->interval);
    }
}
