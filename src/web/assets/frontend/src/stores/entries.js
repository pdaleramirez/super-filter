import {ref, computed} from 'vue'
import {defineStore} from 'pinia'
import axios from 'axios';
import {useUrl} from "../helpers/url";

export const useEntriesStore = defineStore('entries', {
    state: () => ({
        handle: '',
        elements: [],
        searchFieldsInfo: [],
        templateContent: '',
        templateFields: '',
        templateList: '',
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
            for (const [key, field] of Object.entries(this.searchFieldsInfo)) {
                if (this.searchFieldsInfo[key].value !== '') {
                    this.params.config.params.fields[key] = this.searchFieldsInfo[key].value;
                }
            }

            const response = await axios.post(this.url.getUrl('super-filter/fields'), this.params);

             this.elements = response.data;
        },
        action(params, method) {
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

        async getTemplate(handle, filename) {

            const response = await axios.post(this.url.getUrl('super-filter/template'), {
                handle: handle,
                filename: filename
            });
            this.templateContent = response.data;

            return response.data;
        },
        async getFieldTemplate(handle, filename) {

            const response = await axios.post(this.url.getUrl('super-filter/template'), {
                handle: handle,
                filename: filename
            });
            this.templateFields = response.data;

            return response.data;
        },
        async getListTemplate(handle, filename) {

            const response = await axios.post(this.url.getUrl('super-filter/template'), {
                handle: handle,
                filename: filename
            });
            this.templateList = response.data;

            return response.data;
        },

        async getTestRequest(handle) {
            const response = await axios.get('/api/test-api', {handle: handle}, {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    //  "Access-Control-Allow-Origin": "*"
                }
            });
            this.templateContent = response.data;

            return response.data;
        },

        async getFieldRequest(handle) {
            const response = await axios.get(this.url.getUrl('test-fields'), {handle: handle});
            this.fields = response.data;

            return response.data;
        },

        _getResponse(params) {
            return axios.post(this.url.getUrl('super-filter/filter'), params);
        }
    }
})
