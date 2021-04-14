<?php
namespace Boxalino\RealTimeUserExperienceIntegration\ScheduledTask\Product\Task;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

/**
 * Class FullDataIntegration
 * Set the interval to trigger the full export (recommended - 1 day)
 *
 * @package Boxalino\RealTimeUserExperience\ScheduledTask
 */
class DiFullTask extends ScheduledTask
{

    public static function getTaskName(): string
    {
        return 'boxalino.di.full.product';
    }

    /**
     * The full data synchronization is triggered once per day
     *
     * @return int
     */
    public static function getDefaultInterval(): int
    {
        return 86400; // 1day
    }

}
