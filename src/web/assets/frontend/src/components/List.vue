<script>
import {useEntriesStore} from '../stores/entries';
import {storeToRefs} from "pinia";
import VRuntimeTemplate from "vue3-runtime-template";
import {inject} from "vue";
import useTemplate from "../composables/useTemplate";
import moment from "moment";
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
    },
    moment(date) {
      return moment(date);
    },
  },
  components: {
    VRuntimeTemplate
  },
  async mounted() {

    const handle = inject('handle')
    const store = useEntriesStore();
    const filename = 'items';
    const template = useTemplate((handle) => store.getTemplate(handle, filename));

    this.template = await template.get(handle, filename);

    const { elements } = storeToRefs(store);
    this.elements = elements;
  }
};


</script>

<template>
  <v-runtime-template :template="template"></v-runtime-template>

</template>

<style scoped lang="scss">

</style>