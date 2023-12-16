<script>
import useField from "../../composables/useField";
import { useEntriesStore } from "../../stores/entries";
import useTemplate from "../../composables/useTemplate";
import { inject, ref } from "vue";
import { storeToRefs } from "pinia";
import SearchFields from "../SearchFields.vue";
import Fields from "../Fields.vue";
import List from "../List.vue";
import Paginate from "../Paginate.vue";
import AppMessage from "../AppMessage.vue";
import VRuntimeTemplate from "vue3-runtime-template";
import useFilter from "../../composables/useFilter";

export default {
  data: () => ({
    handle: "",
    template: '',
    elements: {},
    SearchField: {},
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
     const templateReq = useTemplate((appHandle, fieldHandle) => store.getSingleFieldTemplate(appHandle, fieldHandle, 'fields/plaintext'));
     await templateReq.get(appHandle, this.fieldHandle);

     const { templateSingleField } = storeToRefs(store);
     this.template = templateSingleField.value[this.fieldHandle];


    //
    //
     const { SearchField } = useField(this.fieldHandle);
      this.SearchField = SearchField;
    if (SearchField) {
      console.log('is.SearchField')
      console.log(SearchField.value.type)
    }

  }
};
//
// const props = defineProps({
//   handle: {
//     type: String,
//     default: ''
//   }
// });
//
// const template = ref('')
// const { SearchField } = useField(props.handle);
// const store = useEntriesStore();
//
// const templateReq = useTemplate((handle) => store.getTemplate(handle, 'fields/plaintext'));
// const handle = inject('handle');
// templateReq.get(handle)
//
// const { templateContent } = storeToRefs(store);
//
// this.template = templateContent;
</script>

<template>
<!--  <input type="text" v-model="SearchField.value" /> {{ SearchField.type }}-->

  <v-runtime-template :template="template"></v-runtime-template>
</template>

<style>
  input {
    border: 1px solid red;
  }
</style>