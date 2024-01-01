<template v-if="SearchField.value">
  Range: <small>Min</small> <input type="number"
                            v-model="range.min"
                            size="2" />

  <small>Max</small> <input type="number"
                            v-model="range.max"
                            size="2" />
</template>
  <script setup>
  </script>