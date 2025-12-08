<template>
  <div class="sheet-card">
    <div class="sheet-header">
      <div class="sheet-title">{{ sheet.title }}</div>
      <div class="sheet-composer">{{ sheet.composer }}</div>
    </div>
    <div class="sheet-body">
      <div class="sheet-meta">
        <span class="meta-tag">{{ sheet.instrument }}</span>
        <span class="meta-tag">{{ sheet.genre }}</span>
        <span class="meta-tag">{{ sheet.difficulty }}</span>
        <span class="meta-tag">{{ sheet.pages }} pages</span>
      </div>
      <div class="sheet-description">
        <p>{{ sheet.description }}</p>
      </div>
      <div class="sheet-actions">
        <button class="action-btn view" @click="$emit('view-sheet')">
          <i class="fas fa-eye"></i> View
        </button>
        <button class="action-btn download" @click="$emit('download-sheet')">
          <i class="fas fa-download"></i> Download
        </button>
        <button class="action-btn add-to-event" @click="$emit('add-to-event')">
          <i class="fas fa-calendar-plus"></i> Add to Event
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SheetCard",
  props: {
    sheet: {
      type: Object,
      required: true,
    },
  },
  emits: ["view-sheet", "download-sheet", "add-to-event"],
};
</script>

<style scoped>
.sheet-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
  border: 1px solid #eaeaea;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.sheet-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.sheet-header {
  background: linear-gradient(to right, #5a7bb0, #4a6491);
  color: white;
  padding: 20px;
}

.sheet-title {
  font-size: 1.3rem;
  margin-bottom: 5px;
  font-weight: 600;
  line-height: 1.3;
}

.sheet-composer {
  font-size: 1rem;
  opacity: 0.9;
}

.sheet-body {
  padding: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.sheet-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 15px;
}

.meta-tag {
  background: #eef2f7;
  padding: 5px 12px;
  border-radius: 50px;
  font-size: 0.85rem;
  color: #4a6491;
  white-space: nowrap;
}

.sheet-description {
  margin-bottom: 20px;
  flex: 1;
}

.sheet-description p {
  line-height: 1.5;
  color: #555;
}

.sheet-actions {
  display: flex;
  justify-content: space-between;
  margin-top: auto;
}

.action-btn {
  background: #f0f4f8;
  border: none;
  padding: 8px 15px;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background 0.3s;
  font-size: 0.9rem;
}

.action-btn:hover {
  background: #e1e7f0;
}

.action-btn.download {
  color: #4a6491;
}

.action-btn.view {
  color: #2c3e50;
}

.action-btn.add-to-event {
  color: #27ae60;
}
</style>
