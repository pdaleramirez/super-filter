<ul v-if="SearchField.handle" class="space-y-1">
<li v-for="option in options" :value="option.value" class="flex items-center space-x-2">
  <input type="radio"
         :value="option.value"
         v-model="SearchField.value"
         class="form-radio text-indigo-600 h-4 w-4" />
  <span class="text-gray-700">{{ option.label }}</span>
</li>
</ul>