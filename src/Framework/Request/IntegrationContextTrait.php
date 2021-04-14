<?php
namespace Boxalino\RealTimeUserExperienceIntegration\Framework\Request;

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
            "rating_average" => ['from' => 'rating_average', 'to' => "0"],
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

}
