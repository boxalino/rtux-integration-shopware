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
     * @Route("/search", name="frontend.search.page", methods={"GET"})
     */
    public function search(SalesChannelContext $context, Request $request): Response
    {
        try {
            $this->requestWrapper->setRequest($request);
            /**
             * set the request parameter "position":"sidebar" for a sidebar search layout
             */
            $this->searchApiPageLoader->getApiContext()->addRequestParameter("position", "sidebar");
            $this->searchApiPageLoader->setSalesChannelContext($context)
                ->setRequest($this->requestWrapper)
                ->load();
            $page = $this->searchApiPageLoader->getApiResponsePage();
            if($page->getRedirectUrl())
            {
                return $this->redirect($page->getRedirectUrl());
            }

            /**
             * the render template is a narrative page
             * div-less (no search CSS classes) narrative template: @BoxalinoRealTimeUserExperience/storefront/element/cms-element-narrative-page.html.twig
             * search-1 column layout template: @BoxalinoRealTimeUserExperienceIntegration/storefront/narrative/page/search/index.html.twig
             * search-sidebar layout template: @BoxalinoRealTimeUserExperienceIntegration/storefront/narrative/page/search/index-with-sidebar.html.twig
             */
            $response = $this->renderStorefront('@BoxalinoRealTimeUserExperienceIntegration/storefront/narrative/page/search/index-with-sidebar.html.twig', ['page' => $page]);
            $response->headers->set('x-robots-tag', 'noindex');

            return $response;
        } catch (\Throwable $exception) {
            /**
             * Fallback
             */
            $this->logger->alert("BoxalinoAPI: There was an issue with your request." . $exception->getMessage());
            return $this->decorated->search($context, $request);
        }

    }

    /**
     * @Route("/suggest", name="frontend.search.suggest", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function suggest(SalesChannelContext $context, Request $request): Response
    {
        try{
            $this->requestWrapper->setRequest($request);
            $this->autocompleteApiPageLoader->setSalesChannelContext($context)
                ->setRequest($this->requestWrapper)
                ->load();
            $page = $this->autocompleteApiPageLoader->getApiResponsePage();
            /**
             * the render template is a narrative element
             */
            $response = $this->renderStorefront('@BoxalinoRealTimeUserExperience/storefront/element/cms-element-narrative-content.html.twig', ['page' => $page]);
            $response->headers->set('x-robots-tag', 'noindex');

            return $response;
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
     * @RouteScope(scopes={"storefront"})
     * @Route("/widgets/search", name="widgets.search.pagelet.v2", methods={"GET", "POST"}, defaults={"XmlHttpRequest"=true})
     *
     * @throws MissingRequestParameterException
     */
    public function ajax(Request $request, SalesChannelContext $context): Response
    {
        try{
            $this->requestWrapper->setRequest($request);
            /**
             * set the request parameter "position":"sidebar" for a sidebar search layout
             */
            $this->searchApiPageLoader->getApiContext()->addRequestParameter("position", "sidebar");
            $this->searchApiPageLoader->setSalesChannelContext($context)
                ->setRequest($this->requestWrapper)
                ->load();
            $page = $this->searchApiPageLoader->getApiResponsePage();

            /**
             * by DEFAULT, Shopware6 does not update facets&search page title on pagelet, only the content within cmsProductListingSelector
             * (as seen in the vendor/shopware/platform/src/Storefront/Resources/app/storefront/src/plugin/listing/listing.plugin.js, renderResponse action)
             * use the template for your scenario:
             * 1column layout: @BoxalinoRealTimeUserExperience/storefront/element/cms-element-narrative-content.html.twig
             * sidebar layout: @BoxalinoRealTimeUserExperience/storefront/element/cms-element-narrative-content-sidebar.html.twig
             */
            $response = $this->renderStorefront('@BoxalinoRealTimeUserExperience/storefront/element/cms-element-narrative-content-sidebar.html.twig', ['page' => $page]);
            $response->headers->set('x-robots-tag', 'noindex');

            return $response;
        } catch (\Throwable $exception)
        {
            /**
             * Fallback
             */
            $this->logger->warning("BoxalinoAPI: There was an issue with the pagelet request " . $exception->getMessage());
            return $this->decorated->ajax($request, $context);
        }

    }

}
