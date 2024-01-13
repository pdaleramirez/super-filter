<script setup>
import {useEntriesStore} from "../stores/entries";
import template from "../composables/useTemplate";
import {storeToRefs} from "pinia";
import {usePaginateProps} from "../composables/usePaginateProps";

const store = useEntriesStore();

const {elements} = storeToRefs(store);

const onClickHandler = (page) => {
  const store = useEntriesStore();

  store.params.config.currentPage = page;
  store.filterData(store.handle);
}


defineProps(usePaginateProps());
</script>

<template>
  <div v-if="elements.config">
    <vue-awesome-paginate

        v-model="store.params.config.currentPage"
        :total-items="elements.links.total"
        :items-per-page="elements.config.options.perPage"
        :on-click="onClickHandler"
        :maxPagesShown="maxPagesShown"
        :showEndingButtons="false"
    />
  </div>
</template>

<style scoped lang="scss">

</style>