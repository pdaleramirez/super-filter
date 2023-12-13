<template>
  <ul v-if="searchField">
    <li v-for="node in tree" :key="node.id">
      <input type="checkbox" v-model="node.selected" />
      {{ node.title }}
      <tree-component :handle="searchField.handle" v-if="node.children && node.children.length > 0" :tree="node.children" :level="node.level + 1" />
    </li>
  </ul>
</template>

<script setup>
import {computed, ref, watch} from 'vue';
import {useEntriesStore} from "../stores/entries";
import {storeToRefs} from "pinia";

const props = defineProps({
  tree: {
    type: Array,
    required: true
  },
  level: {
    type: Number,
    default: 0
  },
  handle: {
    type: String,
    default: ''
  }
});

let tree = ref(props.tree);
let selected = ref(null);

const store = useEntriesStore();

const { searchFieldsInfo } = storeToRefs(store);

const searchField = computed(() => {
  return searchFieldsInfo.value[props.handle];
});

// Watch the selected property of each node
tree.value.forEach(node => {
  watch(() => node.selected, (newValue, oldValue) => {
    console.log(`Node ${node.title} selected changed from ${oldValue} to ${newValue}`);

    if (newValue) {
      // If the node is selected, add its id to searchField.value
      searchField.value.value.push(node.id);
    } else {
      // If the node is not selected, remove its id from searchField.value
      const index = searchField.value.value.indexOf(node.id);
      if (index !== -1) {
        searchField.value.value.splice(index, 1);
      }
    }
  });
});
</script>

<style scoped>
  ul {
    list-style: none;
    padding-left: 1rem;
  }
</style>
```