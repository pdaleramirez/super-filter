<div v-if="searchFieldsInfo">
  <div class="m-4 flex flex-col space-y-1" v-for="field in searchFieldsInfo" >
    <label class="text-sm font-medium text-gray-700" for="name">{{ field.name }}</label>
    <SearchField :handle="field.handle" />
  </div>

  <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" id="filterButton" @click.prevent="handleSubmitFilter" >Filter</button>
  <button class="ml-5 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" id="filterButton" @click.prevent="handleClearFilter" >Clear</button>
</div>