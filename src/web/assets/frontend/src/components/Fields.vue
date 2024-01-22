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

    if (fieldWatch === true) {
      elements.value.params.config.currentPage = 1;
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