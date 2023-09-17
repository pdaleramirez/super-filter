<template>

           <main>

             <Wrapper />

           </main>

</template>

<script setup>
// import {defineAsyncComponent} from "vue";
//
// const AsyncComponent = defineAsyncComponent(() => import('./components/List.vue'));
// export default {
//   // still needs to be registered
//   components: { AsyncComponent }
// }

import Wrapper from "./views/Wrapper.vue";
import List from "./components/List.vue";
</script>

<style scoped>

</style>
