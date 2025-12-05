<template>
  <div v-if="sheets.length > 0" class="sheet-grid">
    <SheetCard
      v-for="sheet in sheets"
      :key="sheet.id"
      :sheet="sheet"
      @view-sheet="$emit('view-sheet', sheet)"
      @download-sheet="$emit('download-sheet', sheet)"
    />
  </div>
  <div v-else class="empty-state">
    <i class="fas fa-file-pdf"></i>
    <h3>No sheet music found</h3>
    <p>Try changing your filters or upload new sheet music</p>
  </div>
</template>

<script>
import SheetCard from "./SheetCard.vue";

export default {
  name: "SheetGrid",
  components: {
    SheetCard,
  },
  props: {
    sheets: {
      type: Array,
      required: true,
    },
  },
  emits: ["view-sheet", "download-sheet"],
};
</script>

<style scoped>
.sheet-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 25px;
}

.empty-state {
  text-align: center;
  padding: 50px 20px;
  color: #888;
}

.empty-state i {
  font-size: 3rem;
  margin-bottom: 15px;
  color: #c3cfe2;
}

.empty-state h3 {
  margin-bottom: 10px;
  font-size: 1.5rem;
}

.empty-state p {
  font-size: 1.1rem;
}

@media (max-width: 1024px) {
  .sheet-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }
}

@media (max-width: 768px) {
  .sheet-grid {
    grid-template-columns: 1fr;
  }
}
</style>
