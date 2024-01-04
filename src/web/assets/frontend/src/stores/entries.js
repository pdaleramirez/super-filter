import {defineStore} from 'pinia'
import axios from 'axios';
import {useUrl} from "../helpers/url";

export const useEntriesStore = defineStore('entries', {
    state: () => ({
        handle: '',
        elements: [],
        searchFieldsInfo: {},
        templates: {},
        fields: {},
        items: [],
        records: [],
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

            const response = await axios.post(this.url.getUrl('super-filter/fields'), this.params);
            const searchFieldsInfoResponse = await axios.post(this.url.getUrl('super-filter/search-fields-info'), this.params);

            this.elements = response.data;
            this.searchFieldsInfo = searchFieldsInfoResponse.data;

            this.currentPage = this.elements.config.currentPage;
            return this.elements;
        },
        async fetchFields(handle) {
            this.params.handle = handle;
            const searchFieldsInfoResponse = await axios.post(this.url.getUrl('super-filter/search-fields-info'), this.params);
            this.searchFieldsInfo = searchFieldsInfoResponse.data;
        },
        async filterData(handle = null) {
            if (handle === null) {
                handle = this.handle;
            }

           this.prepareParams(handle);

            const response = await axios.post(this.url.getUrl('super-filter/fields'), this.params);

            this.elements.items = response.data.items;
            this.elements.links = response.data.links;
        },

        async pushData(handle = null) {
            if (handle === null) {
                handle = this.handle;
            }

            this.prepareParams(handle);

            const response = await axios.post(this.url.getUrl('super-filter/fields'), this.params);
            console.log('response.data.items');
            console.log(response.data.items);
            this.records = response.data.items;
        },
        prepareParams(handle) {
            this.params.handle = handle;

            for (let field of Object.values(this.searchFieldsInfo)) {

                if (field.value !== undefined && (field.value.length > 0 || field.value !== '')) {
                    this.params.config.params.fields[field.handle] = field.value;
                }

                if ((field.value !== undefined && (field.value.length <= 0 || field.value === '')) || field.value === undefined) {
                    delete this.params.config.params.fields[field.handle];
                }
            }

            if (this.elements.config !== undefined && this.elements.config.params.sort !== undefined) {
                this.params.config.params.sort = this.elements.config.params.sort;
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
