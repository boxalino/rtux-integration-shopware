import RtuxListingFilterPlugin from './rtux-listing-filter/rtux-listing-filter.plugin';
import RtuxAutocompletePlugin from './rtux-autocomplete/rtux-autocomplete.plugin';

const PluginManager = window.PluginManager;

PluginManager.register('RtuxListingFilterPlugin', RtuxListingFilterPlugin, '[data-rtux-listing-filter]');
PluginManager.extend('SearchWidget', 'SearchWidget', RtuxAutocompletePlugin, '[data-search-form]');
