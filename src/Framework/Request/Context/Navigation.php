<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context;

use Boxalino\RealTimeUserExperience\Framework\Request\CmsContextAbstract;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;
use Boxalino\RealTimeUserExperienceIntegration\Framework\Request\IntegrationContextTrait;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;

/**
 * Navigation request
 *
 * A listing context can render a default Category view layout:
 * facets, products, sorting, pagination and other narrative elements
 *
 * UPDATE: ADDS THE FILTERABLE PROPERTY GROUPS ON API REQUEST AS WELL
 *
 * @package Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context
 */
class Navigation extends CmsContextAbstract
{
    use IntegrationContextTrait;

    /**
     * @return array
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
        $params = $request->getRequest()->attributes->get('_route_params');
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
        return ["id", "products_group_id", "discountedPrice", "link", "title", "image"];
    }

}
