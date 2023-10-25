<template>

           <main>

             <Wrapper />

             <TestCompiler />
           </main>

</template>

<script setup>

import Wrapper from "./views/Wrapper.vue";

import TestCompiler from "./components/TestCompiler.vue";
import { useEntriesStore } from "./stores/entries";
const store = useEntriesStore();

import {onBeforeMount, inject} from "vue";

const handle = inject('handle');

onBeforeMount(() => {
  setTimeout(() => {
    store.fetchData();

  }, 2000);
});

</script>

<style scoped>

</style>
