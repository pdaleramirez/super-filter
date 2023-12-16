<template>

  <main>

    <Wrapper/>
    <div>

    </div>


  </main>



</template>

<script setup>

import Wrapper from "./views/Wrapper.vue";
import { ref } from "vue";
import { useEntriesStore } from "./stores/entries";
import { onBeforeMount, inject } from "vue";
import { storeToRefs } from "pinia";
const store = useEntriesStore();

const handle = inject('handle');
store.handle = handle;
const text = ref('');

const {elements, searchFieldsInfo} = storeToRefs(store);

onBeforeMount(() => {
  // setTimeout(() => {
  store.fetchData(handle);

  //}, 2000);
});
// const { submit } = filter((handle) => store.filterData(handle));
//
// let delayTimer;
// const subscription = store.$subscribe((mutation, state) => {
//
//   if (Object.entries(state.searchFieldsInfo).length > 0) {
//     submit(handle, mutation, state.searchFieldsInfo)
//   }
// });
</script>

<style>

</style>
