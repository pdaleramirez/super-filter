<script setup>

import List from "../components/List.vue";
import {onBeforeMount} from "vue";
import {useEntriesStore} from "../stores/entries";
const store = useEntriesStore();
import { storeToRefs } from "pinia";

onBeforeMount(() => {
  setTimeout(() => {
    store.fetchData();

  }, 2000);
});

const { elements } = storeToRefs(store);

</script>

<template>
  <div v-if="elements.items">
    <List />
  </div>
  <div v-else>
    <h1>Loading ....</h1>
  </div>
</template>

<style scoped lang="scss">

</style>