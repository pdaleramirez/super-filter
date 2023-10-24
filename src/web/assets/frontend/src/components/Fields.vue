<script>


import {mapActions, storeToRefs} from 'pinia'
import { useEntriesStore } from "../stores/entries";
import Field from "./Field.vue";
import VRuntimeTemplate from "vue3-runtime-template";
import template from "../composables/template";
export default {
  data: () => ({
    name: "Mellow",
    elements: {},
    fields: {},
    template: ''
  }),
  methods: {
    ...mapActions(useEntriesStore,['fetchData', 'getFieldsTemplate'])
  },
  components: {
    Field,
    VRuntimeTemplate
  },
  mounted() {
    const store = useEntriesStore();
    const fieldReq = template((handle) => store.getFieldRequest(handle));
    fieldReq.get('superFilterShows');
    const {elements, fields} = storeToRefs(store);
    this.elements = elements;
    this.fields = fields;



  }
};

</script>

<template>
  <div class="border-2 border-green-500">
  <h1>Fields</h1>
title: {{ fields.title }}
    <ul v-if="fields">
      <li v-for="item in fields" :key="item.id">
        {{ item.id }}
      </li>
    </ul>

<!--  <v-runtime-template :template="template"></v-runtime-template>-->
  </div>

</template>

<style scoped lang="scss">

</style>