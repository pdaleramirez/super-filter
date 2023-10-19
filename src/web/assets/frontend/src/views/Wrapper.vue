<script>


import { mapActions } from 'pinia'
import {onBeforeMount} from "vue";
import { useEntriesStore } from "../stores/entries";

import { storeToRefs } from "pinia";
import SearchFields from "../components/SearchFields.vue";
import ShowRun from "../components/ShowRun.vue";
import AppMessage from "../components/AppMessage.vue";
import VRuntimeTemplate from "vue3-runtime-template";
import List from "../components/List.vue";

export default {
  data: () => ({
    name: "Mellow",
    elements: {},
    template: ''
  }),
  methods: {
    ...mapActions(useEntriesStore,['fetchData', 'getTestRequest'])
  },
  components: {
    SearchFields,
    List,
    AppMessage,
    VRuntimeTemplate
  },
  mounted() {
   this.fetchData().then((response) => {
     this.elements = response;
   })

   this.getTestRequest().then((response) => {
     this.template = response;
   })

  }
};

</script>

<template>

  <v-runtime-template :template="template"></v-runtime-template>


</template>

<style scoped lang="scss">

</style>