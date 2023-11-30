<script>

import {mapActions, storeToRefs} from 'pinia'
import { useEntriesStore } from "../stores/entries";
import Field from "./Field.vue";
import VRuntimeTemplate from "vue3-runtime-template";
import template from "../composables/template";
import {inject} from "vue";
import SearchField from "./SearchField.vue";
export default {
  data: () => ({
    name: "Mellow",
    elements: {},
    fields: {},
    searchFieldsInfo: {},
    template: ''
  }),
  methods: {

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
  }


};


</script>

<template>
  <v-runtime-template :template="template" :template-props="fields" ></v-runtime-template>
<!--  <div class="border-2 border-green-500">-->
<!--    <h1>FIELDS Static!</h1>-->
<!--    <div v-if="searchFieldsInfo">-->
<!--      <div v-for="field in searchFieldsInfo" >-->
<!--        <SearchField :handle="field.handle" />-->


<!--        sfinfo: {{ field.value }}-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
</template>

<style scoped lang="scss">

</style>