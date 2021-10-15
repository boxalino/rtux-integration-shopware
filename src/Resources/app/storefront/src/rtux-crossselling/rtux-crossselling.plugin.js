import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import ElementLoadingIndicatorUtil from 'src/utility/loading-indicator/element-loading-indicator.util';
import PluginManager from 'src/plugin-system/plugin.manager';
import DomAccess from 'src/helper/dom-access.helper';
import deepmerge from 'deepmerge';
import CrossSellingPlugin from 'src/plugin/cross-selling/cross-selling.plugin'
import StringHelper from 'src/helper/string.helper';

/**
 * Checks when the PDP page was loaded
 * Makes the API request
 * Return cross-selling tabs on PDP
 */
export default class RtuxCrosssellingPlugin extends CrossSellingPlugin
{

    static options = deepmerge(CrossSellingPlugin.options, {
        crossellingTabsListId: '#product-detail-cross-selling-tabs',
        adjacentPlugins: ["ProductSlider"],
        main: 0,
        url: '',
        csrfToken: ''
    });

    init() {
        // throw an error if the PDP cross-selling ID is missing from the view
        const tabs = document.querySelector(this.options.crossellingTabsListId);
        if (tabs instanceof Element === false) {
            throw Error('PDP cross-selling tabs element does not exist in your template!');
        }
        // if PDP-ajax loading is disabled, do nothing
        if(tabs.children.length > 0){
            return;
        }

        /** this should happen by default **/
        this.options = deepmerge(this.options, JSON.parse(DomAccess.getAttribute(this.el, `data-rtux-crossselling-options`)));
        this.client = new HttpClient();
        this._getCrossselling();
    }

    /**
     * Makes the API call
     * @private
     */
    _getCrossselling() {
        try{
            event.preventDefault();
            ElementLoadingIndicatorUtil.create(this.el);

            const data = this._getRequestData();
            this.$emitter.publish('beforeGetRtuxApiCrossselling');

            this.client.abort();
            this.client.post(this.options.url, JSON.stringify(data), response => this._showCrossselling(response));
        } catch (e) {
            console.log(e);
        }
    }

    /**
     * Adds the response HTML data to the view
     * @param responseHTMLData
     * @private
     */
    _showCrossselling(responseHTMLData) {
        try{
            //remove template children from the element
            while (this.el.firstChild) {
                this.el.removeChild(this.el.firstChild);
            }
            this.el.insertAdjacentHTML('beforeend', responseHTMLData)
            this._initPlugins();
        } catch (e) {
            console.log(e);
        }

        ElementLoadingIndicatorUtil.remove(this.el);
    }

    /**
     * Initializes ProductSlider plugin for each of the tab reco
     * Initializes the cross-selling.plugin to rebuild the ProductSlider
     * @private
     */
    _initPlugins(){
        const parentNode = this.el.parentNode
        for (const plugin of this.options.adjacentPlugins) {
            const elements = parentNode.querySelectorAll(`[data-${StringHelper.toDashCase(plugin)}]`);
            const optionsDataField = `${plugin}Options`;
            optionsDataField=optionsDataField.charAt(0).toLowerCase() + optionsDataField.substring(1);
            for (const element of elements) {
                const jsonOptions = element.dataset[optionsDataField];
                window.PluginManager.initializePlugin(plugin, element, jsonOptions ? JSON.parse(jsonOptions) : {});
            }
        }

        super.init();
    }

    /**
     * Send the product ID the csrf_token
     *
     * @returns {{id: string}}
     * @private
     */
    _getRequestData() {
        const data = {
            id: this.options.main
        };

        if (window.csrf.enabled && window.csrf.mode === 'twig') {
            data['_csrf_token'] = this.options.csrfToken;
        }

        return data;
    }


}
