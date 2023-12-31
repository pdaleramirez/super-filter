<ul v-if="SearchField.handle">
  <li v-for="option in options" :value="option.value">
    <input type="checkbox" :value="option.value" v-model="SearchField.value" /> {{ option.label }}
  </li>
</ul>