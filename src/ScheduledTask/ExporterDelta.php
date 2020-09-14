<?php
namespace Boxalino\RealTimeUserExperienceIntegration\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

/**
 * Class ExportDelta
 * Set the interval to trigger the delta export (recommended - 1h)
 *
 * @package Boxalino\RealTimeUserExperience\ScheduledTask
 */
class ExporterDelta extends ScheduledTask
{

    public static function getTaskName(): string
    {
        return 'boxalino.export.delta';
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
