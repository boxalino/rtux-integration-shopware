<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceIntegration\Storefront\Controller;

use Boxalino\RealTimeUserExperience\Framework\Content\Page\ApiPageLoader as ApiAutocompletePageLoader;
use Boxalino\RealTimeUserExperience\Framework\Content\Page\ApiPageLoader as ApiSearchPageLoader;
use Boxalino\RealTimeUserExperienceApi\Service\Api\Request\RequestInterface as ShopwareRequestWrapper;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Product\SalesChannel\Search\AbstractProductSearchRoute;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\Framework\Routing\RoutingException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Search\SearchPageLoadedHook;
use Shopware\Storefront\Page\Search\SearchWidgetLoadedHook;
use Shopware\Storefront\Page\Suggest\SuggestPageLoadedHook;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Shopware\Storefront\Controller\SearchController as ShopwareSearchController;
use Shopware\Storefront\Framework\Cache\Annotation\HttpCache;
use Symfony\Component\Routing\Annotation\Route;

/**
 * RTUX SearchController - decorates Shopware SearchController; makes RTUX API requests and displays the response
 */
#[Route(defaults: ['_routeScope' => ['storefront']])]
class SearchController extends ShopwareSearchController
{

    public function __construct(
        private readonly ShopwareSearchController $decorated,
        private readonly AbstractProductSearchRoute $productSearchRoute,
        private readonly  ApiSearchPageLoader $searchApiPageLoader,
        private readonly ApiAutocompletePageLoader $autocompleteApiPageLoader,
        private readonly ShopwareRequestWrapper $requestWrapper,
        private readonly LoggerInterface $logger
    ){
    }

    #[Route(path: '/search', name: 'frontend.search.page', defaults: ['_httpCache' => false], methods: ['GET'])]
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

    #[Route(path: '/suggest', name: 'frontend.search.suggest', defaults: ['XmlHttpRequest' => true, '_httpCache' => false], methods: ['GET'])]
    public function suggest(SalesChannelContext $context, Request $request): Response
    {
        try{
            $this->requestWrapper->setRequest($request);
            $this->autocompleteApiPageLoader->setSalesChannelContext($context)
                ->setRequest($this->requestWrapper)
                ->load();
            $page = $this->autocompleteApiPageLoader->getApiResponsePage();

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


    #[Route(path: '/widgets/search', name: 'widgets.search.pagelet.v2', defaults: ['XmlHttpRequest' => true, '_routeScope' => ['storefront'], '_httpCache' => false], methods: ['GET', 'POST'])]
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

    /**
     * Route to load the available listing filters
     * (only included in the decorator because  the variables in  parent class are not  available)
     *
     * ---- MUST BE REPLACED TO DISPLAY BOXALINO FILTERS ------
     */
    #[Route(path: '/widgets/search/filter', name: 'widgets.search.filter', defaults: ['XmlHttpRequest' => true, '_routeScope' => ['storefront'], '_httpCache' => false], methods: ['GET', 'POST'])]
    public function filter(Request $request, SalesChannelContext $context): Response
    {
        $term = $request->get('search');
        if (!$term) {
            throw RoutingException::missingRequestParameter('search');
        }

        $response = new JsonResponse([]);
        $response->headers->set('x-robots-tag', 'noindex');

        return $response;
    }

}
