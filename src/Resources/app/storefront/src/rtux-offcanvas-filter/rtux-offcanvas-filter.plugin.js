import Plugin from 'src/plugin-system/plugin.class';
import OffCanvas from 'src/plugin/offcanvas/offcanvas.plugin';
import DomAccess from 'src/helper/dom-access.helper';

/**
 * This class is a replica for the OffCanvasFilter from src/plugin/offcanvas-filter/offcanvas-filter.plugin
 * It is linked to RtuxListingFilterPlugin
 * In the filter-panel.html.twig template must be used data-rtux-offcanvas-filter-content="true" property instead of data-offcanvas-filter-content="true"
 */
export default class RtuxOffCanvasFilterPlugin extends Plugin
{

    /**
     * Register events to handle opening the Detail Filter OffCanvas
     * by clicking a defined trigger selector
     */
    init() {
        this.el.addEventListener('click', this._onClickOffRtuxCanvasFilter.bind(this));
    }

    /**
     * On clicking the trigger item the OffCanvas shall be closed and the current
     * filter content should be moved outside the OffCanvas.
     * @param {Event} event
     */
    _onCloseRtuxOffCanvas(event) {
        // move filter back to original place
        const oldChildNode = event.detail.offCanvasContent[0];
        const filterContent = document.querySelector('[data-rtux-offcanvas-filter-content="true"]');
        filterContent.innerHTML = oldChildNode.innerHTML;

        document.$emitter.unsubscribe('onCloseOffcanvas', this._onCloseRtuxOffCanvas.bind(this));
        window.PluginManager.getPluginInstances('RtuxListingFilterPlugin')[0].refreshRegistry();
    }

    /**
     * On clicking the trigger item the OffCanvas shall open and the current
     * filter content should be moved inside the OffCanvas.
     * @param {Event} event
     * @private
     */
    _onClickOffRtuxCanvasFilter(event) {
        event.preventDefault();
        const filterContent = document.querySelector('[data-rtux-offcanvas-filter-content="true"]');

        if (!filterContent) {
            throw Error('There was no DOM element with the data attribute "data-rtux-offcanvas-filter-content".')
        }

        OffCanvas.open(
            filterContent.innerHTML,
            () => {},
            'bottom',
            true,
            OffCanvas.REMOVE_OFF_CANVAS_DELAY(),
            true,
            ['offcanvas-filter','rtux-offcanvas-filter']
        );

        // move filter from original place to offcanvas
        const filterPanel = DomAccess.querySelector(filterContent, '.filter-panel');
        filterPanel.remove();

        window.PluginManager.getPluginInstances('RtuxListingFilterPlugin')[0].refreshRegistry();
        document.$emitter.subscribe('onCloseOffcanvas', this._onCloseRtuxOffCanvas.bind(this));

        this.$emitter.publish('onClickOffCanvasFilter');
    }


}
