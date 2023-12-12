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

const searchField = computed(() => {
  return searchFieldsInfo.value[props.handle];
});
</script>

<template>

    <div v-if="searchField">
      <template v-if="searchField.type === 'PlainText'">
        <input type="text" v-model="searchField.value" />
      </template>
      <template v-if="searchField.type === 'Categories'">
        <select v-model="searchField.value">
          <option v-for="option in searchField.options" :value="option.value">{{ option.label }}</option>
        </select>
      </template>

      {{  searchField.type }} {{ handle ?? '' }}
    </div>

</template>

<style scoped>
  input {
    border: 1px solid red;
  }
</style>