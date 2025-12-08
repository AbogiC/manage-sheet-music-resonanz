<template>
  <div v-if="show" class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h2>{{ isEditing ? "Edit Category" : "Add New Category" }}</h2>
        <button class="close-btn" @click="closeModal">&times;</button>
      </div>

      <form @submit.prevent="submitForm" class="category-form">
        <div class="form-group">
          <label for="type">Type</label>
          <select id="type" v-model="form.type" :disabled="isEditing" required>
            <option value="">Select Type</option>
            <option value="instrument">Instrument</option>
            <option value="genre">Genre</option>
            <option value="difficulty">Difficulty</option>
          </select>
        </div>

        <div class="form-group">
          <label for="name">Name</label>
          <input
            id="name"
            type="text"
            v-model="form.name"
            required
            placeholder="Enter category name"
          />
        </div>

        <div class="form-group">
          <label for="description">Description (Optional)</label>
          <textarea
            id="description"
            v-model="form.description"
            placeholder="Enter description"
            rows="3"
          ></textarea>
        </div>

        <div class="form-group">
          <label for="order">Order</label>
          <input
            id="order"
            type="number"
            v-model.number="form.order"
            min="0"
            placeholder="0"
          />
        </div>

        <div class="form-actions">
          <button type="button" class="cancel-btn" @click="closeModal">
            Cancel
          </button>
          <button type="submit" class="submit-btn" :disabled="loading">
            {{ loading ? "Saving..." : isEditing ? "Update" : "Add" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, watch } from "vue";
import api from "../services/api";

export default {
  name: "CategoryModal",
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    category: {
      type: Object,
      default: null,
    },
  },
  emits: ["close", "saved"],
  setup(props, { emit }) {
    const loading = ref(false);
    const isEditing = ref(false);

    const form = ref({
      type: "",
      name: "",
      description: "",
      order: 0,
    });

    const resetForm = () => {
      form.value = {
        type: "",
        name: "",
        description: "",
        order: 0,
      };
      isEditing.value = false;
    };

    const closeModal = () => {
      resetForm();
      emit("close");
    };

    const submitForm = async () => {
      loading.value = true;
      try {
        if (isEditing.value) {
          await api.updateCategory(props.category.id, form.value);
        } else {
          await api.createCategory(form.value);
        }
        emit("saved");
        closeModal();
      } catch (error) {
        console.error("Error saving category:", error);
        alert("Error saving category. Please try again.");
      } finally {
        loading.value = false;
      }
    };

    watch(
      () => props.category,
      (newCategory) => {
        if (newCategory) {
          form.value = {
            type: newCategory.type,
            name: newCategory.name,
            description: newCategory.description || "",
            order: newCategory.order || 0,
          };
          isEditing.value = true;
        } else {
          resetForm();
        }
      }
    );

    watch(
      () => props.show,
      (newShow) => {
        if (!newShow) {
          resetForm();
        }
      }
    );

    return {
      loading,
      isEditing,
      form,
      closeModal,
      submitForm,
    };
  },
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 15px;
  padding: 0;
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 25px;
  border-bottom: 1px solid #e1e7f0;
}

.modal-header h2 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.5rem;
}

.close-btn {
  background: none;
  border: none;
  font-size: 2rem;
  cursor: pointer;
  color: #7f8c8d;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  color: #34495e;
}

.category-form {
  padding: 25px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
  color: #4a6491;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 12px;
  border: 2px solid #e1e7f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #4a6491;
}

.form-group textarea {
  resize: vertical;
  min-height: 80px;
}

.form-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  margin-top: 30px;
}

.cancel-btn,
.submit-btn {
  padding: 12px 25px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
}

.cancel-btn {
  background: #ecf0f1;
  color: #7f8c8d;
}

.cancel-btn:hover {
  background: #d5dbdb;
}

.submit-btn {
  background: #4a6491;
  color: white;
}

.submit-btn:hover:not(:disabled) {
  background: #3a5479;
}

.submit-btn:disabled {
  background: #bdc3c7;
  cursor: not-allowed;
}
</style>
