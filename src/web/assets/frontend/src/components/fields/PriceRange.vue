<script>
import useField from "../../composables/useField";
import {useEntriesStore} from "../../stores/entries";
import useTemplate from "../../composables/useTemplate";
import {inject, ref, watch} from "vue";
import VRuntimeTemplate from "vue3-runtime-template";

export default {
  data: () => ({
    handle: "",
    template: '',
    elements: {},
    SearchField: {},
    store: {},
    range: {
      min: null,
      max: null
    }
  }),
  props: {
    fieldHandle: {
      type: String,
      default: ''
    }
  },
  components: {
    VRuntimeTemplate
  },
  async mounted() {

    const appHandle = inject('handle');
    const store = useEntriesStore();
    const filename = 'fields/pricerange';
    const template = useTemplate((appHandle, filename) => store.getTemplate(appHandle, filename));

    this.template = await template.get(appHandle, filename);

    const {SearchField} = useField(this.fieldHandle);

    this.SearchField = SearchField;

    watch(this.range, (newValue, oldValue) => {
      console.log('range changed');


      SearchField.value.value.min = newValue.min;
      SearchField.value.value.max = newValue.max;

    });
  }
};
</script>

<template>
  <v-runtime-template :template="template"></v-runtime-template>
</template>

<style>
input {
  border: 1px solid red;
}
</style>