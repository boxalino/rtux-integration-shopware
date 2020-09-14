<?php
namespace Boxalino\RealTimeUserExperienceIntegration\ScheduledTask;

use Boxalino\Exporter\ScheduledTask\ExporterDeltaHandlerAbstract;

/**
 * Class ExporterDeltaHandler
 * @package Boxalino\RealTimeUserExperienceIntegration\ScheduledTask
 */
class ExporterDeltaHandler extends ExporterDeltaHandlerAbstract
{

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        return [ExporterDelta::class];
    }

}
