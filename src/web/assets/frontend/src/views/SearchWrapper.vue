<script>

import { useEntriesStore } from "@/stores/entries";
import VRuntimeTemplate from "vue3-runtime-template";
import List from "../components/List.vue";
import {inject} from "vue";
import Fields from "../components/Fields.vue";
import Paginate from "../components/PaginateList.vue";
import Sorts from "../components/Sorts.vue";
import {storeToRefs} from "pinia";
import useFilter from "../composables/useFilter";
import useTemplate from "../composables/useTemplate";

export default {
  data: () => ({
    handle: "",
    elements: {},
    template: '',
    searchFieldsInfo: '',
    currentPage: 1,
    page: 1,
    store: null
  }),
  props: {
    isInfiniteScroll: {
      type: Boolean,
      default: false
    }
  },
  methods: {

  },
  components: {
    Fields,
    List,
    Sorts,
    Paginate,
    VRuntimeTemplate
  },
  async mounted() {
    const store = useEntriesStore();
    const handle = inject('handle');
    this.handle = handle;


    const filename = 'main';
    const template = useTemplate((handle, filename) => store.getTemplate(handle, filename));

    this.template = await template.get(handle, filename);

    const { elements } = storeToRefs(store);
    this.elements = elements;
  }
};

</script>

<template>

  <v-runtime-template :template="template"></v-runtime-template>

</template>

<style>

.pagination-container {
  display: flex;
  column-gap: 10px;
}
.paginate-buttons {
  height: 40px;
  width: 40px;
  border-radius: 20px;
  cursor: pointer;
  background-color: rgb(242, 242, 242);
  border: 1px solid rgb(217, 217, 217);
  color: black;
}
.paginate-buttons:hover {
  background-color: #d8d8d8;
}
.active-page {
  background-color: #3498db;
  border: 1px solid #3498db;
  color: white;
}
.active-page:hover {
  background-color: #2988c8;
}

</style>