<?php
namespace Boxalino\RealTimeUserExperienceIntegration\ScheduledTask\Product;

use Boxalino\DataIntegration\ScheduledTask\Product\DiDeltaScheduledTask;
use Boxalino\RealTimeUserExperienceIntegration\ScheduledTask\Product\Task\DiDeltaTask;

/**
 * Class DeltaDataIntegration
 * Set the interval to trigger the delta export (recommended - 1h)
 *
 * @package Boxalino\RealTimeUserExperience\ScheduledTask
 */
class DeltaDataIntegration extends DiDeltaScheduledTask
{

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        return [DiDeltaTask::class];
    }


}
