import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios';
import {useUrl} from "../helpers/url";

export const useEntriesStore = defineStore('entries', {
    state: () => ({
        elements: [],
        params: {
            handle: 'superFilterShows',
            config: {
                currentPage: 1
            }
        },
        url: useUrl(),
        currentPage: 1,
        response: {}
    }),
    getters: {

    },
    actions: {
         async fetchData() {

            const response =  await axios.post( this.url.getUrl('super-filter/fields'), this.params);

            this.elements = response.data;
            this.currentPage = this.elements.config.currentPage;
            return this.elements;
        },
        action(params, method) {
             console.log('method ' + method)
            switch (method) {
                case 'next':
                    return this.next(params);
                    break;

                    case 'back':
                    return this.back(params);
                    break;

            }
        },
         async next(params) {
             params.config.currentPage = params.config.currentPage + 1;

            const response = await this._getResponse(params);

            this.elements = response.data;

            return this.elements;
        },

        async back(params) {
            params.config.currentPage = params.config.currentPage - 1;

            const response = await this._getResponse(params);

            this.elements = response.data;

            return this.elements;
        },

         _getResponse(params) {
            return axios.post( this.url.getUrl('super-filter/filter'), params);
        }
    }
})
