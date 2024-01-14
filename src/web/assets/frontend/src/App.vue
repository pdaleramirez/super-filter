<template>

  <main v-if="isDataLoaded && store">

    <SearchWrapper />

    <InfiniteLoading v-if="infiniteScroll === true" @infinite="load"/>
  </main>

</template>

<script setup>

import SearchWrapper from "./views/SearchWrapper.vue";
import {useEntriesStore} from "./stores/entries";
import { inject, ref, onMounted, onBeforeMount} from "vue";
import {storeToRefs} from "pinia";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";

const handle = inject('handle');
const store = useEntriesStore();
const infiniteScroll = ref(false);
const infiniteScrollAttribute = inject('infiniteScroll');

const isDataLoaded = ref(false);

onBeforeMount(async () => {

  await store.fetchData(handle);
  store.handle = handle;

  const {elements} = storeToRefs(store);
  infiniteScroll.value = (infiniteScrollAttribute === true || infiniteScrollAttribute === 'true') ||
      (elements.value.config !== undefined && elements.value.config.options.infiniteScroll === '1');

  store.isInfiniteScroll = infiniteScroll.value;

  isDataLoaded.value = true;
});

let page = 1;
const load = async function ($state) {
  try {

    store.params.config.currentPage = page;
    if (page !== 1) {
      await store.pushData(handle);

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
