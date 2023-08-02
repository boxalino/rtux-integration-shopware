<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Storefront\Controller;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextServiceInterface;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Newsletter controller will be used as endpoint for the newsletter recommendation
 */
#[Route(defaults: ['_routeScope' => ['storefront']])]
class NewsletterController extends StorefrontController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SalesChannelRepository
     */
    private $productRepository;

    /**
     * @var SalesChannelContextServiceInterface
     */
    protected $salesChannelContextService;

    public function __construct(
        SalesChannelRepository $productRepository,
        SalesChannelContextServiceInterface $salesChannelContextService,
        LoggerInterface $logger
    ){
        $this->productRepository = $productRepository;
        $this->salesChannelContextService = $salesChannelContextService;
        $this->logger = $logger;
    }

    /**
     * Product template newseltter preview
     * for a given product on a required store-view
     *
     * @Route("/boxalino/newsletter/product/{language}/{id}", name="frontend.api.newsletter.product", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function product(SalesChannelContext $context, Request $request)
    {
        try {
            $productId = $request->get("id", null);
            $language = $request->get("language", "de");

            if($productId)
            {
                $criteria = new Criteria([$productId]);
                $product = $this->productRepository->search(
                    $criteria,
                    #$this->getSalesChannelContext($context->getSalesChannel()->getId(), $context->getSalesChannel()->getLanguageId())
                    $context
                )->getEntities()->first();

                return $this->renderStorefront('@BoxalinoRealTimeUserExperienceIntegration/storefront/narrative/element/cms-element-product-newsletter.html.twig', ['product' => $product]);
            }
        } catch (\Throwable $exception) {
            /**
             * Fallback if product does not exist or is not available ?!
             */
            $this->logger->alert("Newsletter Product Image Render: There was an issue with your request." . $exception->getMessage());
        }
    }

    /**
     * Load a specific language view for a SalesChannel
     * 
     * @param string $channelId
     * @param string $languageId
     * @return SalesChannelContext
     */
    protected function getSalesChannelContext(string $channelId, string $languageId) : SalesChannelContext
    {
        return $this->salesChannelContextService->get(
            $channelId,
            "api-newsletter-recommendation",
            $languageId
        );
    }


}
