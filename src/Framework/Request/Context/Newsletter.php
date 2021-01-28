<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context;

use Boxalino\RealTimeUserExperience\Framework\Request\ListingContextAbstract;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;
use Boxalino\RealTimeUserExperienceIntegration\Framework\Request\IntegrationContextTrait;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * Dynamic Newsletter request
 *
 * @package Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context
 */
class Newsletter extends ListingContextAbstract
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
     * Set the return fields desired for newsletter recommendation
     *
     * @return array
     */
    public function getReturnFields() : array
    {
        return ["id", "discountedPrice", "products_seo_url", "title", "products_image"];
    }

}
