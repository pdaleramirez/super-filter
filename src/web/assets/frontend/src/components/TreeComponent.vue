<template>
  <ul v-if="SearchField">
    <li v-for="node in tree" :key="node.id">
      <input type="checkbox" v-model="node.selected" />
      {{ node.title }}
      <tree-component :fieldHandle="SearchField.handle" v-if="node.children && node.children.length > 0" :tree="node.children" :level="node.level + 1" />
    </li>
  </ul>
</template>

<script setup>
import {computed, ref, watch} from 'vue';
import {useEntriesStore} from "../stores/entries";
import {storeToRefs} from "pinia";
import useField from "../composables/useField";
let tree = ref(props.tree);
let selected = ref(null);


const props = defineProps({
  tree: {
    type: Array,
    required: true
  },
  level: {
    type: Number,
    default: 0
  },
  fieldHandle: {
    type: String,
    default: ''
  }
});

const store = useEntriesStore();

 const { searchFieldsInfo } = storeToRefs(store);
//
// const searchField = computed(() => {
//   return searchFieldsInfo.value[props.fieldHandle];
// });

const {SearchField} = useField(props.fieldHandle)

tree.value.forEach(node => {
  watch(() => node.selected, (newValue, oldValue) => {
    if (newValue) {
      // If the node is selected, add its id to searchField.value
      SearchField.value.value.push(node.id);
    } else {
      // If the node is not selected, remove its id from searchField.value
      const index = SearchField.value.value.indexOf(node.id);
      if (index !== -1) {
        SearchField.value.value.splice(index, 1);
      }
    }
    console.log(SearchField.value.value)
  });
});
</script>

<style scoped>
  ul {
    list-style: none;
    padding-left: 1rem;
  }
</style>