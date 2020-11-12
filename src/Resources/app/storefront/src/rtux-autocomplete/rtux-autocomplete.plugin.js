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
        var otherParameters = {
            'acQueriesHitCount': 4,     // number of textual sugestions
            'acHighlight': true,        // highlight matching sections
            'acHighlightPre':"<em>",    //textual suggestion highlight start -- for matching section
            'acHighlightPost':"</em>",  //textual suggsstion highlight end -- for matching section
            'query':value,
            'filters': [
                {"field": "products_visibility","from": 20,"to": 1000,"fromInclusive": true, "toInclusive": true},
                {"field": "products_active","values": [1],"negative": false}
            ]
        };
        return this.rtuxApiHelper.getApiRequestData(
            window.rtuxAutocomplete['apiPreferentialAccount'],
            window.rtuxAutocomplete['apiPreferentialKey'],
            'autocomplete',
            window.rtuxAutocomplete['language'],
            'products_group_id',        //default group-by fields
            5,                          // number of products returned
            window.rtuxAutocomplete['dev'],
            window.rtuxAutocomplete['test'],
            null,
            otherParameters
        );
    }

}
