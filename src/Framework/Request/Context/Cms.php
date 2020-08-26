<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context;

use Boxalino\RealTimeUserExperience\Framework\Request\CmsContextAbstract;
use Boxalino\RealTimeUserExperienceIntegration\Framework\Request\IntegrationContextTrait;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;

/**
 * CMS request
 *
 * A CMS context can render a default Category view layout:
 * facets, products, sorting, pagination and other narrative CMS elements
 *
 * @package Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context
 */
class Cms extends CmsContextAbstract
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
     * Set the return fields desired for navigation
     *
     * @return array
     */
    public function getReturnFields() : array
    {
        return ["id", "products_group_id","discountedPrice", "products_seo_url", "title", "products_image"];
    }

}
