import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios';
import {useUrl} from "../helpers/url";

export const useEntriesStore = defineStore('entries', {
    state: () => ({
        elements: [],
        templateVar: {},
        fields: {},
        items: [],
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
         async fetchGql() {
            const query = `{
  entries(section: "superFilterShows" ) { title }
}`;

             const headers = {
                 'Authorization': 'Bearer BGk2lakdab3ztbEavoAXtJdSPyoohkoB',
                 'Content-Type': 'application/graphql', // Example header for specifying JSON content
                 // Add any other custom headers here
             };

             const response =  await axios.post( this.url.getUrl('super-filter/fields'), query, {
                 headers: headers
             });

             this.items = response.data;
             this.currentPage = this.elements.config.currentPage;
             return this.items;
         },
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

        async getTemplate(handle) {
            const response = await axios.post( this.url.getUrl('super-filter/template'), {handle: handle});
            this.templateVar = response.data;
            return response.data;
        },

        async getTestRequest(handle) {
            const response = await axios.get( '/api/test-api', {handle: handle});
            this.templateVar = response.data;

            return response.data;
        },

        async getFieldRequest(handle) {
            const response = await axios.get( '/api/test-fields', {handle: handle});
            this.fields = response.data;

            return response.data;
        },

         _getResponse(params) {
            return axios.post( this.url.getUrl('super-filter/filter'), params);
        }
    }
})
