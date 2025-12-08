<template>
  <div v-if="show" class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h2>Add Sheet Music to Event</h2>
        <button class="close-btn" @click="closeModal">&times;</button>
      </div>

      <div class="modal-body">
        <div v-if="sheet" class="sheet-info">
          <h3>{{ sheet.title }}</h3>
          <p>by {{ sheet.composer }}</p>
        </div>

        <form @submit.prevent="submitForm" class="event-form">
          <div class="form-group">
            <label for="event">Select Event *</label>
            <select
              id="event"
              v-model="selectedEventId"
              required
              :disabled="loadingEvents"
            >
              <option value="">Choose an event...</option>
              <option v-for="event in events" :key="event.id" :value="event.id">
                {{ event.name }} - {{ formatDate(event.event_date) }}
              </option>
            </select>
            <div v-if="loadingEvents" class="loading">Loading events...</div>
          </div>

          <div class="form-group">
            <label for="order">Order (optional)</label>
            <input
              id="order"
              v-model.number="form.order"
              type="number"
              min="0"
              placeholder="Set order in event setlist"
            />
          </div>

          <div class="form-group">
            <label for="notes">Notes (optional)</label>
            <textarea
              id="notes"
              v-model="form.notes"
              placeholder="Additional notes for this piece in the event"
              rows="3"
            ></textarea>
          </div>

          <div class="form-actions">
            <button type="button" class="cancel-btn" @click="closeModal">
              Cancel
            </button>
            <button
              type="submit"
              class="submit-btn"
              :disabled="loading || !selectedEventId"
            >
              {{ loading ? "Adding..." : "Add to Event" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, watch, onMounted } from "vue";
import api from "../services/api";

export default {
  name: "AddToEventModal",
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    sheet: {
      type: Object,
      default: null,
    },
  },
  emits: ["close", "added"],
  setup(props, { emit }) {
    const loading = ref(false);
    const loadingEvents = ref(false);
    const events = ref([]);
    const selectedEventId = ref("");

    const form = ref({
      order: 0,
      notes: "",
    });

    const resetForm = () => {
      selectedEventId.value = "";
      form.value = {
        order: 0,
        notes: "",
      };
    };

    const closeModal = () => {
      resetForm();
      emit("close");
    };

    const fetchEvents = async () => {
      loadingEvents.value = true;
      try {
        const response = await api.getEvents();
        events.value = response.data.data;
      } catch (error) {
        console.error("Error fetching events:", error);
        alert("Error loading events. Please try again.");
      } finally {
        loadingEvents.value = false;
      }
    };

    const submitForm = async () => {
      if (!selectedEventId.value) {
        alert("Please select an event.");
        return;
      }

      loading.value = true;
      try {
        await api.addSheetMusicToEvent(selectedEventId.value, {
          sheet_music_id: props.sheet.id,
          order: form.value.order,
          notes: form.value.notes,
        });
        emit("added");
        closeModal();
      } catch (error) {
        console.error("Error adding sheet music to event:", error);
        let errorMessage =
          "Error adding sheet music to event. Please try again.";

        if (error.response) {
          if (error.response.status === 422 && error.response.data.errors) {
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

    const formatDate = (dateString) => {
      const date = new Date(dateString);
      return date.toLocaleDateString();
    };

    watch(
      () => props.show,
      (newShow) => {
        if (newShow) {
          fetchEvents();
        } else {
          resetForm();
        }
      }
    );

    return {
      loading,
      loadingEvents,
      events,
      selectedEventId,
      form,
      closeModal,
      submitForm,
      formatDate,
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

.modal-body {
  padding: 25px;
}

.sheet-info {
  margin-bottom: 20px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
}

.sheet-info h3 {
  margin: 0 0 5px 0;
  color: #2c3e50;
}

.sheet-info p {
  margin: 0;
  color: #7f8c8d;
}

.event-form {
  margin-top: 20px;
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

.form-group select,
.form-group input,
.form-group textarea {
  width: 100%;
  padding: 12px;
  border: 2px solid #e1e7f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-group select:focus,
.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #4a6491;
}

.form-group textarea {
  resize: vertical;
  min-height: 80px;
}

.loading {
  margin-top: 5px;
  font-size: 0.9rem;
  color: #7f8c8d;
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
