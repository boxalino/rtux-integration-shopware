<?php
namespace Boxalino\RealTimeUserExperienceIntegration\ScheduledTask\Product\Task;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

/**
 * Class DeltaDataIntegration
 * Set the interval to trigger the delta export (recommended - 1h)
 *
 * @package Boxalino\RealTimeUserExperience\ScheduledTask
 */
class DiDeltaTask extends ScheduledTask
{

    public static function getTaskName(): string
    {
        return 'boxalino.di.delta.product';
    }

    /**
     * The delta data synchronization is triggered as configured, at least every 1h
     *
     * @return int
     */
    public static function getDefaultInterval(): int
    {
        return 3600; // 1h
    }

}
