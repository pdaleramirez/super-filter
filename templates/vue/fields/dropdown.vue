<div class="flex flex-col space-y-1">
  <select v-model="SearchField.value"
          name="genre"
          class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
    <option v-if="SearchField.selectionLabel !== ''" value="">{{ SearchField.selectionLabel ?? "Select a " + SearchField.name }}</option>
    <option v-for="option in options" :value="option.value">{{ option.label }}</option>
  </select>
  </div>
