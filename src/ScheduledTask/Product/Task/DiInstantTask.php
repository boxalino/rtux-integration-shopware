<?php
namespace Boxalino\RealTimeUserExperienceIntegration\ScheduledTask\Product\Task;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

/**
 * Class DiInstantTask
 *
 * @package Boxalino\RealTimeUserExperience\ScheduledTask
 */
class DiInstantTask extends ScheduledTask
{

    public static function getTaskName(): string
    {
        return 'boxalino.di.instant.product';
    }

    /**
     * The instant data synchronization is triggered as configured
     *
     * @return int
     */
    public static function getDefaultInterval(): int
    {
        return 300; // 5min
    }

}
