<script>
import {useEntriesStore} from '../stores/entries';
import {storeToRefs} from "pinia";
import VRuntimeTemplate from "vue3-runtime-template";
import {inject} from "vue";
import useFilter from "../composables/useFilter";
import useTemplate from "../composables/useTemplate";

export default {
  data: () => ({
    elements: {},
    fields: {},
    searchFieldsInfo: {},
    template: '',
    handle: '',
    loading: false,
    params: {},
    filter: () => {}
  }),
  methods: {
    get(params, handle) {
      this.filter.get(params, handle);
    }
  },
  components: {
    VRuntimeTemplate
  },
  async mounted() {

    const handle = inject('handle')
    const store = useEntriesStore();
    const filename = 'list';
    const template = useTemplate((handle) => store.getTemplate(handle, filename));

    this.template = await template.get(handle, filename);

    const { elements } = storeToRefs(store);
    this.elements = elements;

    this.params = {
      handle: handle,
      config: {
        currentPage: 1
      }
    }

    this.filter = useFilter((params, method) => store.action(params, method));
  }
};


</script>

<template>

  <v-runtime-template :template="template"></v-runtime-template>

</template>

<style scoped lang="scss">

</style>