<template>
  <div class="stats-container">
    <div class="stat-item">
      <div class="stat-value">{{ totalSheets }}</div>
      <div class="stat-label">Total Sheets</div>
    </div>
    <div class="stat-item">
      <div class="stat-value">{{ filteredSheets }}</div>
      <div class="stat-label">Filtered Sheets</div>
    </div>
    <div class="stat-item">
      <div class="stat-value">{{ instruments }}</div>
      <div class="stat-label">Instruments</div>
    </div>
    <div class="stat-item">
      <div class="stat-value">{{ genres }}</div>
      <div class="stat-label">Genres</div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Stats",
  props: {
    totalSheets: {
      type: Number,
      required: true,
    },
    filteredSheets: {
      type: Number,
      required: true,
    },
    instruments: {
      type: Number,
      required: true,
    },
    genres: {
      type: Number,
      required: true,
    },
  },
};
</script>

<style scoped>
.stats-container {
  display: flex;
  justify-content: space-between;
  background: #f8fafc;
  padding: 15px;
  border-radius: 10px;
  margin-bottom: 25px;
}

.stat-item {
  text-align: center;
  flex: 1;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #4a6491;
}

.stat-label {
  font-size: 0.9rem;
  color: #666;
}

@media (max-width: 768px) {
  .stats-container {
    flex-direction: column;
    gap: 15px;
  }
}
</style>
