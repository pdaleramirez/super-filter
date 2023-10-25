<script>


import { mapActions } from 'pinia'
import { useEntriesStore } from "../stores/entries";
import SearchFields from "../components/SearchFields.vue";
import AppMessage from "../components/AppMessage.vue";
import VRuntimeTemplate from "vue3-runtime-template";
import List from "../components/List.vue";
import {inject} from "vue";
import Fields from "../components/Fields.vue";
import {storeToRefs} from "pinia";
import template from "../composables/template";
export default {
  data: () => ({
    name: "Mellow",
    elements: {},
    template: '',
  }),
  methods: {
   // ...mapActions(useEntriesStore,['fetchData', 'getTestRequest'])
  },
  components: {
    SearchFields,
    Fields,
    List,
    AppMessage,
    VRuntimeTemplate
  },
  async mounted() {

    const handle = inject('handle')
    const store = useEntriesStore();
    const templateReq = template((handle) => store.getTemplate(handle, 'main'));
    const templateField = template((handle) => store.getFieldTemplate(handle, 'fields'));

    templateReq.get(handle);
    templateField.get(handle);

    const { elements, templateContent } = storeToRefs(store);
    this.elements = elements;
    this.template = templateContent;

  }
};

</script>

<template>

  <v-runtime-template :template="template"></v-runtime-template>

</template>

<style scoped lang="scss">

</style>