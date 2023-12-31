<select v-model="SearchField.value">
  <option v-if="SearchField.selectionLabel !== ''" value="">{{ SearchField.selectionLabel ?? "Select a " + SearchField.name }}</option>
  <option v-for="option in options" :value="option.value">{{ option.label }}</option>
</select>