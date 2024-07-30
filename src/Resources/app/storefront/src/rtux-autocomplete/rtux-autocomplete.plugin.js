/* global window */
import ButtonLoadingIndicator from 'src/utility/loading-indicator/button-loading-indicator.util';
import SearchWidgetPlugin from 'src/plugin/header/search-widget.plugin';
import RtuxAutocompleteHelper from './rtux-autocomplete.helper';

/**
 * Autocomplete integration plugin
 * rewrites the function _suggest(value) from parent widget
 * uses the RtuxApiHelper to create the API request
 */
export default class RtuxAutocompletePlugin extends SearchWidgetPlugin {

    init() {
        super.init();
        this._renderHelper = new RtuxAutocompleteHelper();
    }

    /**
     * Process the API request and show result
     * If fail - execute parrent function
     *
     * @param {string} value
     * @private
     */
    _suggest(value) {
        if(window.rtuxAutocomplete === undefined)
        {
            return super._suggest(value);
        }

        this.isTest = window.rtuxAutocomplete['test'];
        this.rtuxApiHelper = window.PluginManager.getPluginInstances('RtuxApiHelper')[0];

        const indicator = new ButtonLoadingIndicator(this._submitButton);
        indicator.create();

        this.$emitter.publish('beforeSearch');
        this._client.abort();

        var requestData = JSON.stringify(this._getApiRequestData(value));
        var requestUrl = this.rtuxApiHelper.getApiRequestUrl(window.rtuxAutocomplete['url']);
        if(this.isTest) { console.log(requestUrl); console.log(requestData);}

        if(requestData) {
            this._client.post(requestUrl, requestData, (response)=> {
                this._clearSuggestResults();
                indicator.remove();
                try{
                    if(!response) {
                        throw new Error('RtuxApiAutocomplete error: the request failed');
                    }
                    var htmlResponse = this._renderHelper.getHtml(response, value);
                    if(this.isTest) { console.log(htmlResponse);}

                    this.el.insertAdjacentHTML('beforeend', htmlResponse);
                    this._handleAfterSuggest();

                    this.$emitter.publish('afterSuggest');
                } catch(error) {
                    if(this.isTest) { console.log(error);}
                    super._suggest(value);
                }
            },  'application/json', false );
        }
    }

    /**
     * additional parameters to be set: filters, facets, sort
     * for more details, check the Narrative Api Technical Integration manual provided by Boxalino
     *
     * @param value
     * @private
     */
    _getApiRequestData(value) {
        return this.rtuxApiHelper.getApiRequestData(
            window.rtuxAutocomplete['apiPreferentialAccount'],
            window.rtuxAutocomplete['apiPreferentialKey'],
            'autocomplete',
            window.rtuxAutocomplete['language'],
            'products_group_id',        //default group-by fields
            5,                          // number of products returned - as on production
            window.rtuxAutocomplete['dev'],
            window.rtuxAutocomplete['test'],
            null,
            this._getParametersForAcRequest(value)
        );
    }

    /**
     * Configurable parameters for AC request :
     * (int) acQueriesHitCount - number of textual suggestions
     * (bool) acHighlight - highlight matching sections
     * (string) acHighlightPre - textual suggestion highlight start -- for matching section
     * (string) acHighlightPost - textual suggestion highlight end -- for matching section
     * (string) query - the searched string
     * (array) filters - default filters for the AC request
     * 
     * @param value
     * @returns {{acHighlightPre: string, acHighlight: boolean, acHighlightPost: string, query, acQueriesHitCount: number, filters: *[]}}
     * @private
     */
    _getParametersForAcRequest(value) {
        return {
            'acQueriesHitCount': 12,     
            'acHighlight': true,        
            'acHighlightPre':"<em>",    
            'acHighlightPost':"</em>",
            'query':value,
            'filters': this._getFiltersForAcRequest()
        };
    }

    /**
     * Default filters for the AC request: status, visibility and store channel id category
     * Can be configured in IA - search TPO/widget as defaults
     * 
     * @returns []
     * @private
     */
    _getFiltersForAcRequest() {
        if(window.masterNavigationId)
        {
            return [
                {"field": "visibility","from": 20,"to": 1000,"fromInclusive": true, "toInclusive": true},
                {"field": "status","values": [1],"negative": false},
                {"field": "category_id", "values" : [window.masterNavigationId], "negative": false}
            ];
        }

        return [
            {"field": "visibility","from": 20,"to": 1000,"fromInclusive": true, "toInclusive": true},
            {"field": "status","values": [1],"negative": false}
        ]
    }

    /** FOLLOWING CODE HAS BEEN DUPLICATED FROM @SwagEnterpriseSearch SespSearchWidgetPlugin **/
    _handleAfterSuggest() {
        const searchTerms = this._getSearchTerms();
        const searchResult = this._getSearchResult();

        if (!searchResult) {
            return;
        }

        const searchResultItems = this._getResultItems(searchResult);

        if (searchResultItems.length === 0) {
            return;
        }

        searchResultItems.forEach((item) => {
            this._highlightSearchTerm(item, searchTerms);
        });
    }

    _highlightSearchTerm(item, terms) {
        terms.forEach((term) => {
            const regex = this._getRegex(term);

            item.innerHTML = item.innerHTML.replace(regex, '<b>$1</b>');
        });
    }

    _getRegex(term) {
        return new RegExp(`(${term})`, 'gi');
    }

    _getSearchTerms() {
        const terms = this._getSearchInput().value.split(' ');

        return terms.filter((term) => {
            return term !== '';
        });
    }

    _getSearchInput() {
        return this.el.querySelector('input[type=search]');
    }

    _getSearchResult() {
        return this.el.querySelector('.js-search-result');
    }

    _getResultItems(container) {
        return container.querySelectorAll('.js-search-result .search-suggest-product-name');
    }

}
