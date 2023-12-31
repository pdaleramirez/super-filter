<div>
  <div v-if="searchFieldsInfo">
    <div class="m-4 flex flex-col space-y-1" v-for="field in searchFieldsInfo" >
      <label class="text-sm font-medium text-gray-700" for="name">{{ field.name }}</label>
      <SearchField :handle="field.handle" />
    </div>
  </div>
  <button class="btn btn-outline-primary" id="filterButton" @click="handleSubmitFilter" >Submit Test</button>
</div>