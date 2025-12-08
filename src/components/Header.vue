<template>
  <header class="app-header">
    <div class="header-content">
      <div class="header-text">
        <h1><i class="fas fa-music"></i> {{ appName }}</h1>
        <p class="app-description">{{ appDescription }}</p>
      </div>
      <button class="logout-btn" @click="$emit('logout')">
        <i class="fas fa-sign-out-alt"></i> Logout
      </button>
    </div>
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
  emits: ["logout"],
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

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-text h1 {
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

.logout-btn {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.3);
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s;
}

.logout-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }

  .header-text h1 {
    font-size: 2rem;
  }
}
</style>
