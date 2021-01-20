<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context;

use Boxalino\RealTimeUserExperience\Framework\Request\ItemContextAbstract;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

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
     * Adds the configured cross-sell products as context parameters
     * Activate if it is a project requirement
     *
     * @return bool
     */
    public function useConfiguredProductsAsContextParameters() : bool
    {
        return true;
    }

    /**
     * @return int
     */
    public function getContextVisibility() : array
    {
        return [ProductVisibilityDefinition::VISIBILITY_SEARCH];
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    public function getContextNavigationId(RequestInterface $request): array
    {
        return [$this->getSalesChannelContext()->getSalesChannel()->getNavigationCategoryId()];
    }

    /**
     * @return array
     */
    public function getReturnFields() : array
    {
        return ["products_group_id"];
    }

}
