import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import Iterator from 'src/helper/iterator.helper';
import StringHelper from 'src/helper/string.helper';
import OffCanvas from 'src/plugin/offcanvas/offcanvas.plugin';

/**
 * Integration sample for making use of the default Shopware6 listing.plugin.js
 * The purpose of this plugin is to replicate the listing.plugin.js & filter-*.plugin.js dependenices
 * So that template-returned selected options/filter items are in sync with the facet registry
 *
 * This sample is not maintained by Boxalino
 */
export default class RtuxListingFilterPlugin extends Plugin
{
    /**
     * The ajax reloaded filters
     * @source ListingPlugin classes
     * @type {{parentFilterPanelSelector: string, activeFilterContainerSelector: string, resetAllFilterButtonSelector: string, activeFilterLabelRemoveClass: string}}
     */
    static options = {
        parentFilterPanelSelector: '.cms-element-product-listing-wrapper',
        activeFilterLabelRemoveClass: 'filter-active-remove',
        activeFilterContainerSelector: '.filter-panel-rtux-active-container',
        resetAllFilterButtonSelector: '.filter-reset-all',
        filterPlugins: ['FilterRange', 'RtuxFilterRatingSelect', 'RtuxFilterMultiSelect', 'FilterBoolean'],
        pluginSelectorPrefix: 'rtux',
        offCanvasSelector: '.rtux-offcanvas-filter'
    };

    init() {
        const parentFilterPanelElement = DomAccess.querySelector(document, this.options.parentFilterPanelSelector);
        this.listing = window.PluginManager.getPluginInstanceFromElement(
            parentFilterPanelElement,
            'Listing'
        );

        this.listing._filterPanelActive = false;
        this.listing.refreshRegistry();

        this._initFilterPlugins();

        this.activeFilterContainer = DomAccess.querySelector(
            document,
            this.options.activeFilterContainerSelector
        );
        this._buildLabels();
        this._prepareOffCanvas();
    }

    /**
     * @public
     */
    refreshRegistry() {
        this.init();
        window.PluginManager.initializePlugins();
    }

    _initFilterPlugins() {
        for (const plugin of this.options.filterPlugins) {
            let selector = `[data-${this.options.pluginSelectorPrefix}-${StringHelper.toDashCase(plugin)}]`;
            if(StringHelper.toDashCase(plugin).startsWith(this.options.pluginSelectorPrefix))
            {
                selector = `[data-${StringHelper.toDashCase(plugin)}]`;
            }
            const elements = this.el.querySelectorAll(selector);
            const optionsDataField = `${this.options.pluginSelectorPrefix}${plugin}Options`;
            for (const element of elements) {
                const jsonOptions = element.dataset[optionsDataField];
                window.PluginManager.initializePlugin(plugin, element, jsonOptions ? JSON.parse(jsonOptions) : {});
            }
        }
    }

    _buildLabels(){
        this.listing._registry.forEach((filterPlugin) => {
            filterPlugin.getValues();
        });
        const resetButtons = DomAccess.querySelectorAll(
            this.activeFilterContainer,
            `.${this.options.activeFilterLabelRemoveClass}`,
            false
        );

        if(resetButtons.length)
        {
            this.registerLabelEvents(resetButtons);
            this.createResetAllButton();
        }
    }

    /**
     * @source ListingPlugin
     * @param resetButtons
     * @private
     */
    registerLabelEvents(resetButtons) {
        Iterator.iterate(resetButtons, (label) => {
            label.addEventListener('click', () => this.resetFilter(label));
        });
    }

    /**
     * @param label
     */
    resetFilter(label){
        this.listing._filterPanelActive = false;
        this.listing.resetFilter(label);
        this.listing.refreshRegistry();
    }

    /**
     * Create the button to reset all active filters.
     * Register event listener to remove a single filter.
     *
     * @source ListingPlugin
     */
    createResetAllButton() {
        const resetAllButtonEl = DomAccess.querySelector(
            this.activeFilterContainer,
            this.options.resetAllFilterButtonSelector
        );

        resetAllButtonEl.addEventListener('click', this.resetAll.bind(this));
    }

    /**
     * When the "reset all" button is clicked the registry must also flush
     */
    resetAll()
    {
        this.listing._filterPanelActive = false;
        this.listing.resetAllFilter();
        this.listing.refreshRegistry();
        this.init();
    }

    /**
     * @private
     */
    _prepareOffCanvas() {
        // when the canvas is open, replace the content within the canvas
        if(this.hasOffCanvas()) {
            const filterContent = document.querySelector('[data-rtux-offcanvas-filter-content="true"]');
            const filterPanel = DomAccess.querySelector(filterContent, '.filter-panel', false);
            if(filterPanel)
            {
                OffCanvas.setContent(filterContent.innerHTML);
                filterPanel.remove();
            }
        }
    }

    /**
     * @returns bool
     */
    hasOffCanvas() {
        if(document.querySelector(this.options.offCanvasSelector)) {
            return true;
        }
        return false;
    }

    /**
     * @returns HTMLElement
     */
    getOffCanvas() {
        return document.querySelector(this.options.offCanvasSelector);
    }


}
