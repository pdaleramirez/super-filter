<script setup>

import { useEntriesStore } from "../stores/entries";
import {storeToRefs} from "pinia";
import { ref,computed, watch } from "vue";
import TreeComponent from "./TreeComponent.vue";
const props = defineProps({
  handle: { type: String },
})

//const fieldValue = ref('');

const store = useEntriesStore();

const { templateFields, searchFieldsInfo, fieldValue } = storeToRefs(store);

const searchField = computed(() => {
  return searchFieldsInfo.value[props.handle];
});

watch(searchField.value, (newValue, oldValue) => {

  console.log(newValue.value.length)
  if (newValue.value.length > 0) {
    store.filterData(store.handle);
  }
});


</script>

<template>

    <div v-if="searchField">
      <template v-if="searchField.type === 'PlainText'">
        <input type="text" v-model="searchField.value" />
      </template>
      <template v-if="searchField.type === 'Categories'">

        <TreeComponent :handle="searchField.handle" :tree="searchField.options" />

      </template>

    </div>

</template>

<style scoped>
  input {
    border: 1px solid red;
  }
</style>