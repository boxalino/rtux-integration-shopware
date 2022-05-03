<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Storefront\Controller;

use Boxalino\RealTimeUserExperience\Framework\Content\Page\ApiCrossSellingLoaderAjax as PdpPageLoader;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface as ShopwareRequestWrapper;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Storefront\Framework\Cache\Annotation\HttpCache;

/**
 * RTUX PdpController - ajax request on product detail page (PDP) in order to load & display cross-selling elements
 * https://github.com/boxalino/rtux-integration-shopware/wiki/PDP-Context-(AJAX)
 *
 * @RouteScope(scopes={"storefront"})
 */
class PdpController extends StorefrontController
{
    /**
     * @var PdpPageLoader
     */
    private $pdpApiPageLoader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ShopwareRequestWrapper
     */
    private $requestWrapper;

    public function __construct(
        PdpPageLoader $pdpApiPageLoader,
        ShopwareRequestWrapper $requestWrapper,
        LoggerInterface $logger
    ){
        $this->requestWrapper = $requestWrapper;
        $this->pdpApiPageLoader = $pdpApiPageLoader;
        $this->logger = $logger;
    }

    /**
     * @RouteScope(scopes={"storefront"})
     * @Route("/boxalino-api/pdp/crosssell", name="frontend.boxalino-api.crosssell", methods={"GET", "POST"}, defaults={"XmlHttpRequest"=true})
     */
    public function ajax(Request $request, SalesChannelContext $context): Response
    {
        try {
            $request->attributes->set("mainProductId", $request->get("id", 0));

            $this->requestWrapper->setRequest($request);
            $this->pdpApiPageLoader->setSalesChannelContext($context)
                ->setRequest($this->requestWrapper)
                ->load();

            $page = $this->pdpApiPageLoader->getApiResponsePage();

            return $this->renderStorefront('@BoxalinoRealTimeUserExperienceIntegration/storefront/narrative/page/product-detail/cross-selling/tabs.html.twig', ['page' => $page]);
        } catch (\Throwable $exception) {
            /**
             * Do not return any content otherwise
             */
            $this->logger->alert("PDP BoxalinoAPI JS: There was an issue with your request." . $exception->getMessage());
            return new Response("");
        }
    }


}
