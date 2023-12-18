import {defineStore} from 'pinia'
import axios from 'axios';
import {useUrl} from "../helpers/url";

export const useEntriesStore = defineStore('entries', {
    state: () => ({
        handle: '',
        elements: [],
        searchFieldsInfo: [],
        templates: {},
        fields: {},
        items: [],
        fieldValue: '',
        params: {
            handle: '',
            config: {
                currentPage: 1,
                params: {
                    fields: {},
                    options: {
                        perPage: 1
                    },
                }
            },

        },
        url: useUrl(),
        currentPage: 1,
        response: {}
    }),
    getters: {},
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

            const response = await axios.post(this.url.getUrl('super-filter/fields'), query, {
                headers: headers
            });

            this.items = response.data;
            this.currentPage = this.elements.config.currentPage;
            return this.items;
        },
        async fetchData(handle) {
            this.params.handle = handle;
            //this.params.config.params.fields.title = 'attack';

            const response = await axios.post(this.url.getUrl('super-filter/fields'), this.params);
            const searchFieldsInfoResponse = await axios.post(this.url.getUrl('super-filter/search-fields-info'), this.params);

            this.elements = response.data;
            this.searchFieldsInfo = searchFieldsInfoResponse.data;

            this.currentPage = this.elements.config.currentPage;
            return this.elements;
        },
        async filterData(handle = null) {
            if (handle === null) {
                handle = this.handle;
            }

            this.params.handle = handle;
            for (const [key] of Object.entries(this.searchFieldsInfo)) {

                this.params.config.params.fields[key] = this.searchFieldsInfo[key].value;

                if (this.searchFieldsInfo[key].value === '' || this.searchFieldsInfo[key].value.length <= 0) {
                    delete this.params.config.params.fields[key];
                }
            }

            const response = await axios.post(this.url.getUrl('super-filter/fields'), this.params);

             this.elements.items = response.data.items;
             this.elements.links = response.data.links;
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

        async getTemplate(handle, filename) {

            const response = await axios.post(this.url.getUrl('super-filter/template'), {
                handle: handle,
                filename: filename
            });
            this.templates[filename] = response.data;

            return response.data;
        },

        _getResponse(params) {
            return axios.post(this.url.getUrl('super-filter/filter'), params);
        }
    }
})
