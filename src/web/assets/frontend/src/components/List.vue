<script setup>
import {useEntriesStore} from "@/stores/entries";
import {storeToRefs} from "pinia";

const store = useEntriesStore();
const { list } = storeToRefs(store);
</script>

<template>
<h1>This is the listz</h1>
  <ul>
    <li v-for="item in store.getEntries().items" :key="item.id">
      {{ item.title }}
    </li>
  </ul>
</template>

<style scoped lang="scss">

</style>