import DomAccess from 'src/helper/dom-access.helper';
import Iterator from 'src/helper/iterator.helper';
import FilterMultiSelectPlugin from 'src/plugin/listing/filter-multi-select.plugin';
import deepmerge from 'deepmerge';

/**
 * Extended from the default filter-multer-select plugin
 * In order to use "value" for the URL (SEO)
 * and to use different id-value on the checkboxes (to allow same-value facet options across facets)
 */
export default class RtuxFilterMultiSelectPlugin extends FilterMultiSelectPlugin {

    static options = deepmerge(FilterMultiSelectPlugin.options, {
    });

    getValues() {
        const checkedCheckboxes =
            DomAccess.querySelectorAll(this.el, `${this.options.checkboxSelector}:checked`, false);

        let selection = [];

        if (checkedCheckboxes) {
            Iterator.iterate(checkedCheckboxes, (checkbox) => {
                selection.push(checkbox.value);
            });
        } else {
            selection = [];
        }

        this.selection = selection;
        this._updateCount();

        const values = {};
        values[this.options.name] = selection;

        return values;
    }

    /**
     *
     * @param params
     * @returns {boolean}
     */
    setValuesFromUrl(params) {
        let stateChanged = false;
        Object.keys(params).forEach(key => {
            if (key === this.options.name) {
                stateChanged = true;
                const ids = params[key].split('|');

                ids.forEach(id => {
                    const checkboxEl = DomAccess.querySelector(this.el, `[value="${id}"]`, false);

                    if (checkboxEl) {
                        checkboxEl.checked = true;
                        this.selection.push(checkboxEl.value);
                    }
                });
            }
        });

        super._updateCount();

        return stateChanged;
    }


    /**
     * @return {Array}
     * @public
     */
    getLabels() {
        const activeCheckboxes =
            DomAccess.querySelectorAll(this.el, `${this.options.checkboxSelector}:checked`, false);

        let labels = [];

        if (activeCheckboxes) {
            Iterator.iterate(activeCheckboxes, (checkbox) => {
                labels.push({
                    label: checkbox.dataset.label,
                    value: checkbox.dataset.value,
                    id: checkbox.id
                });
            });
        } else {
            labels = [];
        }

        return labels;
    }
}
