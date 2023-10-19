<template>
  <div>

  </div>
</template>

<script setup>
import VRuntimeTemplate from "vue3-runtime-template";
import AppMessage from "./AppMessage.vue";
import {ref} from "vue";

const name  = ref('Mellow');
const title  = ref('Lorax');
const template = ref("<h1>{{ title }}bab {{ name }}</h1>");

const templateProps = {
  title, name
}
// export default {
//   data: () => ({
//     name: "Mellow",
//     template: `
//       <h1>Hello {{ title }}!</h1>
//     `
//   }),
//   components: {
//     AppMessage,
//     VRuntimeTemplate
//   }
// };
</script>