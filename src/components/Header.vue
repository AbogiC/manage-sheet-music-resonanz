<template>
  <header class="app-header">
    <h1><i class="fas fa-music"></i> {{ appName }}</h1>
    <p class="app-description">{{ appDescription }}</p>
  </header>
</template>

<script>
export default {
  name: "Header",
  props: {
    appName: {
      type: String,
      required: true,
    },
    appDescription: {
      type: String,
      required: true,
    },
  },
};
</script>

<style scoped>
.app-header {
  background: linear-gradient(to right, #2c3e50, #4a6491);
  color: white;
  padding: 25px;
  border-radius: 15px;
  margin-bottom: 30px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

h1 {
  font-size: 2.5rem;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  gap: 15px;
}

.app-description {
  font-size: 1.1rem;
  opacity: 0.9;
  max-width: 800px;
  line-height: 1.6;
}
</style>
