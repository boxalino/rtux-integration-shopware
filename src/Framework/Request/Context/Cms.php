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
     * @return array
     */
    public function getContextVisibility() : array
    {
        return [ProductVisibilityDefinition::VISIBILITY_ALL];
    }

    /**
     * Set the return fields desired for navigation
     *
     * NOTICE: IN ORDER TO USE THE API RESPONSE FIELDS AS DATA SOURCE - ALL REQUIRED FIELDS MUST BE INCLUDED
     * @return array
     */
    public function getReturnFields() : array
    {
        return [
            "id", "products_group_id", "discountedPrice", "link", "title", "image", "brand",
            "standardPrice", "main_variant_id", "is_closeout", "sku", "ean", "mark_as_topseller",
            "is_new", "rating_average", "body", "variant_count", "configurator_group_config"
        ];
    }

}
