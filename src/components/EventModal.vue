<template>
  <div v-if="show" class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h2>{{ isEditing ? "Edit Event" : "Create New Event" }}</h2>
        <button class="close-btn" @click="closeModal">&times;</button>
      </div>

      <form @submit.prevent="submitForm" class="event-form">
        <div class="form-group">
          <label for="name">Event Name *</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            placeholder="Enter event name"
          />
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea
            id="description"
            v-model="form.description"
            placeholder="Enter event description"
            rows="3"
          ></textarea>
        </div>

        <div class="form-group">
          <label for="event_date">Event Date & Time *</label>
          <input
            id="event_date"
            v-model="form.event_date"
            type="datetime-local"
            required
          />
        </div>

        <div class="form-group">
          <label for="location">Location</label>
          <input
            id="location"
            v-model="form.location"
            type="text"
            placeholder="Enter event location"
          />
        </div>

        <div class="form-actions">
          <button type="button" class="cancel-btn" @click="closeModal">
            Cancel
          </button>
          <button type="submit" class="submit-btn" :disabled="loading">
            {{
              loading
                ? "Saving..."
                : isEditing
                ? "Update Event"
                : "Create Event"
            }}
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
  name: "EventModal",
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    event: {
      type: Object,
      default: null,
    },
  },
  emits: ["close", "saved"],
  setup(props, { emit }) {
    const loading = ref(false);
    const isEditing = ref(false);

    const form = ref({
      name: "",
      description: "",
      event_date: "",
      location: "",
    });

    const resetForm = () => {
      form.value = {
        name: "",
        description: "",
        event_date: "",
        location: "",
      };
    };

    const closeModal = () => {
      resetForm();
      emit("close");
    };

    const submitForm = async () => {
      loading.value = true;
      try {
        if (isEditing.value) {
          await api.updateEvent(props.event.id, form.value);
        } else {
          await api.createEvent(form.value);
        }
        emit("saved");
        closeModal();
      } catch (error) {
        console.error("Error saving event:", error);
        let errorMessage = "Error saving event. Please try again.";

        if (error.response) {
          if (error.response.status === 401) {
            errorMessage = "You must be logged in to save events.";
          } else if (
            error.response.status === 422 &&
            error.response.data.errors
          ) {
            // Validation errors
            const errors = Object.values(error.response.data.errors).flat();
            errorMessage = errors.join("\n");
          } else if (error.response.data.message) {
            errorMessage = error.response.data.message;
          }
        }

        alert(errorMessage);
      } finally {
        loading.value = false;
      }
    };

    watch(
      () => props.event,
      (newEvent) => {
        if (newEvent) {
          isEditing.value = true;
          form.value = {
            name: newEvent.name || "",
            description: newEvent.description || "",
            event_date: newEvent.event_date
              ? new Date(newEvent.event_date).toISOString().slice(0, 16)
              : "",
            location: newEvent.location || "",
          };
        } else {
          isEditing.value = false;
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
  color: #2c3e50;
}

.event-form {
  padding: 25px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #4a6491;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 12px;
  border: 2px solid #e1e7f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-group input:focus,
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
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.cancel-btn {
  background: #e74c3c;
  color: white;
}

.cancel-btn:hover {
  background: #c0392b;
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
