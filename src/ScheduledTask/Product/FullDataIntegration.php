<?php
namespace Boxalino\RealTimeUserExperienceIntegration\ScheduledTask\Product;

use Boxalino\DataIntegration\ScheduledTask\Product\DiFullScheduledTask;
use Boxalino\RealTimeUserExperienceIntegration\ScheduledTask\Product\Task\DiFullTask;

/**
 * Class FullDataIntegration
 * Set the interval to trigger the full export (recommended - 1 day)
 *
 * @package Boxalino\RealTimeUserExperience\ScheduledTask
 */
class FullDataIntegration extends DiFullScheduledTask
{

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        return [DiFullTask::class];
    }

}
