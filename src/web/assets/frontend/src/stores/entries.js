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
        response: {},
        isInfiniteScroll: false,
        itemAttributes: {},
        csrfName: '',
        csrfToken: '',
    }),
    getters: {},
    actions: {
        async fetchData(handle) {
            this.params.handle = handle;

            const response = await this.fetchDataWithCsrf('super-filter/fields');
            const searchFieldsInfoResponse = await this.fetchDataWithCsrf('super-filter/search-fields-info');

            this.elements = response.data;
            this.searchFieldsInfo = searchFieldsInfoResponse.data;

            this.currentPage = this.elements.config.currentPage;
            return this.elements;
        },
        fetchDataWithCsrf(url) {
            let csrf = {};
            csrf[this.csrfName] = this.csrfToken;

            return axios.post(this.url.getUrl(url), {...csrf, ...this.params});
        },
        async fetchFields(handle) {
            this.params.handle = handle;

            const searchFieldsInfoResponse = await this.fetchDataWithCsrf('super-filter/search-fields-info')
            this.searchFieldsInfo = searchFieldsInfoResponse.data;
        },
        async filterData(handle = null) {
            if (handle === null) {
                handle = this.handle;
            }

           this.prepareParams(handle);

            const response = await this.fetchDataWithCsrf('super-filter/fields')

            this.elements.items = response.data.items;
            this.elements.links = response.data.links;
        },

        async pushData(handle = null) {
            if (handle === null) {
                handle = this.handle;
            }

            this.prepareParams(handle);

            const response = await this.fetchDataWithCsrf('super-filter/fields')

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
        async getTemplate(handle, filename) {
            let csrf = {};
            csrf[this.csrfName] = this.csrfToken;

            let data = {...csrf, handle: handle, filename: filename};
            const response = await axios.post(this.url.getUrl('super-filter/template'), data);
            this.templates[filename] = response.data;

            return response.data;
        }
    }
})
