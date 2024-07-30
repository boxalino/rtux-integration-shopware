<?php
namespace Boxalino\RealTimeUserExperienceIntegration\ScheduledTask\Product;

use Boxalino\DataIntegration\ScheduledTask\Product\DiInstantScheduledTask;
use Boxalino\RealTimeUserExperienceIntegration\ScheduledTask\Product\Task\DiInstantTask;

/**
 * Class InstantDataIntegration
 *
 * @package Boxalino\RealTimeUserExperience\ScheduledTask
 */
class InstantDataIntegration extends DiInstantScheduledTask
{

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        return [DiInstantTask::class];
    }


}
