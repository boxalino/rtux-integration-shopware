<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context;

use Boxalino\RealTimeUserExperience\Framework\Request\ItemContextAbstract;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * PDP recommendation request
 *
 * PDP recommendation context requires a product id as an item context
 * set on the api request
 *
 * @package Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context
 */
class CrossSelling extends ItemContextAbstract
{
    /**
     * @return int
     */
    public function getContextVisibility() : int
    {
        return ProductVisibilityDefinition::VISIBILITY_SEARCH;
    }

    /**
     * @param Request $request
     * @param SalesChannelContext $salesChannelContext
     * @return string
     */
    public function getContextNavigationId(Request $request, SalesChannelContext $salesChannelContext): array
    {
        return [$salesChannelContext->getSalesChannel()->getNavigationCategoryId()];
    }

    /**
     * @return array
     */
    public function getReturnFields() : array
    {
        return ["products_group_id"];
    }

}
