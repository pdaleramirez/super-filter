<template>

           <main>

             <Wrapper />

             <TestCompiler />
           </main>

</template>

<script setup>
// import {defineAsyncComponent} from "vue";
//
// const AsyncComponent = defineAsyncComponent(() => import('./components/List.vue'));
// export default {
//   // still needs to be registered
//   components: { AsyncComponent }
// }

import Wrapper from "./views/Wrapper.vue";
import List from "./components/List.vue";
import TestCompiler from "./components/TestCompiler.vue";
import { useEntriesStore } from "./stores/entries";
const store = useEntriesStore();
import { storeToRefs } from "pinia";
import {onBeforeMount} from "vue";
onBeforeMount(() => {
  setTimeout(() => {
    store.fetchData();

  }, 2000);
});

const { elements } = storeToRefs(store);
</script>

<style scoped>

</style>
