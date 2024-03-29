<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Storefront\Controller;

use Boxalino\RealTimeUserExperience\Framework\Content\Page\ApiPageLoader as DynamicPageLoader;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface as ShopwareRequestWrapper;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * RTUX DynamicController - makes RTUX API requests and displays the narrative response (ex: campaigns, brand pages, etc)
 */
#[Route(defaults: ['_routeScope' => ['storefront']])]
class DynamicController extends StorefrontController
{
    /**
     * @var DynamicPageLoader
     */
    private $dynamicApiPageLoader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ShopwareRequestWrapper
     */
    private $requestWrapper;

    public function __construct(
        DynamicPageLoader $dynamicApiPageLoader,
        ShopwareRequestWrapper $requestWrapper,
        LoggerInterface $logger
    ){
        $this->requestWrapper = $requestWrapper;
        $this->dynamicApiPageLoader = $dynamicApiPageLoader;
        $this->logger = $logger;
    }

    /**
     * The brand layout will have a sidebar
     *
     * Show a brand page, it accepts dynamic brand name
     * If no match in your synchronized data - redirect to home page
     *
     * @Route("/brand/{brand}", name="frontend.brand.page", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function brand(SalesChannelContext $context, Request $request)
    {
        try {
            $this->requestWrapper->setRequest($request);

            $this->dynamicApiPageLoader->getApiContext()->setWidget("brand");
            $this->dynamicApiPageLoader->getApiContext()->addRequestParameter("position", "sidebar");
            
            $this->dynamicApiPageLoader->setSalesChannelContext($context)
                ->setRequest($this->requestWrapper)
                ->load();
            $page = $this->dynamicApiPageLoader->getApiResponsePage();

            return $this->renderStorefront('@BoxalinoRealTimeUserExperience/storefront/element/cms-element-narrative-page-sidebar.html.twig', ['page' => $page]);
        } catch (\Throwable $exception) {
            /**
             * Fallback to home page
             */
            $this->logger->alert("Dynamic Brand BoxalinoAPI: There was an issue with your request." . $exception->getMessage());
            return $this->redirectToRoute('frontend.home.page');
        }
    }

    /**
     * The campaigns are defined in Boxalino Intelligence Admin (IA)
     * The mapping is done based on the name which is set as a parameter for the request
     *
     * The campaign layout is single-column
     *
     * Show a campaign page, it accepts dynamic input
     * If no match in your synchronized data - redirect to home page
     *
     * @Route("/campaign/{campaign}", name="frontend.campaign.page", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function campaign(SalesChannelContext $context, Request $request)
    {
        try {
            $this->requestWrapper->setRequest($request);

            $this->dynamicApiPageLoader->getApiContext()->setWidget("campaign");
            
            $this->dynamicApiPageLoader->setSalesChannelContext($context)
                ->setRequest($this->requestWrapper)
                ->load();
            $page = $this->dynamicApiPageLoader->getApiResponsePage();

            return $this->renderStorefront('@BoxalinoRealTimeUserExperience/storefront/element/cms-element-narrative-page.html.twig', ['page' => $page]);
        } catch (\Throwable $exception) {
            /**
             * Fallback to home page
             */
            $this->logger->alert("Dynamic Campaign BoxalinoAPI: There was an issue with your request." . $exception->getMessage());
            return $this->redirectToRoute('frontend.home.page');
        }
    }
    
    
}
