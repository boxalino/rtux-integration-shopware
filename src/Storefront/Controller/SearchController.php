<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Storefront\Controller;

use Boxalino\RealTimeUserExperience\Framework\Content\Page\ApiPageLoader as AutocompletePageLoader;
use Boxalino\RealTimeUserExperience\Framework\Content\Page\ApiPageLoader as SearchPageLoader;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface as ShopwareRequestWrapper;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Shopware\Storefront\Controller\SearchController as ShopwareSearchController;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Storefront\Framework\Cache\Annotation\HttpCache;
use Symfony\Component\Routing\Annotation\Route;

/**
 * RTUX SearchController - decorates Shopware SearchController; makes RTUX API requests and displays the response
 * @RouteScope(scopes={"storefront"})
 */
class SearchController extends ShopwareSearchController
{
    /**
     * @var SearchPageLoader
     */
    private $searchApiPageLoader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AutocompletePageLoader
     */
    private $autocompleteApiPageLoader;

    /**
     * @var ShopwareSearchController
     */
    private $decorated;

    /**
     * @var ShopwareRequestWrapper
     */
    private $requestWrapper;

    public function __construct(
        ShopwareSearchController $decorated,
        SearchPageLoader $searchApiPageLoader,
        AutocompletePageLoader $autocompleteApiPageLoader,
        ShopwareRequestWrapper $requestWrapper,
        LoggerInterface $logger
    ){
        $this->decorated = $decorated;
        $this->requestWrapper = $requestWrapper;
        $this->searchApiPageLoader = $searchApiPageLoader;
        $this->autocompleteApiPageLoader = $autocompleteApiPageLoader;
        $this->logger = $logger;
    }

    /**
     * @HttpCache()
     * @Route("/search", name="frontend.search.page", methods={"GET"})
     */
    public function search(SalesChannelContext $context, Request $request): Response
    {
        try {
            $this->requestWrapper->setRequest($request);
            $this->searchApiPageLoader->setSalesChannelContext($context)
                ->setRequest($this->requestWrapper)
                ->load();
            $page = $this->searchApiPageLoader->getApiResponsePage();
            if($page->getRedirectUrl())
            {
                return $this->forwardToRoute($page->getRedirectUrl());
            }

            /**
             * the render template is a narrative page
             * div-less narrative template: @BoxalinoRealTimeUserExperience/storefront/element/cms-element-narrative-page.html.twig
             */
            return $this->renderStorefront('@BoxalinoRealTimeUserExperienceIntegration/storefront/narrative/page/search/index.html.twig', ['page' => $page]);
        } catch (\Throwable $exception) {
            /**
             * Fallback
             */
            $this->logger->alert("BoxalinoAPI: There was an issue with your request." . $exception->getMessage());
            return $this->decorated->search($context, $request);
        }

    }

    /**
     * @HttpCache()
     * @Route("/suggest", name="frontend.search.suggest", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function suggest(SalesChannelContext $context, Request $request): Response
    {
        try{
            $this->requestWrapper->setRequest($request);
            $this->searchApiPageLoader->setSalesChannelContext($context)
                ->setRequest($this->requestWrapper)
                ->load();
            $page = $this->searchApiPageLoader->getApiResponsePage();
            /**
             * the render template is a narrative element
             */
            return $this->renderStorefront('@BoxalinoRealTimeUserExperience/storefront/element/cms-element-narrative-content.html.twig', ['page' => $page]);
        } catch (\Throwable $exception)
        {
            /**
             * Fallback
             */
            $this->logger->warning("BoxalinoAPI: There was an issue with the autocomplete request " . $exception->getMessage());
            return $this->decorated->suggest($context, $request);
        }
    }

    /**
     * @HttpCache()
     * @RouteScope(scopes={"storefront"})
     * @Route("/widgets/search/{search}", name="widgets.search.pagelet", methods={"GET", "POST"}, defaults={"XmlHttpRequest"=true})
     *
     * @throws MissingRequestParameterException
     */
    public function pagelet(Request $request, SalesChannelContext $context): Response
    {
        try{
            $this->requestWrapper->setRequest($request);
            $this->searchApiPageLoader->setSalesChannelContext($context)
                ->setRequest($this->requestWrapper)
                ->load();
            $page = $this->searchApiPageLoader->getApiResponsePage();

            /**
             * by DEFAULT, Shopware6 does not update facets&search page title on pagelet, only the content within cmsProductListingSelector
             * (as seen in the vendor/shopware/platform/src/Storefront/Resources/app/storefront/src/plugin/listing/listing.plugin.js, renderResponse action)
             */
            return $this->renderStorefront('@BoxalinoRealTimeUserExperience/storefront/element/cms-element-narrative-content.html.twig', ['page' => $page]);
        } catch (\Throwable $exception)
        {
            /**
             * Fallback
             */
            $this->logger->warning("BoxalinoAPI: There was an issue with the pagelet request " . $exception->getMessage());
            return $this->decorated->pagelet($request, $context);
        }

    }

}
