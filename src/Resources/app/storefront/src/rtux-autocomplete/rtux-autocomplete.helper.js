/**
 * Holds the template for the autocomplete rendering
 * The functions designed here must match the ones set as the "callback" property on the level 0 visual block
 */
export default class RtuxAutocompleteHelper {

    /**
     * Generic helper function to parse the response JSON and to render all blocks
     * by calling the function matching the "callback" parameter of the visual block
     *
     * @public
     * @param response
     * @param value
     * @returns {string}
     */
    getHtml(response, value) {
        this._value = value;
        var responseData = JSON.parse(response);
        var html = '';
        responseData['blocks'].forEach(function(block) {
            if(block['callback']) {
                html += this.toHtml(block['callback'][0], block);
            }
        }.bind(this));

        return html;
    }

    /**
     * Generic helper function to render the template by calling the function set as visual block property "callback"
     *
     * @public
     * @param name
     * @param block
     * @returns {string|*}
     */
    toHtml(name, block) {
        try {
            return this[name](block);
        } catch (e) {
            return '<div></div>';
        }
    }

    /**
     * Based on a default autocomplete-js layout, the origin is a "wrapper" element with:
     * -> suggestion list (rendered in getSuggestionListHtml(block){})
     * -> product list (rendered in getProductListHtml(block){})
     * -> see all link (rendered in getSeeAllHtml(block){})
     *
     * @public
     * @param bxblock
     * @returns {string}
     */
    getWrapperHtml(bxblock) {
        var html = '<div class="search-suggest js-search-result">';
        html += '<ul class="search-suggest-container">';
        bxblock['blocks'].forEach(function (block) {
            if (block['callback']) {
                html += this.toHtml(block['callback'][0], block);
            }
        }.bind(this));
        html += '</ul></div>';

        return html;
    }

    /**
     * Renders every bx-hit (product) element via getProductItemHtml(block)
     *
     * @public
     * @param bxblock
     * @returns {string}
     */
    getProductListHtml(bxblock) {
        var html = '<div class="bx-narrative">';
        this._totalProductsFound = bxblock['bx-hits']['totalHitCount'];
        bxblock['blocks'].forEach(function(block){
            if(block['callback']) {
                html += this.getProductItemHtml(block);
            }
        }.bind(this));
        html +='</div>';

        return html;
    }

    /**
     * Template for how a product recommendation is displayed
     *
     * @public
     * @param bxblock
     * @returns {string}
     */
    getProductItemHtml(bxblock) {
        var html = '<li class="search-suggest-product js-result">';
        html += '<a href="/' + bxblock['bx-hit']['products_seo_url'][0] +'" title="' +
            bxblock['bx-hit']['products_title'] + '" class="search-suggest-product-link">';
        html += '<div class="row align-items-center no-gutters bx-narrative-item">';
        html += '<div class="col-auto search-suggest-product-image-container">'+
            '<img src="' + bxblock['bx-hit']['products_image'][0] + '" ' +
            'srcset="'+ bxblock['bx-hit']['products_image'][0] +'" class="search-suggest-product-image" ' +
            'alt="'+ bxblock['bx-hit']['title'] +'"/></div>';
        html += '<div class="col search-suggest-product-name">'+ bxblock['bx-hit']['title'] +'</div>';
        html += '<div class="col-auto search-suggest-product-price"><br><small class="search-suggest-product-reference-price">' +
            bxblock['bx-hit']['discountedPrice'] + '&nbsp;' + window.rtuxAutocomplete['currency'] +
            '</small></div></div></a></li>';

        return html;
    }

    /**
     * Renders every bx-acQuery (textual suggestion) element via getSuggestionItemHtml(block)
     *
     * @public
     * @param bxblock
     * @returns {string}
     */
    getSuggestionListHtml(bxblock) {
        var html = '';
        bxblock['blocks'].forEach(function(block){
            if(block['callback']) {
                html += this.getSuggestionItemHtml(block);
            }
        }.bind(this));

        return html;
    }

    /**
     * @public
     * @param bxblock
     * @returns {string}
     */
    getSuggestionItemHtml(bxblock) {
        var html ='';
        if(bxblock['accessor'] === 'accessor') {
            let suggestion = bxblock['bx-acQuery']['highlighted'];
            if(suggestion == null) {suggestion = bxblock['bx-acQuery']['suggestion'];}
            html +='<li class="search-suggest-product js-result">';
            html +=' <a href="'+ window.rtuxAutocomplete['suggestLink'] +
                encodeURIComponent(bxblock['bx-acQuery']['suggestion']) + '"\n' +
                '       title="' + bxblock['bx-acQuery']['suggestion'] + '"\n' +
                '       class="search-suggestion-link">\n' +
                '        <p>'+ suggestion +'</p>\n' +
                '    </a>';
            html+='</li>';
        }

        return html;
    }

    /**
     * Displays the "see all" link
     * (per Shopware6 default autocomplete structure)
     *
     * @public
     * @returns {string}
     */
    getSeeAllHtml() {
        var html = '';
        if(this._totalProductsFound > 0) {
            html +='<li class="js-result search-suggest-total"><div class="row align-items-center no-gutters"><div class="col">';
            html +='<a href="'+ window.rtuxAutocomplete['suggestLink'] + this._value + '" ' +
                'title="'+ window.rtuxAutocomplete['seeAllSearchResultsMessage'] + '" '+
                'class="search-suggest-total-link">'+
                window.rtuxAutocomplete['seeAllSearchResultsMessage'] +
                '</a></div><div class="col-auto search-suggest-total-count">' +
                this._totalProductsFound + window.rtuxAutocomplete['seeAllSearchResultLabel'] + '</div></div></li>';
        } else {
            html += '<li class="search-suggest-no-result">' + window.rtuxAutocomplete['noSearchResultsMessage'] + '</li>';
        }

        return html;
    }

}
