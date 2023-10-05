/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

define([
    'underscore',
    'knockout'
], function (_, ko) {
    'use strict';

    /**
     * Get sortOrder for field
     *
     * @param idx
     * @param elems
     * @returns {null|number}
     */
    function getSortOrderFor(idx, elems){
        for(const elem of elems) {
            if (elem.name === idx) {
                const sortOrder = parseFloat(elem.sortOrder);
                if(isNaN(sortOrder)) {
                    return null;
                }
                return sortOrder;
            }
        }
        return null;
    }

    /**
     * Normalize Sort Orders
     *
     * To sort the fields dynamically we need to have a numeric sort order.
     * Some fields have a 'before: element-x' or 'after: element-y' we solve this
     *
     * By adding .01 to the related field, some One Step Checkouts have low
     * numeric values 1 - 10, so we add a very small number instead of integers.
     *
     * This loops max 5 times, so we can resolve up to 5 dependency layers
     * For Example: element x after element y, element y after element z ...
     *
     * @param elementArray
     * @returns {boolean}
     */
    function normalizeSortOrders(elementArray) {
        var allResolved = true;
        var maxLoops = 5;
        do {
            allResolved = true;

            for (const elem of elementArray) {
                if (typeof elem.sortOrder === 'object') {
                    if ('after' in elem.sortOrder) {
                        const newSortOrder = getSortOrderFor(
                            elem.sortOrder.after,
                            elementArray
                        );
                        if (newSortOrder === null) {
                            allResolved = false;
                            continue;
                        }
                        elem.sortOrder = newSortOrder + 0.01;
                        continue;
                    }

                    if ('before' in elem.sortOrder) {
                        const newSortOrder = getSortOrderFor(
                            elem.sortOrder.before,
                            elementArray
                        );
                        if (newSortOrder === null) {
                            allResolved = false;
                            continue;
                        }
                        elem.sortOrder = newSortOrder - 0.01;
                        continue;
                    }
                }

                if (typeof elem.sortOrder === "string") {
                    elem.sortOrder = parseFloat(elem.sortOrder);
                }
            }

        } while(!allResolved && --maxLoops > 0);

        return allResolved;
    }

    return function(fieldset){
       normalizeSortOrders(fieldset.elems());

       fieldset.elems.sort(((a, b) => {
            if(typeof a.sortOrder === 'undefined' || typeof b.sortOrder === 'undefined'){
                return 0;
            }

            const sortOrderA = a.sortOrder;
            const sortOrderB = b.sortOrder;

            if (sortOrderA < sortOrderB) {
                return -1;
            }

            if (sortOrderA > sortOrderB) {
                return 1;
            }
            return 0;
        }));
   }
});
