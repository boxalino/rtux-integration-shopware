<?php
namespace Boxalino\RealTimeUserExperienceIntegration\Framework\Request;

use Boxalino\RealTimeUserExperienceApi\Service\Api\Response\Accessor\AccessorFacetModelInterface;

/**
 * Trait IntegrationContextTrait
 * Common context functions
 *
 * @package Boxalino\RealTimeUserExperienceIntegration\Framework\Request
 */
trait IntegrationContextTrait
{
    /**
     * Set the range properties following the presented structure
     *
     * @return array
     */
    public function getRangeProperties() : array
    {
        return [
            "rating_average" => ['from' => 'products_rating_average', 'to' => "0"],
            "discountedPrice" => ['from' => 'min-price', 'to' => 'max-price']
        ];
    }

    /**
     * @return string
     */
    public function getFilterValuesDelimiter(): string
    {
        return "|";
    }

    /**
     * @return string
     */
    public function getFacetPrefix() : string
    {
        return AccessorFacetModelInterface::BOXALINO_STORE_FACET_PREFIX;
    }

    /**
     * Function to be rewritten based on client integration
     * (either set different properties based on the product attribute or set general ones: key->value)
     * Review documentation for available options
     * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/8749643/Narrative+API+-+Technical+Reference#Facets-Request-Parameters
     *
     * @param string $propertyName
     * @return array
     */
    public function getFacetRequestProperties(string $propertyName) : array
    {
        return [];
    }


}
