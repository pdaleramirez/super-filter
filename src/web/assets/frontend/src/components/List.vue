<script setup>
import {useEntriesStore} from '../stores/entries';
import filter from "../composables/filter";
import {storeToRefs} from "pinia";

const store = useEntriesStore();
const {elements} = storeToRefs(store);

const params = {
  handle: 'superFilterShows',
  config: {
    currentPage: 1
  }
}

//const url = useUrl();
//const { data } = await axios.post( url.getUrl('super-filter/fields'), params);

//const elements = await url.fetchData();
//console.log(fetch);
const {get, loading, error} = filter((params, method) => store.action(params, method));

</script>

<template>
  <div class="w-full flex">
    <div class="flex-auto bg-white h-5 w-25 block h-48 w-1/3">--</div>
    <div class="flex-auto bg-white h-full">
      <h1>Entries</h1>

      <span v-if="loading">Loading...</span>
      <ul v-if="elements">
        <li v-for="item in elements.items" :key="item.id">
          {{ item.title }}
        </li>
      </ul>

      <button @click="get(params, 'next')">Next</button>
      -
      <button @click="get(params, 'back')">Back</button>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>