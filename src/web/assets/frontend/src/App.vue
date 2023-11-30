<template>

  <main>

    <Wrapper/>
    <div v-if="searchFieldsInfo.title">
      tests <input class="border-blue-500" type="text" v-model="searchFieldsInfo.title.value"/> watch
      {{ searchFieldsInfo.title.value }}
    </div>
  </main>

</template>

<script setup>

import Wrapper from "./views/Wrapper.vue";
import {ref} from "vue";
import TestCompiler from "./components/TestCompiler.vue";
import {useEntriesStore} from "./stores/entries";

const store = useEntriesStore();

import {onBeforeMount, onMounted, inject, watch} from "vue";
import {storeToRefs} from "pinia";

const handle = inject('handle');
const text = ref('');
const {elements, searchFieldsInfo} = storeToRefs(store);

onBeforeMount(() => {
  // setTimeout(() => {
  store.fetchData(handle);

  //}, 2000);
});

let previousState = { ...store.$state }

const subscription = store.$subscribe((mutation, state) => {

  if (Object.entries(state.searchFieldsInfo).length > 0) {

    for (const [key, field] of Object.entries(state.searchFieldsInfo)) {
      if (mutation.events.target.handle === key) {
        console.log('old: ' + mutation.events.oldValue)
        console.log('new: ' + mutation.events.newValue)
       // store.fetchData(handle);
      }
    }

  }


});

</script>

<style scoped>

</style>
