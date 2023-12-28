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
    const filename = 'fields/plaintext';
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
  <template v-if="SearchField.value">
    <small>Min</small> <input type="number"
                              v-model="range.min"
                              size="2" />

    <small>Max</small> <input type="number"
                              v-model="range.max"
                              size="2" />
  </template>
</template>

<style>
input {
  border: 1px solid red;
}
</style>