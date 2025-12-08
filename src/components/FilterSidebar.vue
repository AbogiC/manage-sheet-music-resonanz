<template>
  <div class="sidebar">
    <h2 class="section-title"><i class="fas fa-filter"></i> Filters</h2>

    <div class="filter-group">
      <div class="filter-title">Instrument</div>
      <div class="filter-options">
        <div
          v-for="instrument in instruments"
          :key="'inst-' + instrument"
          class="filter-tag"
          :class="{ active: activeFilters.instrument === instrument }"
          @click="$emit('toggle-filter', 'instrument', instrument)"
        >
          {{ instrument }}
        </div>
      </div>
    </div>

    <div class="filter-group">
      <div class="filter-title">Genre</div>
      <div class="filter-options">
        <div
          v-for="genre in genres"
          :key="'genre-' + genre"
          class="filter-tag"
          :class="{ active: activeFilters.genre === genre }"
          @click="$emit('toggle-filter', 'genre', genre)"
        >
          {{ genre }}
        </div>
      </div>
    </div>

    <div class="filter-group">
      <div class="filter-title">Difficulty</div>
      <div class="filter-options">
        <div
          v-for="difficulty in difficulties"
          :key="'diff-' + difficulty"
          class="filter-tag"
          :class="{ active: activeFilters.difficulty === difficulty }"
          @click="$emit('toggle-filter', 'difficulty', difficulty)"
        >
          {{ difficulty }}
        </div>
      </div>
    </div>

    <div class="upload-section">
      <h3><i class="fas fa-cloud-upload-alt"></i> Upload Sheet Music</h3>
      <p>Upload PDFs and add metadata for organization</p>
      <button class="upload-btn" @click="$emit('show-upload')">
        <i class="fas fa-plus"></i> Upload New Sheet
      </button>
    </div>

    <div class="upload-section">
      <h3><i class="fas fa-calendar-alt"></i> Manage Events</h3>
      <p>Create and manage events for your sheet music</p>
      <button class="upload-btn" @click="$emit('show-events')">
        <i class="fas fa-calendar-plus"></i> Manage Events
      </button>
    </div>

    <div class="filter-group" style="margin-top: 25px">
      <button class="upload-btn clear-btn" @click="$emit('clear-filters')">
        <i class="fas fa-times"></i> Clear All Filters
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "FilterSidebar",
  props: {
    instruments: {
      type: Array,
      required: true,
    },
    genres: {
      type: Array,
      required: true,
    },
    difficulties: {
      type: Array,
      required: true,
    },
    activeFilters: {
      type: Object,
      required: true,
    },
  },
  emits: ["toggle-filter", "clear-filters", "show-upload", "show-events"],
};
</script>

<style scoped>
.sidebar {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  height: fit-content;
}

.section-title {
  font-size: 1.3rem;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid #eaeaea;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 10px;
}

.filter-group {
  margin-bottom: 25px;
}

.filter-title {
  font-weight: 600;
  margin-bottom: 10px;
  color: #4a6491;
}

.filter-options {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.filter-tag {
  background: #eef2f7;
  padding: 8px 15px;
  border-radius: 50px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid #d1d9e6;
  font-size: 0.9rem;
  user-select: none;
}

.filter-tag:hover {
  background: #dfe6f0;
}

.filter-tag.active {
  background: #4a6491;
  color: white;
  border-color: #4a6491;
}

.upload-section {
  background: #f8fafc;
  padding: 20px;
  border-radius: 10px;
  border: 2px dashed #c3cfe2;
  margin-top: 25px;
  text-align: center;
}

.upload-btn {
  background: #4a6491;
  color: white;
  border: none;
  padding: 12px 25px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  margin-top: 10px;
  transition: background 0.3s;
  width: 100%;
  justify-content: center;
}

.upload-btn:hover {
  background: #3a5479;
}

.clear-btn {
  background: #e74c3c;
}

.clear-btn:hover {
  background: #c0392b;
}
</style>
