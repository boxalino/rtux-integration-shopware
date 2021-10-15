import RtuxListingFilterPlugin from './rtux-listing-filter/rtux-listing-filter.plugin';
import RtuxAutocompletePlugin from './rtux-autocomplete/rtux-autocomplete.plugin';
import RtuxFilterRatingSelectPlugin from './rtux-listing-filter/rtux-filter-rating-select.plugin';
import RtuxFilterMultiSelectPlugin from './rtux-listing-filter/rtux-filter-multi-select.plugin';
import RtuxCrossSellingPlugin from './rtux-crossselling/rtux-crossselling.plugin';

const PluginManager = window.PluginManager;

PluginManager.register('RtuxListingFilterPlugin', RtuxListingFilterPlugin, '[data-rtux-listing-filter]');
PluginManager.extend('SearchWidget', 'SearchWidget', RtuxAutocompletePlugin, '[data-search-form]');
PluginManager.register('RtuxFilterRatingSelect', RtuxFilterRatingSelectPlugin, '[data-rtux-filter-rating-select]');
PluginManager.register('RtuxFilterMultiSelect', RtuxFilterMultiSelectPlugin, '[data-rtux-filter-multi-select]');
PluginManager.register('RtuxCrossSellingPlugin', RtuxCrossSellingPlugin, '[data-rtux-crossselling]');
