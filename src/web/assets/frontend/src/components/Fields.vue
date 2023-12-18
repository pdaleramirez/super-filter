<script>

import { storeToRefs} from 'pinia'
import { useEntriesStore } from "../stores/entries";
import VRuntimeTemplate from "vue3-runtime-template";
import SearchField from "./SearchField.vue";
import {inject} from "vue";
import useTemplate from "../composables/useTemplate";
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

      store.filterData(this.handle);
    },
  },
  components: {
    SearchField,
    VRuntimeTemplate
  },
  async mounted() {
    const handle = inject('handle');
    const store = useEntriesStore();

    const filename = 'fields';
    const template = useTemplate((handle) => store.getTemplate(handle, filename));
    this.template = await template.get(handle, filename);

    const { elements, searchFieldsInfo } = storeToRefs(store);

    if (elements.value.config !== undefined) {
      this.fields = elements.value.config.items.items;
    }

    if (searchFieldsInfo !== undefined) {
      this.searchFieldsInfo = searchFieldsInfo;
    }
  }
};

</script>

<template>
  <v-runtime-template :template="template" :template-props="fields" ></v-runtime-template>

</template>

<style scoped lang="scss">

</style>