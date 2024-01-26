<div class="flex-auto bg-white mb-10">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4" v-if="elements">
    <div class="p-4 border rounded-lg shadow-sm bg-white" v-for="item in elements.items" :key="item.id">
      <div class="flex flex-col md:flex-row md:items-start">
        <div class="w-full md:w-1/2 text-right">
          <div v-if="item.superFilterAssets != null && item.superFilterAssets.length > 0">
            <template v-for="asset in item.superFilterAssets">
              <img :src="asset.url" class="w-full h-auto" />
            </template>
          </div>
          <div v-else>
            <img src="https://via.placeholder.com/182x268.png?text=Image+Placeholder" class="w-full h-auto" />
          </div>
        </div>
        <div class="w-full md:w-1/2 mt-4 md:mt-0 md:pl-4">
          <h2 class="text-lg font-semibold mb-2">{{ item.title }} ({{ item.superFilterReleaseDate.label }}) | {{ item.superFilterImdbRating.label }} star</h2>
          <dl>
            <dt class="font-medium">Genre:
              <template v-if="item.superFilterGenre.length" v-for="(element, index) in item.superFilterGenre">
                <span v-if="index != 0">, </span><span>{{ element.title }}</span>
              </template>
            </dt>
            <dt class="font-medium">Type: {{ item.superFilterShowTypes.label }}</dt>
          </dl>

          <div class="description mt-2 text-gray-700">
            {{ item.superFilterDescription }}
          </div>
          <p class="mt-2 text-sm text-gray-500"><strong>Tags:</strong>
            <template v-if="item.superFilterShowTags.length" v-for="(element, index) in item.superFilterShowTags">
              <span v-if="index != 0">, </span><span>{{ element.title }}</span>
            </template>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>