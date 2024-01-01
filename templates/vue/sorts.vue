<div v-if="elements.config && elements.config.items" class="flex flex-col space-y-1">
<label for="sort" class="text-sm font-medium text-gray-700">Sort:</label>
  <select v-model="elements.config.params.sort" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
    <template v-for="sort in elements.config.items.sorts">
      <option :value="sort.orderBy + '-asc'">{{ sort.name }} Ascending</option>
      <option :value="sort.orderBy + '-desc'">{{ sort.name }} Descending</option>
    </template>
  </select>
</div>