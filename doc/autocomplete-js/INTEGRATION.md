# JAVASCRIPT AUTOCOMPLETE INTEGRATION

Please be aware that there have to be designed Layout Blocks in Boxalino Intelligence Admin
and that there has to exist a narrative attached to the widget <b>autocomplete</b>

JSON samples of both elements are being provided for the _autocomplete_ scenario 
and can be imported in your Boxalino Intelligence Admin panel.
https://github.com/boxalino/rtux-integration-shopware/blob/master/doc/autocomplete

The guidelines follow a default Shopware autocomplete view/response.
The javascript integration strategy is 

## Steps
###### 1. Extend the base.html.twig and add the configuration for the JS

If no other (custom) configurations are needed, the data set on Storefront for the TrackerSubscriber can be used.
https://github.com/boxalino/rtux-shopware/blob/master/src/Resources/views/storefront/component/analytics.html.twig#L7

As seen in the extended template,
https://github.com/boxalino/rtux-integration-shopware/blob/master/src/Resources/views/storefront/layout/header/search.html.twig,
the following parameters have to be defined:
1. number of returned products _apiProductsCount_
2. number returned textual suggestion _apiSuggestionsCount_
3. your system group by field _apiProductsGroupBy_

Depending on your autocomplete design, other configurations can be set which then you can use in the Vue JS plugin.

###### 2. Set the return fields for autocomplete
Autocomplete acts as a "search" subrequest so it does not have a widget of it`s own in Boxalino Intelligence Admin.
By default, only the product ID will be part of the response. 
In order to change that, you have to add an optimization strategy to the *search* widget.

    1. Open the "search" widget in Boxalino Intelligence Admin
    2. Add the use-case: "Add return fields (simple)"
    3. Write the fields required for the autocomplete response.
    4. Save & test, save and then publish

You can also import the provided JSON https://github.com/boxalino/rtux-integration-shopware/blob/master/doc/autocomplete-js/optimization-strategy.json

###### 3. Extend the Shopware "SearchWidget" JS by rewriting the _suggest(value) function
The sample JS plugin can be found here:
https://github.com/boxalino/rtux-integration-shopware/blob/master/src/Resources/app/storefront/src/rtux-autocomplete/rtux-autocomplete.plugin.js

It makes use of the rtux-shopware plugin RtuxApiHelper to create the API request JSON
https://github.com/boxalino/rtux-shopware/blob/master/src/Resources/app/storefront/src/rtux-api/rtux-api.helper.js

###### 4. Create the render helper for the visual elements segments
Because it is a JS integration, the visual element "template" will be rendered via JS (functions or elements, per your choice)

A JS render helper can be as simple as the sample provided
https://github.com/boxalino/rtux-integration-shopware/blob/master/src/Resources/app/storefront/src/rtux-autocomplete/rtux-autocomplete.helper.js

To keep the order of the autocomplete response elements (products list, suggestion list, see all) detached from the technical integration,
a property *callback* has been set on the visual elements which dictates to the wrapper what template to render
https://github.com/boxalino/rtux-integration-shopware/blob/master/src/Resources/app/storefront/src/rtux-autocomplete/rtux-autocomplete.helper.js#L55

###### 5. Register the API autocomplete plugin
https://github.com/boxalino/rtux-integration-shopware/blob/master/src/Resources/app/storefront/src/main.js#L7


###### 6. Build the theme
``./psh.phar storefront:build``

