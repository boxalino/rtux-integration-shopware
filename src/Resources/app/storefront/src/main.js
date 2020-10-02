import RtuxListingFilterPlugin from './rtux-listing-filter/rtux-listing-filter.plugin';
import RtuxAutocompletePlugin from './rtux-autocomplete/rtux-autocomplete.plugin';
import RtuxFilterRatingPlugin from './rtux-listing-filter/rtux-filter-rating.plugin';

const PluginManager = window.PluginManager;

PluginManager.register('RtuxListingFilterPlugin', RtuxListingFilterPlugin, '[data-rtux-listing-filter]');
PluginManager.extend('SearchWidget', 'SearchWidget', RtuxAutocompletePlugin, '[data-search-form]');
PluginManager.override('FilterRating', RtuxFilterRatingPlugin, '[data-filter-rating]')
