<template>

  <main v-if="isDataLoaded && store">

    <SearchWrapper />

    <InfiniteLoading v-if="infiniteScroll === true" @infinite="load"/>
  </main>

</template>

<script setup>

import SearchWrapper from "./views/SearchWrapper.vue";
import {useEntriesStore} from "./stores/entries";
import { inject, ref, onBeforeMount} from "vue";
import {storeToRefs} from "pinia";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";

const handle = inject('handle');
const options = inject('options');
const store = useEntriesStore();
const infiniteScroll = ref(false);
const infiniteScrollAttribute = inject('infiniteScroll');

const isDataLoaded = ref(false);

onBeforeMount(async () => {

  let parseOptions  = JSON.parse(options);

  if (parseOptions.attributes !== undefined) {
    store.params.itemAttributes = parseOptions.attributes;
  }

  await store.fetchData(handle);
  store.handle = handle;

  const {elements} = storeToRefs(store);
  infiniteScroll.value = (infiniteScrollAttribute === true || infiniteScrollAttribute === 'true') ||
      (elements.value.config !== undefined && elements.value.config.options.infiniteScroll === '1');

  store.isInfiniteScroll = infiniteScroll.value;

  isDataLoaded.value = true;


  if (options !== undefined && parseOptions.filter !== undefined ) {

    if (parseOptions.filter !== undefined) {
      store.params.config.params.fields = parseOptions.filter;
      for (let [key, field] of Object.entries(parseOptions.filter)) {
        store.searchFieldsInfo[key].value = field;
      }
    }

  }
});
let parseOptions  = JSON.parse(options);
if ( options !== undefined ) {
  if (parseOptions.filter !== undefined) {
    store.params.config.params.fields = parseOptions.filter;
  }


}
const load = async function ($state) {
  try {

    if (store.params.config.currentPage !== 1) {
      await store.pushData(handle);

      store.elements.items.push(...store.records);
    }


    store.params.config.currentPage++;

  } catch (error) {
    $state.error();
  }
};
</script>

<style>

</style>
