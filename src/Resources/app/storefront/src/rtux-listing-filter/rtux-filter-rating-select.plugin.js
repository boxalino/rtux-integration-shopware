import FilterRatingSelectPlugin from 'src/plugin/listing/filter-rating-select.plugin';
import deepmerge from 'deepmerge';

export default class RtuxFilterRatingSelectPlugin extends FilterRatingSelectPlugin {
    static options = deepmerge(FilterRatingSelectPlugin.options, {
    });

}
