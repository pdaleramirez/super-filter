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
    templateFields: '',
  }),
  methods: {
    ...mapActions(useEntriesStore,['fetchData', 'getTestRequest'])
  },
  components: {
    SearchFields,
    Fields,
    List,
    AppMessage,
    VRuntimeTemplate
  },
  async mounted() {
   // this.fetchData().then((response) => {
   //   this.elements = response;
   // })

   // this.getTestRequest().then((response) => {
   //   this.template = response;
   // })
    const handle = inject('handle')
    const store = useEntriesStore();
    const templateReq = template((handle) => store.getTemplate(handle, 'main'));

    templateReq.get(handle);

    const { elements, templateContent } = storeToRefs(store);
    this.elements = elements;
    this.template = templateContent;

  }
};

</script>

<template>

  <v-runtime-template :template="template"></v-runtime-template>
  <v-runtime-template :template="templateFields"></v-runtime-template>


</template>

<style scoped lang="scss">

</style>