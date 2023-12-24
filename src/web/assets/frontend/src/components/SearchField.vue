<script setup>

import { useEntriesStore } from "../stores/entries";
import {storeToRefs} from "pinia";
import { computed, watch, toRaw, ref } from "vue";
import TreeComponent from "./fields/TreeComponent.vue";
import PlainText from "./fields/PlainText.vue";
import Dropdown from "./fields/Dropdown.vue";
import Checkboxes from "./fields/Checkboxes.vue";
const props = defineProps({
  handle: { type: String },
})

const store = useEntriesStore();

const { templateFields, searchFieldsInfo, fieldValue } = storeToRefs(store);

const searchField = computed(() => {
  return searchFieldsInfo.value[props.handle];
});

</script>

<template>

    <div v-if="searchField">
      <template v-if="searchField.type === 'PlainText'">

        <PlainText :fieldHandle="searchField.handle" />
      </template>
      <template v-if="searchField.type === 'Categories'">

        <template v-if="searchField.limit === 1">
          <Dropdown :fieldHandle="searchField.handle" :options="searchField.options" />
        </template>
        <template v-else>
          <TreeComponent :fieldHandle="searchField.handle" :tree="searchField.options" />
        </template>
      </template>
      <template v-if="searchField.type === 'Dropdown'">
        <Dropdown :fieldHandle="searchField.handle" :options="searchField.options" />
      </template>
      <template v-if="searchField.type === 'Tags'">
        <Checkboxes :fieldHandle="searchField.handle" :options="searchField.options" />
      </template>


    </div>

</template>

<style scoped>
  select {
    border: 1px solid red;
  }
</style>