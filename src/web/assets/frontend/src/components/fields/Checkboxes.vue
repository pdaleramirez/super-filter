<script>
import useField from "../../composables/useField";
import {useEntriesStore} from "../../stores/entries";
import useTemplate from "../../composables/useTemplate";
import {inject, ref} from "vue";
import VRuntimeTemplate from "vue3-runtime-template";
import TreeComponent from "./TreeComponent.vue";

export default {
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
    options: {
      type: Array,
      default: []
    }
  },
  components: {
    TreeComponent,
    VRuntimeTemplate
  },
  async mounted() {

    const appHandle = inject('handle');
    const store = useEntriesStore();
    const filename = 'fields/checkboxes';
    const template = useTemplate((appHandle, filename) => store.getTemplate(appHandle, filename));

    this.template = await template.get(appHandle, filename);

    const {SearchField} = useField(this.fieldHandle);

    this.SearchField = SearchField;
  }
};
</script>

<template>
<!--  <v-runtime-template :template="template"></v-runtime-template>-->

  <ul v-if="SearchField.handle">
    <li v-for="option in options" :value="option.value">
      <input type="checkbox" :value="option.value" v-model="SearchField.value" /> {{ option.label }}
    </li>
  </ul>

</template>

<style>
input {
  border: 1px solid red;
}
</style>