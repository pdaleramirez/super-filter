<div class="flex p-4">
  <div class="p-4">
    <Fields/>

  </div>
  <div class="p-4">
    <Sorts />

    <List/>

    <Paginate v-if="isInfiniteScroll === false"
        :maxPagesShown=1
        :show-breakpoint-buttons="false"
    />

  </div>
</div>