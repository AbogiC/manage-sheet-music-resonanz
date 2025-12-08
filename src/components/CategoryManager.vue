<template>
  <div class="category-manager">
    <div class="manager-header">
      <div class="header-left">
        <button
          class="back-btn"
          @click="$emit('close')"
          title="Back to Dashboard"
        >
          <i class="fas fa-arrow-left"></i>
        </button>
        <h2><i class="fas fa-tags"></i> Manage Categories</h2>
      </div>
      <button class="add-btn" @click="openAddModal">
        <i class="fas fa-plus"></i> Add Category
      </button>
    </div>

    <div v-if="loading" class="loading">
      <i class="fas fa-spinner fa-spin"></i> Loading categories...
    </div>

    <div v-else-if="categories.length === 0" class="empty-state">
      <i class="fas fa-tags"></i>
      <p>No categories found. Add your first category to get started.</p>
    </div>

    <div v-else class="categories-grid">
      <div
        v-for="(categoryGroup, type) in groupedCategories"
        :key="type"
        class="category-section"
      >
        <h3 class="section-title">
          {{ type.charAt(0).toUpperCase() + type.slice(1) }}s
          <span class="count">({{ categoryGroup.length }})</span>
        </h3>

        <div class="category-list">
          <div
            v-for="category in categoryGroup"
            :key="category.id"
            class="category-item"
          >
            <div class="category-info">
              <h4>{{ category.name }}</h4>
              <p v-if="category.description">{{ category.description }}</p>
              <span class="order-badge">Order: {{ category.order }}</span>
            </div>

            <div class="category-actions">
              <button
                class="edit-btn"
                @click="openEditModal(category)"
                title="Edit"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                class="delete-btn"
                @click="deleteCategory(category)"
                title="Delete"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <CategoryModal
      :show="showModal"
      :category="editingCategory"
      @close="closeModal"
      @saved="onCategorySaved"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import CategoryModal from "./CategoryModal.vue";
import api from "../services/api";

export default {
  name: "CategoryManager",
  components: {
    CategoryModal,
  },
  emits: ["close"],
  setup(props, { emit }) {
    const loading = ref(false);
    const categories = ref([]);
    const showModal = ref(false);
    const editingCategory = ref(null);

    const groupedCategories = computed(() => {
      const grouped = {};
      categories.value.forEach((category) => {
        if (!grouped[category.type]) {
          grouped[category.type] = [];
        }
        grouped[category.type].push(category);
      });
      return grouped;
    });

    const fetchCategories = async () => {
      loading.value = true;
      try {
        const response = await api.getCategories();
        categories.value = response.data;
      } catch (error) {
        console.error("Error fetching categories:", error);
        alert("Error loading categories. Please try again.");
      } finally {
        loading.value = false;
      }
    };

    const openAddModal = () => {
      editingCategory.value = null;
      showModal.value = true;
    };

    const openEditModal = (category) => {
      editingCategory.value = category;
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
      editingCategory.value = null;
    };

    const onCategorySaved = () => {
      fetchCategories();
    };

    const deleteCategory = async (category) => {
      if (!confirm(`Are you sure you want to delete "${category.name}"?`)) {
        return;
      }

      try {
        await api.deleteCategory(category.id);
        fetchCategories();
        alert("Category deleted successfully!");
      } catch (error) {
        console.error("Error deleting category:", error);
        alert("Error deleting category. Please try again.");
      }
    };

    onMounted(() => {
      fetchCategories();
    });

    return {
      loading,
      categories,
      showModal,
      editingCategory,
      groupedCategories,
      openAddModal,
      openEditModal,
      closeModal,
      onCategorySaved,
      deleteCategory,
    };
  },
};
</script>

<style scoped>
.category-manager {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.manager-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid #e1e7f0;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 15px;
}

.back-btn {
  background: #ecf0f1;
  color: #7f8c8d;
  border: none;
  padding: 10px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.back-btn:hover {
  background: #d5dbdb;
  color: #34495e;
}

.manager-header h2 {
  margin: 0;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 10px;
}

.add-btn {
  background: #4a6491;
  color: white;
  border: none;
  padding: 12px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background 0.3s;
}

.add-btn:hover {
  background: #3a5479;
}

.loading,
.empty-state {
  text-align: center;
  padding: 50px 20px;
  color: #7f8c8d;
}

.empty-state i {
  font-size: 3rem;
  margin-bottom: 20px;
  display: block;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 30px;
}

.category-section {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.section-title {
  margin: 0 0 20px 0;
  color: #2c3e50;
  font-size: 1.3rem;
  display: flex;
  align-items: center;
  gap: 10px;
}

.count {
  background: #ecf0f1;
  color: #7f8c8d;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: normal;
}

.category-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.category-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 10px;
  border: 1px solid #e9ecef;
  transition: all 0.3s;
}

.category-item:hover {
  background: #e9ecef;
  border-color: #dee2e6;
}

.category-info {
  flex: 1;
}

.category-info h4 {
  margin: 0 0 5px 0;
  color: #2c3e50;
  font-size: 1.1rem;
}

.category-info p {
  margin: 0 0 8px 0;
  color: #6c757d;
  font-size: 0.9rem;
}

.order-badge {
  background: #4a6491;
  color: white;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.category-actions {
  display: flex;
  gap: 10px;
}

.edit-btn,
.delete-btn {
  background: none;
  border: none;
  padding: 8px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s;
  width: 35px;
  height: 35px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.edit-btn {
  color: #4a6491;
}

.edit-btn:hover {
  background: #4a6491;
  color: white;
}

.delete-btn {
  color: #e74c3c;
}

.delete-btn:hover {
  background: #e74c3c;
  color: white;
}

@media (max-width: 768px) {
  .categories-grid {
    grid-template-columns: 1fr;
  }

  .manager-header {
    flex-direction: column;
    gap: 15px;
    align-items: stretch;
  }

  .category-item {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }

  .category-actions {
    justify-content: center;
  }
}
</style>
