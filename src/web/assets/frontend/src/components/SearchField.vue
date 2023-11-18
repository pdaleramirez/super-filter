<script setup>
const props = defineProps({
  type: { type: String, required: true },
  handle: { type: String },
})
</script>

<template>
  <input type="text" /> Plain Text {{ type }}
</template>

<style scoped lang="scss">

</style>