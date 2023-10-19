<script>
import VRuntimeTemplate from "vue3-runtime-template";
import AppMessage from "./AppMessage.vue";
export default {
  data: () => ({
    name: "Mellow",
    template: `
      <app-message>Hello {{ name }}!</app-message>
    `
  }),
  components: {
    AppMessage,
    VRuntimeTemplate
  }
};
</script>

<template>
  <div>
    <v-runtime-template :template="template"></v-runtime-template>
  </div>
</template>

<style scoped lang="scss">

</style>