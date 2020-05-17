<?php
namespace  Boxalino\RealTimeUserExperienceIntegration\ScheduledTask;

use Boxalino\RealTimeUserExperience\ScheduledTask\ExporterFullHandlerAbstract;

/**
 * Class ExportFullHandler
 * @package Boxalino\RealTimeUserExperienceIntegration\ScheduledTask
 */
class ExporterFullHandler extends ExporterFullHandlerAbstract
{

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        return [ExporterFull::class];
    }

}
