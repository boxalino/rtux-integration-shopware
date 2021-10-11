<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Core\Framework\Adapter\Cache;

use Boxalino\RealTimeUserExperience\Core\Framework\Adapter\Cache\NavigationCacheStateSubscriber;
use Shopware\Core\PlatformRequest;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Subscriber with logic on which pages/contexts the API cache tag must be added
 * Further configuration required in shopware.cache.invalidation.category_route to avoid caching of the pages
 */
class ApiNavigationCacheStateSubscriber extends NavigationCacheStateSubscriber
{
    /**
     * @param ControllerEvent $event
     * @return bool
     */
    public function checkIfAddState(ControllerEvent $event) : bool
    {
        $request = $event->getRequest();

        if($request->attributes->has(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT))
        {
            if($request->attributes->has("navigationId")
                || $request->attributes->get("_route") === 'frontend.home.page')
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCacheState() : string
    {
        return NavigationCacheStateSubscriber::STATE_BOXALINO_API;
    }


}
