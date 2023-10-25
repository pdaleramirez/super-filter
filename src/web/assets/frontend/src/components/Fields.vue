<script>


import {mapActions, storeToRefs} from 'pinia'
import { useEntriesStore } from "../stores/entries";
import Field from "./Field.vue";
import VRuntimeTemplate from "vue3-runtime-template";
import template from "../composables/template";
import {inject} from "vue";
export default {
  data: () => ({
    name: "Mellow",
    elements: {},
    fields: {},
    template: ''
  }),
  components: {
    Field,
    VRuntimeTemplate
  },
  mounted() {
    const store = useEntriesStore();

    const { elements, templateFields } = storeToRefs(store);

    if (elements.value.config !== undefined) {
     // console.log(elements.value.config.items.items)
      this.fields = elements.value.config.items.items;
    }

    ///this.fields = elements.config.items.items;
    this.template = templateFields;
  }
};

</script>

<template>
  <div class="border-2 border-green-500">

  <v-runtime-template :template="template" :template-props="fields" ></v-runtime-template>
  </div>

</template>

<style scoped lang="scss">

</style>