<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\DataIntegration\Document\Product\Attribute;

use Boxalino\DataIntegration\Service\Document\Product\ModeIntegrator;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;
use Boxalino\DataIntegrationDoc\Doc\Schema\Category as CategorySchema;
use Boxalino\DataIntegration\Service\Document\Product\Attribute\DeltaInstantTrait;

/**
 * Class Category
 * Category is the only hierarchical property in Shopware6
 *
 * UPDATE: EXPORTS THE CATEGORY_ID MAPPING BASED ON category_tree VALUES instead
 * PRACTICAL FLOW WHEN THE SHOPWARE6 DATABASE IS NOT REINDEXED
 *
 * @package Boxalino\RealTimeUserExperienceIntegration\DataIntegration\Document\Product\Attribute
 */
class Category extends ModeIntegrator
{

    use DeltaInstantTrait;

    /**
     * @return array
     */
    public function getValues() : array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();
        foreach($this->getData() as $item)
        {
            if(!isset($content[$item[$this->getDiIdField()]]))
            {
                $content[$item[$this->getDiIdField()]][DocSchemaInterface::FIELD_CATEGORIES] = [];
            }

            if(is_null($item[DocSchemaInterface::FIELD_INTERNAL_ID]))
            {
                continue;
            }

            /** @var CategorySchema $schema */
            $schema =  $this->getCategoryAttributeSchema(
                explode(",", $item[DocSchemaInterface::FIELD_INTERNAL_ID]),
                $languages
            );

            $content[$item[$this->getDiIdField()]][DocSchemaInterface::FIELD_CATEGORIES][] = $schema;
        }

        return $content;
    }

    /**
     * Get leaf category IDs
     * There is no difference between languages for each product
     *
     * @param string $propertyName
     * @return QueryBuilder
     */
    public function _getQuery(?string $propertyName = null) : QueryBuilder
    {
        return $this->_getProductQuery($this->_getQueryFields())
            ->andWhere("product.id IS NOT NULL")
            ->setParameter('channelId', Uuid::fromHexToBytes($this->getSystemConfiguration()->getSalesChannelId()), ParameterType::BINARY)
            ->setParameter('live', Uuid::fromHexToBytes(Defaults::LIVE_VERSION), ParameterType::BINARY)
            ->setParameter('channelRootCategoryId', $this->getSystemConfiguration()->getNavigationCategoryId(), ParameterType::STRING);
    }

    /**
     * @return string[]
     */
    protected function _getQueryFields() : array
    {
        return [
            "LOWER(HEX(id)) AS {$this->getDiIdField()}",
            'REPLACE(REPLACE(REPLACE(category_tree, "[",""), "]",""), "\"","") AS ' . DocSchemaInterface::FIELD_INTERNAL_ID
        ];
    }


}
