<template>

  <main>

    <Wrapper/>
    infinite scroll {{ infiniteScroll }}
    <InfiniteLoading v-if="infiniteScroll === true" @infinite="load"/>
  </main>

</template>

<script setup>

import Wrapper from "./views/Wrapper.vue";
import {useEntriesStore} from "./stores/entries";
import {onBeforeMount, inject, ref} from "vue";
import {storeToRefs} from "pinia";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";

const handle = inject('handle');
const store = useEntriesStore();
const infiniteScroll = ref(true);
onBeforeMount(async () => {

  await store.fetchData(handle);
  const infiniteScrollAttribute = inject('infiniteScroll');

  infiniteScroll.value = (infiniteScrollAttribute === true || infiniteScrollAttribute === 'true') || (elements.value.config !== undefined && elements.value.config.options.infiniteScroll === '1');
  store.handle = handle;


});

const {elements} = storeToRefs(store);

let page = 1;
const load = async function ($state) {
  try {

    store.params.config.currentPage = page;
    if (page !== 1) {
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
