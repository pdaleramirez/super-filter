<div class="flex-auto bg-white mb-10">
  <span v-if="loading">Loading...</span>
  <ul v-if="elements">
    <li v-for="item in elements.items" :key="item.id">
      {{ item.title }}
    </li>
  </ul>
</div>
