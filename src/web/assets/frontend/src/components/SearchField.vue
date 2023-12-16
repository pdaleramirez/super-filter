<script setup>

import { useEntriesStore } from "../stores/entries";
import {storeToRefs} from "pinia";
import { computed, watch } from "vue";
import TreeComponent from "./TreeComponent.vue";
import PlainText from "./fields/PlainText.vue";
import useFilter from "../composables/useFilter";
const props = defineProps({
  handle: { type: String },
})

//const fieldValue = ref('');

const store = useEntriesStore();

const { templateFields, searchFieldsInfo, fieldValue } = storeToRefs(store);

const searchField = computed(() => {
  return searchFieldsInfo.value[props.handle];
});

watch(searchField.value, (newValue) => {
console.log('sss')
   const { get } = useFilter((handle) => store.filterData(handle));
   if (newValue.value.length > 0) {
     get(store.handle)
   }
});


</script>

<template>

    <div v-if="searchField">
      <template v-if="searchField.type === 'PlainText'">

        <PlainText :fieldHandle="searchField.handle" />
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