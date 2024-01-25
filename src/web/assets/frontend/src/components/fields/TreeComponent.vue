<script>

import {inject, ref, watch} from 'vue';
import {useEntriesStore} from "../../stores/entries";
import useField from "../../composables/useField";
import VRuntimeTemplate from "vue3-runtime-template";
import useTemplate from "../../composables/useTemplate";

export default {
  name: 'TreeComponent',
  data: () => ({
    handle: "",
    template: '',
    elements: {},
    SearchField: {},
    store: {},
  }),
  props: {
    fieldHandle: {
      type: String,
      default: ''
    },
    tree: {
      type: Array,
      required: true
    },
    level: {
      type: Number,
      default: 0
    },
  },
  components: {
    'TreeComponent': () => import('./TreeComponent.vue'),
    VRuntimeTemplate,

  },
  async mounted() {

    const appHandle = inject('handle');
    const store = useEntriesStore();
    const filename = 'fields/categorycheckbox';
    const template = useTemplate((appHandle, filename) => store.getTemplate(appHandle, filename));

    this.template = await template.get(appHandle, filename);



  },
  created() {
    const {SearchField} = useField(this.fieldHandle);

    this.SearchField = SearchField;

    this.tree.forEach(node => {

      SearchField.value.value.some(num => num === node.id) ? node.selected = true : node.selected = false;

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
      });
    });
  }
};
</script>
<template>
  <ul v-if="SearchField.handle">
    <li v-for="node in tree" :key="node.id">
      <v-runtime-template :templateProps="{ node: node }" :template="template"></v-runtime-template>

      <TreeComponent :fieldHandle="SearchField.handle" v-if="node.children && node.children.length > 0"
                     :tree="node.children" :level="node.level + 1"/>
    </li>
  </ul>
</template>


<style scoped>
ul {
  list-style: none;
  padding-left: 1rem;
}
</style>