<script setup>

import { useEntriesStore } from "../stores/entries";
import {storeToRefs} from "pinia";
import { ref,computed } from "vue";
const props = defineProps({
  handle: { type: String },
})

//const fieldValue = ref('');

const store = useEntriesStore();

const { elements, templateFields, searchFieldsInfo, fieldValue } = storeToRefs(store);

const getFieldValue = (e) => {

  if (searchFieldsInfo.value) {
   // searchFieldsInfo.value[props.handle].value = searchField.value;
  }
}

const searchField = computed(() => {
  return searchFieldsInfo.value[props.handle];
});
</script>

<template>

    <div v-if="searchField">
      <input type="text" @input="getFieldValue" v-model="searchField.value" /> {{  searchField.value }} {{ handle ?? '' }}
    </div>

</template>

<style scoped>
  input {
    border: 1px solid red;
  }
</style>