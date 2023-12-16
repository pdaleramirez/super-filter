<script setup>
import { useEntriesStore } from '../stores/entries';
import {storeToRefs} from "pinia";
import {onBeforeMount, ref} from "vue";
import template from "../composables/useTemplate";
import VRuntimeTemplate from "vue3-runtime-template";
import AppMessage from "./AppMessage.vue";
import useTemplate from "../composables/useTemplate";

const props = defineProps(['handle']);
const store = useEntriesStore();
// const {get, loading, error } = template((handle) => store.getTemplate(handle));
const testReq = useTemplate((handle) => store.getTestRequest(handle));
const fieldReq = useTemplate((handle) => store.getFieldRequest(handle));

testReq.get(props.handle);
fieldReq.get(props.handle);

const { elements, templateVar, fields } = storeToRefs(store);

const name  = ref('Mellow');
const title  = ref('Lorax');

const templateContentTest = ref("{{ title }} imbax <AppMessage /> {{ name }}");

const templateProps = {
  title, name, fields
}


</script>

<template>

<!--  <div v-if="templateContentTest">-->

<!--   <v-runtime-template :template="templateContentTest" :template-props="templateProps"></v-runtime-template>-->
<!--  </div>-->

  <div v-if="elements.config">
    <div v-for="item in elements.config.items.items" :key="item.id">
      {{ item.name ?? item.title ?? item.id }}
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>