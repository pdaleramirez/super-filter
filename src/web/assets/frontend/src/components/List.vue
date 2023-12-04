<!--<script setup>-->
<!--import {useEntriesStore} from '../stores/entries';-->
<!--import filter from "../composables/filter";-->
<!--import {storeToRefs} from "pinia";-->

<!--const store = useEntriesStore();-->
<!--const {elements, searchFieldsInfo} = storeToRefs(store);-->

<!--const params = {-->
<!--  handle: 'superFilterShows',-->
<!--  config: {-->
<!--    currentPage: 1-->
<!--  }-->
<!--}-->

<!--const {get, loading, error} = filter((params, method) => store.action(params, method));-->

<!--</script>-->

<script>
import {useEntriesStore} from '../stores/entries';
import {storeToRefs} from "pinia";
import VRuntimeTemplate from "vue3-runtime-template";
import {inject} from "vue";
import template from "../composables/template";
import filter from "../composables/filter";

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
  mounted() {

    const handle = inject('handle')
    const store = useEntriesStore();
    const templateReq = template((handle) => store.getListTemplate(handle, 'list'));

    templateReq.get(handle);

    const {elements, templateList} = storeToRefs(store);
    this.elements = elements;
    this.template = templateList;

    this.params = {
      handle: handle,
      config: {
        currentPage: 1
      }
    }

    this.filter = filter((params, method) => store.action(params, method));
  }
};


</script>

<template>

  <v-runtime-template :template="template"></v-runtime-template>

  <!--  <div class="w-full flex border-blue-500">-->
  <!--    <div class="flex-auto bg-white h-5 w-25 block h-48 w-1/3">&#45;&#45;</div>-->
  <!--    <div class="flex-auto bg-white h-full">-->
  <!--      <h1>List Entries</h1>-->

  <!--      <span v-if="loading">Loading...</span>-->
  <!--      <ul v-if="elements">-->
  <!--        <li v-for="item in elements.items" :key="item.id">-->
  <!--          {{ item.title }}-->
  <!--        </li>-->
  <!--      </ul>-->

  <!--      <button @click="get(params, 'next')">Next</button>-->
  <!--      - -->
  <!--      <button @click="get(params, 'back')">Back</button>-->
  <!--    </div>-->
  <!--  </div>-->
</template>

<style scoped lang="scss">

</style>