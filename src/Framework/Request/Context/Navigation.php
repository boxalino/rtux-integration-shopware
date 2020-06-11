<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context;

use Boxalino\RealTimeUserExperience\Framework\Request\ContextAbstract;
use Boxalino\RealTimeUserExperience\Framework\Request\ListingContextAbstract;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * Navigation request
 *
 * A listing context can render a default Category/Search view layout:
 * facets, products, sorting, pagination and other narrative elements
 *
 * @package Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context
 */
class Navigation extends ListingContextAbstract
{
    /**
     * @return int
     */
    public function getContextVisibility() : array
    {
        return [ProductVisibilityDefinition::VISIBILITY_ALL];
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getContextNavigationId(Request $request): array
    {
        $params = $request->attributes->get('_route_params');
        if ($params && isset($params['navigationId']))
        {
            return [$params['navigationId']];
        }

        return [$this->getSalesChannelContext()->getSalesChannel()->getNavigationCategoryId()];
    }

    /**
     * Set the return fields desired for navigation
     *
     * @return array
     */
    public function getReturnFields() : array
    {
        return ["id", "products_group_id","discountedPrice", "products_seo_url", "title", "products_image"];
    }

    /**
     * Set the range properties following the presented structure
     *
     * @return array
     */
    public function getRangeProperties() : array
    {
        return [
            "products_rating_average" => ['from' => 'products_rating_average', 'to' => 0],
            "discountedPrice" => ['from' => 'min-price', 'to' => 'max-price']
        ];
    }

}
