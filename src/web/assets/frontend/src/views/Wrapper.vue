<script>

import { useEntriesStore } from "../stores/entries";
import SearchFields from "../components/SearchFields.vue";
import AppMessage from "../components/AppMessage.vue";
import VRuntimeTemplate from "vue3-runtime-template";
import List from "../components/List.vue";
import {inject} from "vue";
import Fields from "../components/Fields.vue";
import Paginate from "../components/Paginate.vue";
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
  }),
  methods: {
    onClickHandler(page) {
      const store = useEntriesStore();

      store.params.config.currentPage = page;
      store.filterData(this.handle)
    },
  },
  components: {
    SearchFields,
    Fields,
    List,
    Paginate,
    AppMessage,
    VRuntimeTemplate
  },
  async mounted() {

    const handle = inject('handle');
    this.handle = handle;

    const store = useEntriesStore();
    const templateReq = useTemplate((handle) => store.getTemplate(handle, 'main'));
    const templateField = useTemplate((handle) => store.getFieldTemplate(handle, 'fields'));

    templateReq.get(handle);
    templateField.get(handle);

    const { elements, templateContent } = storeToRefs(store);
    this.elements = elements;
    this.template = templateContent;

    this.filter = useFilter((params, method) => store.action(params, method));
  }
};

</script>

<template>

  <v-runtime-template :template="template"></v-runtime-template>

  <Paginate
      :maxPagesShown=1
      :show-breakpoint-buttons="false"
  />
<!--  <h1>Super filter static</h1>-->
<!--  <div class="grid grid-cols-2 border-blue-500">-->
<!--    <div>-->
<!--      <Fields/>-->

<!--    </div>-->
<!--    <div>-->
<!--      <List/>-->

<!--    </div>-->
<!--  </div>-->

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