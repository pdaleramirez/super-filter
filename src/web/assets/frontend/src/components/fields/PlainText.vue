<script>
import useField from "../../composables/useField";
import {useEntriesStore} from "../../stores/entries";
import useTemplate from "../../composables/useTemplate";
import {inject, ref} from "vue";
import VRuntimeTemplate from "vue3-runtime-template";
import useFilter from "../../composables/useFilter";

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
    }
  },
  components: {
    VRuntimeTemplate
  },
  async mounted() {

    const appHandle = inject('handle');
    const store = useEntriesStore();
    const filename = 'fields/plaintext';
    const templateReq = useTemplate((appHandle, filename) => store.getTemplate(appHandle, filename));

    this.template = await templateReq.get(appHandle, filename);

    const {SearchField} = useField(this.fieldHandle);
    this.SearchField = SearchField;

    this.store = store;
  },
  // Watch for Search.value changes
  watch: {
    SearchField: {
      handler: function (newValue, oldValue) {
        console.log('watch searcg')
        console.log(newValue)


        const {get} = useFilter((handle) => this.store.filterData(handle));
        get(this.store.handle)
      },
      deep: true
    }
  },
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