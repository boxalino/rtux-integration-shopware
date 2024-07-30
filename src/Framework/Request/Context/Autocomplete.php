<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Framework\Request\Context;

use Boxalino\RealTimeUserExperience\Framework\Request\AutocompleteContextAbstract;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * Autocomplete request
 *
 * The autocomplete request can have facets&filters set; predefined order, etc
 * These are used align response elements (of different types or under different facets)
 *
 * By default, in Shopware6, the autocomplete response is from
 * the route("/suggest", name="frontend.search.suggest", methods={"GET"}, defaults={"XmlHttpRequest"=true})
 *
 * Can be customized to also have facets set/pre-defined. Please consult with Boxalino on more advanced scenarios.
 *
 * @package Boxalino\RealTimeUserExperienceIntegration\Service\Api
 */
class Autocomplete extends AutocompleteContextAbstract
{

    /**
     * @param RequestInterface $request
     * @return string
     */
    public function getContextNavigationId(RequestInterface $request): array
    {
        return [$this->getSalesChannelContext()->getSalesChannel()->getNavigationCategoryId()];
    }

    /**
     * @return int
     */
    public function getContextVisibility() : array
    {
        return [ProductVisibilityDefinition::VISIBILITY_SEARCH];
    }

    /**
     * @return array
     */
    public function getReturnFields() : array
    {
        return ["id", "discountedPrice", "link", "title", "image"];
    }

}
