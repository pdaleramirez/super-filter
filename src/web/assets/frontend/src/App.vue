<template>

  <main>

    <Wrapper/>
    <InfiniteLoading @infinite="load"/>
  </main>

</template>

<script setup>

import Wrapper from "./views/Wrapper.vue";
import {ref} from "vue";
import {useEntriesStore} from "./stores/entries";
import {onBeforeMount, inject} from "vue";
import {storeToRefs} from "pinia";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";

const store = useEntriesStore();

const handle = inject('handle');

store.handle = handle;
const text = ref('');

onBeforeMount(() => {
  //store.fetchData(handle);
});
let page = 1;
const load = async function ($state) {
  try {
    console.log($state)
    console.log("loading...")
    console.log(page)
    store.params.config.currentPage = page;
    if (page === 1) {

      await store.fetchData(handle);
    } else {

      await store.pushData(handle);

      //if (elements.items.value) {
      console.log('records')
      console.log(store.records)

      store.elements.items.push(...store.records);

    }


    page++;

  } catch (error) {
    $state.error();
  }
};
</script>

<style>

</style>
