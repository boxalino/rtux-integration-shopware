<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context;

use Boxalino\RealTimeUserExperience\Framework\Request\ListingContextAbstract;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;
use Boxalino\RealTimeUserExperienceIntegration\Framework\Request\IntegrationContextTrait;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * Dynamic Navigation request
 *
 * @package Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context
 */
class Dynamic extends ListingContextAbstract
{
    use IntegrationContextTrait;

    /**
     * @return int
     */
    public function getContextVisibility() : array
    {
        return [ProductVisibilityDefinition::VISIBILITY_ALL];
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
     * Set the return fields desired for navigation
     *
     * @return array
     */
    public function getReturnFields() : array
    {
        return ["id", "products_group_id","discountedPrice", "link", "title", "image"];
    }

}
