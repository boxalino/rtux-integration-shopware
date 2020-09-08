<?php
namespace Boxalino\RealTimeUserExperienceIntegration\ScheduledTask;

use Boxalino\Exporter\ScheduledTask\ExporterFullHandlerAbstract;

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
