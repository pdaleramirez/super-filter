<script setup>

import { useEntriesStore } from "../stores/entries";
import {storeToRefs} from "pinia";
import { ref,computed } from "vue";
import TreeComponent from "./TreeComponent.vue";
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

        <TreeComponent :handle="searchField.handle" :tree="searchField.options" />
<!--        <select v-model="searchField.value">-->
<!--          <option v-for="option in searchField.options" :value="option.id">{{ option.title }}</option>-->
<!--        </select>-->
      </template>

      {{  searchField.type }} {{ handle ?? '' }}
    </div>

</template>

<style scoped>
  input {
    border: 1px solid red;
  }
</style>