<?php

namespace App\Service;

use Mix\Redis\Subscribe\Subscriber;

trait SubscriberTrait
{
    /**
     * @var Subscriber
     */
    protected Subscriber $subscriber;

    public function setSubscriber(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    public function getSubscriber(): Subscriber
    {
        return $this->subscriber;
    }
}