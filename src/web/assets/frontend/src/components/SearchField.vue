<script setup>

import { useEntriesStore } from "../stores/entries";
import {storeToRefs} from "pinia";
import { computed, watch, toRaw, ref } from "vue";
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

// watch(searchFieldsInfo.value[props.handle].value, (newValue, oldValue) => {
//
//     console.log('watch it')
//     const { get } = useFilter((handle) => store.filterData(handle));
//     get(store.handle)
//     if (toRaw(newValue).type === 'PlainText') {
//
//       //get(store.handle)
//     }
// });


</script>

<template>

    <div v-if="searchField">
      <template v-if="searchField.type === 'PlainText'">

        <PlainText :fieldHandle="searchField.handle" />
      </template>
      <template v-if="searchField.type === 'Categories'">
        <TreeComponent :fieldHandle="searchField.handle" :tree="searchField.options" />

      </template>

    </div>

</template>

<style scoped>
  input {
    border: 1px solid red;
  }
</style>