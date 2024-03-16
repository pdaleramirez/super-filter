<script>

import {storeToRefs} from 'pinia'
import {useEntriesStore} from "../stores/entries";
import VRuntimeTemplate from "vue3-runtime-template";
import SearchField from "./SearchField.vue";
import {inject, watch} from "vue";
import useTemplate from "../composables/useTemplate";
import useFilter from "../composables/useFilter";

export default {
  data: () => ({
    elements: {},
    fields: {},
    searchFieldsInfo: {},
    template: '',
    handle: ''
  }),
  methods: {
    handleSubmitFilter() {
      const store = useEntriesStore();
      store.params.config.currentPage = 1;

      store.filterData(this.handle);
    },
    handleClearFilter() {
      const store = useEntriesStore();

      store.params.config.currentPage = 1;

      let fields = store.searchFieldsInfo;


      for (let key in fields) {


        if (fields[key].value !== undefined && fields[key].value !== null && fields[key].type === 'Categories') {

          for (let option of fields[key].options) {
            option.selected = false;
          }
        } else {
          if (fields[key].value !== undefined && fields[key].value !== null && typeof fields[key].value === 'string' ) {
            store.searchFieldsInfo[key].value = "";
          } else if (fields[key].value !== undefined && fields[key].value !== null && typeof fields[key].value === 'number') {
            store.searchFieldsInfo[key].value = 0;
          } else if (fields[key].value !== undefined && fields[key].value !== null && typeof fields[key].value === 'boolean') {
            store.searchFieldsInfo[key].value = false;
          } else if (fields[key].value !== undefined && fields[key].value !== null && Array.isArray(fields[key].value)) {
            store.searchFieldsInfo[key].value = [];
          } else if (fields[key].value !== undefined && fields[key].value !== null && typeof fields[key].value === 'object') {
            store.searchFieldsInfo[key].value = {};
          }
        }
      }

      store.params.config.params.fields = {};
      console.log(store.params.config.params.fields);
      store.filterData(this.handle);
      store.params.config.params.fields = {};
    },
  },
  components: {
    SearchField,
    VRuntimeTemplate
  },
  async mounted() {
    const handle = inject('handle');
    const fieldWatchAttribute = inject('fieldWatch');

    const store = useEntriesStore();
    this.handle = handle;
    const filename = 'fields';
    const template = useTemplate((handle) => store.getTemplate(handle, filename));
    this.template = await template.get(handle, filename);

    const {elements, searchFieldsInfo} = storeToRefs(store);

    const fieldWatch = (fieldWatchAttribute === true || fieldWatchAttribute === 'true') || (elements.value.config !== undefined && elements.value.config.options.fieldWatch === '1');

    if (elements.value.config !== undefined) {
      this.fields = elements.value.config.items.items;
    }

    if (searchFieldsInfo !== undefined) {
      this.searchFieldsInfo = searchFieldsInfo;
    }

    if (fieldWatch === true && elements.value.config.params) {
      elements.value.config.params.currentPage = 1;
      if (Object.keys(searchFieldsInfo.value).length > 0) {
        for (let searchField of Object.values(searchFieldsInfo.value)) {
          watch(() => searchField.value, (newValue, oldValue) => {
            const { get } = useFilter((handle) => store.filterData(handle));
            get(store.handle)
          }, {deep: true});
        }
      }
    }

    if (elements.value.config && elements.value.config.params) {
      watch(() => elements.value.config.params.sort, (newValue, oldValue) => {
        store.params.config.currentPage = 1;
        const { get } = useFilter((handle) => store.filterData(handle));
        get(store.handle)
      });
    }
  }
};

</script>

<template>
  <v-runtime-template :template="template" :template-props="fields"></v-runtime-template>

</template>

<style scoped lang="scss">

</style>