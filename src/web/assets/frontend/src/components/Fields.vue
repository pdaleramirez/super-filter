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
      this.fields = elements.value.config.items.items;
    }

    this.template = templateFields;
  }
};

</script>

<template>
  <div class="border-2 border-green-500">

  <v-runtime-template :template="template" :template-props="fields" ></v-runtime-template>
    <h1>FIELDS Static!</h1>
    <div v-if="fields">
      {{ fields }}
    </div>

<!--    <div v-if="elements.config.items.items">-->
<!--      <li v-for="item in elements.config.items.items" :key="item.id">-->
<!--        {{ item.title }}-->
<!--      </li>-->
<!--    </div>-->
<!--    <div v-else>-->
<!--      <h1>Loading fields ....</h1>-->
<!--    </div>-->
  </div>

</template>

<style scoped lang="scss">

</style>