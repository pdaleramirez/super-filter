<script>
import useField from "../../composables/useField";
import {useEntriesStore} from "../../stores/entries";
import useTemplate from "../../composables/useTemplate";
import {inject, ref} from "vue";
import VRuntimeTemplate from "vue3-runtime-template";

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
    VRuntimeTemplate
  },
  async mounted() {

    const appHandle = inject('handle');
    const store = useEntriesStore();
    const filename = 'fields/dropdown';
    const templateReq = useTemplate((appHandle, filename) => store.getTemplate(appHandle, filename));

    this.template = await templateReq.get(appHandle, filename);

    const {SearchField} = useField(this.fieldHandle);

    this.SearchField = SearchField;
  }
};
</script>

<template>
  <!-- Create a dropdown selection from options props -->
  <select v-model="SearchField.value">
    <option v-for="option in options" :value="option.value">{{ option.label }}</option>
  </select>
</template>

<style>
input {
  border: 1px solid red;
}
</style>