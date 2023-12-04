<script>

import { storeToRefs} from 'pinia'
import { useEntriesStore } from "../stores/entries";
import Field from "./Field.vue";
import VRuntimeTemplate from "vue3-runtime-template";
import SearchField from "./SearchField.vue";
import {inject} from "vue";
export default {
  data: () => ({
    name: "Mellow",
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
    Field,
    VRuntimeTemplate
  },
  mounted() {
    const store = useEntriesStore();

    const { elements, templateFields, searchFieldsInfo } = storeToRefs(store);

    if (elements.value.config !== undefined) {
      this.fields = elements.value.config.items.items;
    }

    if (searchFieldsInfo !== undefined) {
      this.searchFieldsInfo = searchFieldsInfo;
    }

    this.template = templateFields;

    this.handle = inject('handle');

    //console.log('handle: ' + this.handle);
  }
};

</script>

<template>
  <v-runtime-template :template="template" :template-props="fields" ></v-runtime-template>
<!--  <div class="border-2 border-green-500">-->
<!--    <h1>FIELDS Runtime!</h1>-->
<!--    <div v-if="searchFieldsInfo">-->
<!--      <div v-for="field in searchFieldsInfo" >-->
<!--        <SearchField :handle="field.handle" />-->
<!--      </div>-->
<!--    </div>-->
<!--    <button class="btn btn-outline-primary" id="filterButton" @click="handleSubmitFilter" >Submit Test</button>-->
<!--  </div>-->
</template>

<style scoped lang="scss">

</style>