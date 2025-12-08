<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h2><i class="fas fa-cloud-upload-alt"></i> Upload Sheet Music</h2>
        <button class="close-btn" @click="$emit('close')">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="modal-body">
        <div
          class="upload-area"
          @dragover.prevent
          @drop.prevent="handleDrop"
          @click="$refs.fileInput.click()"
        >
          <input
            ref="fileInput"
            type="file"
            accept=".pdf"
            @change="handleFileSelect"
            style="display: none"
          />
          <i class="fas fa-file-pdf upload-icon"></i>
          <h3 v-if="!selectedFile">Drop PDF files here or click to browse</h3>
          <h3 v-else>{{ selectedFile.name }}</h3>
          <p v-if="!selectedFile">Supported format: PDF only</p>
          <p v-else>{{ formatFileSize(selectedFile.size) }}</p>
          <p class="file-size">Max file size: 50MB</p>
          <div v-if="uploadProgress > 0" class="progress-bar">
            <div
              class="progress-fill"
              :style="{ width: uploadProgress + '%' }"
            ></div>
            <span class="progress-text">{{ uploadProgress }}%</span>
          </div>
        </div>

        <div class="metadata-form" v-if="selectedFile">
          <h3>Metadata</h3>
          <div class="form-group">
            <label>Title *</label>
            <input
              type="text"
              placeholder="Enter sheet music title"
              v-model="form.title"
              :class="{ error: errors.title }"
            />
            <span v-if="errors.title" class="error-text">{{
              errors.title[0]
            }}</span>
          </div>
          <div class="form-group">
            <label>Composer *</label>
            <input
              type="text"
              placeholder="Enter composer name"
              v-model="form.composer"
              :class="{ error: errors.composer }"
            />
            <span v-if="errors.composer" class="error-text">{{
              errors.composer[0]
            }}</span>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Instrument *</label>
              <select
                v-model="form.instrument"
                :class="{ error: errors.instrument }"
                :disabled="loadingCategories"
              >
                <option value="">Select instrument</option>
                <option
                  v-for="instrument in categories.instrument"
                  :key="instrument.name"
                  :value="instrument.name"
                >
                  {{ instrument.name }}
                </option>
              </select>
              <span v-if="errors.instrument" class="error-text">{{
                errors.instrument[0]
              }}</span>
            </div>
            <div class="form-group">
              <label>Difficulty *</label>
              <select
                v-model="form.difficulty"
                :class="{ error: errors.difficulty }"
                :disabled="loadingCategories"
              >
                <option value="">Select difficulty</option>
                <option
                  v-for="difficulty in categories.difficulty"
                  :key="difficulty.name"
                  :value="difficulty.name"
                >
                  {{ difficulty.name }}
                </option>
              </select>
              <span v-if="errors.difficulty" class="error-text">{{
                errors.difficulty[0]
              }}</span>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Genre *</label>
              <select
                v-model="form.genre"
                :class="{ error: errors.genre }"
                :disabled="loadingCategories"
              >
                <option value="">Select genre</option>
                <option
                  v-for="genre in categories.genre"
                  :key="genre.name"
                  :value="genre.name"
                >
                  {{ genre.name }}
                </option>
              </select>
              <span v-if="errors.genre" class="error-text">{{
                errors.genre[0]
              }}</span>
            </div>
            <div class="form-group">
              <label>Pages</label>
              <input
                type="number"
                min="1"
                placeholder="Number of pages"
                v-model.number="form.pages"
              />
            </div>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea
              placeholder="Optional description"
              v-model="form.description"
              rows="3"
            ></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" @click="clearFile">
          <i class="fas fa-times"></i> Clear
        </button>
        <div class="footer-right">
          <button class="btn btn-cancel" @click="$emit('close')">Cancel</button>
          <button
            class="btn btn-primary"
            @click="uploadSheet"
            :disabled="!canUpload || isUploading"
          >
            <i v-if="isUploading" class="fas fa-spinner fa-spin"></i>
            {{ isUploading ? "Uploading..." : "Upload" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from "vue";
import api from "../services/api.js";

export default {
  name: "UploadModal",
  emits: ["close", "uploaded"],
  setup(props, { emit }) {
    const selectedFile = ref(null);
    const uploadProgress = ref(0);
    const isUploading = ref(false);
    const errors = ref({});
    const categories = ref({
      instrument: [
        { name: "Piano" },
        { name: "Guitar" },
        { name: "Violin" },
        { name: "Flute" },
        { name: "Cello" },
        { name: "Saxophone" },
      ],
      genre: [
        { name: "Classical" },
        { name: "Jazz" },
        { name: "Pop" },
        { name: "Rock" },
        { name: "Folk" },
        { name: "Other" },
      ],
      difficulty: [
        { name: "Beginner" },
        { name: "Intermediate" },
        { name: "Advanced" },
        { name: "Professional" },
      ],
    });
    const loadingCategories = ref(false);

    const form = ref({
      title: "",
      composer: "",
      instrument: "",
      difficulty: "",
      genre: "",
      pages: 1,
      description: "",
      is_public: true,
    });

    const canUpload = computed(() => {
      const result =
        selectedFile.value &&
        form.value.title &&
        form.value.composer &&
        form.value.instrument &&
        form.value.difficulty &&
        form.value.genre;
      console.log("canUpload check:", {
        hasFile: !!selectedFile.value,
        title: form.value.title,
        composer: form.value.composer,
        instrument: form.value.instrument,
        difficulty: form.value.difficulty,
        genre: form.value.genre,
        result: result,
      });
      return result;
    });

    const handleFileSelect = (event) => {
      const file = event.target.files[0];
      if (file) {
        validateAndSetFile(file);
      }
    };

    const handleDrop = (event) => {
      const file = event.dataTransfer.files[0];
      if (file) {
        validateAndSetFile(file);
      }
    };

    const validateAndSetFile = (file) => {
      // Check file type
      if (file.type !== "application/pdf") {
        alert("Please select a PDF file only.");
        return;
      }

      // Check file size (50MB limit)
      if (file.size > 50 * 1024 * 1024) {
        alert("File size must be less than 50MB.");
        return;
      }

      selectedFile.value = file;
      errors.value = {};

      // Auto-fill title from filename if empty
      if (!form.value.title) {
        form.value.title = file.name.replace(".pdf", "").replace(/_/g, " ");
      }
    };

    const clearFile = () => {
      selectedFile.value = null;
      uploadProgress.value = 0;
      errors.value = {};
    };

    const formatFileSize = (bytes) => {
      if (bytes === 0) return "0 Bytes";
      const k = 1024;
      const sizes = ["Bytes", "KB", "MB", "GB"];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
    };

    const fetchCategories = async () => {
      loadingCategories.value = true;
      try {
        console.log("Fetching categories...");
        const response = await api.getCategoriesGrouped();
        console.log("Categories response:", response.data);
        console.log("Response data type:", typeof response.data);
        console.log("Response data keys:", Object.keys(response.data));
        categories.value = response.data;
        console.log("Categories set to:", categories.value);
        console.log("Instrument categories:", categories.value.instrument);
        console.log("Genre categories:", categories.value.genre);
        console.log("Difficulty categories:", categories.value.difficulty);
      } catch (error) {
        console.error("Error fetching categories:", error);
        // Fallback to default values if API fails
        categories.value = {
          instrument: [
            { name: "Piano" },
            { name: "Guitar" },
            { name: "Violin" },
            { name: "Flute" },
            { name: "Cello" },
            { name: "Saxophone" },
          ],
          genre: [
            { name: "Classical" },
            { name: "Jazz" },
            { name: "Pop" },
            { name: "Rock" },
            { name: "Folk" },
            { name: "Other" },
          ],
          difficulty: [
            { name: "Beginner" },
            { name: "Intermediate" },
            { name: "Advanced" },
            { name: "Professional" },
          ],
        };
        console.log("Using fallback categories:", categories.value);
      } finally {
        loadingCategories.value = false;
      }
    };

    // Fetch categories when component is mounted
    fetchCategories();

    const uploadSheet = async () => {
      console.log("Upload button clicked");
      console.log("canUpload:", canUpload.value);
      console.log("isUploading:", isUploading.value);
      console.log("Form data:", form.value);
      console.log("Selected file:", selectedFile.value);
      console.log("Auth token:", localStorage.getItem("token"));

      if (!canUpload.value || isUploading.value) {
        console.log("Upload blocked - validation failed");
        return;
      }

      // Check if user is authenticated
      const token = localStorage.getItem("token");
      if (!token) {
        alert(
          "You must be logged in to upload sheet music. Please log in first."
        );
        return;
      }

      isUploading.value = true;
      uploadProgress.value = 0;
      errors.value = {};

      try {
        const formData = new FormData();
        formData.append("file", selectedFile.value);
        formData.append("title", form.value.title);
        formData.append("composer", form.value.composer);
        formData.append("instrument", form.value.instrument);
        formData.append("genre", form.value.genre);
        formData.append("difficulty", form.value.difficulty);
        formData.append("pages", form.value.pages.toString());
        formData.append("description", form.value.description);
        // For boolean fields in Laravel, send as 1/0
        formData.append("is_public", form.value.is_public ? "1" : "0");

        console.log("Making API call to upload sheet music...");
        console.log("FormData contents:");
        for (let [key, value] of formData.entries()) {
          console.log(key + ":", value);
        }

        const response = await api.createSheetMusic(formData, {
          onUploadProgress: (progressEvent) => {
            if (progressEvent.total) {
              const percentCompleted = Math.round(
                (progressEvent.loaded * 100) / progressEvent.total
              );
              uploadProgress.value = percentCompleted;
              console.log("Upload progress:", percentCompleted + "%");
            }
          },
        });
        console.log("Upload successful:", response);

        emit("uploaded", response.data);
        emit("close");
      } catch (error) {
        console.error("Upload failed:", error);
        console.log("Full error response:", error.response);
        console.log("Error data:", error.response?.data);
        console.log("Error status:", error.response?.status);

        if (
          error.response &&
          error.response.data &&
          error.response.data.errors
        ) {
          errors.value = error.response.data.errors;
          console.log("Validation errors:", error.response.data.errors);
          console.log(
            "Validation errors keys:",
            Object.keys(error.response.data.errors)
          );

          // Log each error
          Object.keys(error.response.data.errors).forEach((field) => {
            console.log(
              `Error for ${field}:`,
              error.response.data.errors[field]
            );
          });

          alert("Please check the form for errors and try again.");
        } else if (error.response && error.response.status === 401) {
          alert(
            "You must be logged in to upload sheet music. Please log in first."
          );
        } else if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          alert("Upload failed: " + error.response.data.message);
        } else {
          alert("Upload failed. Please try again.");
        }
      } finally {
        isUploading.value = false;
        uploadProgress.value = 0;
      }
    };

    return {
      form,
      selectedFile,
      uploadProgress,
      isUploading,
      errors,
      categories,
      loadingCategories,
      canUpload,
      handleFileSelect,
      handleDrop,
      clearFile,
      formatFileSize,
      uploadSheet,
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
  padding: 20px;
}

.modal-content {
  background: white;
  border-radius: 15px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 25px;
  border-bottom: 1px solid #eaeaea;
}

.modal-header h2 {
  margin: 0;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 10px;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #999;
  cursor: pointer;
  padding: 5px;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  background: #f5f5f5;
  color: #666;
}

.modal-body {
  padding: 25px;
}

.upload-area {
  background: #f8fafc;
  border: 2px dashed #c3cfe2;
  border-radius: 10px;
  padding: 40px 20px;
  text-align: center;
  margin-bottom: 30px;
  transition: all 0.3s;
}

.upload-area:hover {
  border-color: #4a6491;
  background: #f0f4f8;
}

.upload-icon {
  font-size: 3rem;
  color: #4a6491;
  margin-bottom: 15px;
}

.upload-area h3 {
  margin-bottom: 10px;
  color: #2c3e50;
}

.upload-area p {
  color: #666;
  margin-bottom: 5px;
}

.file-size {
  font-size: 0.9rem;
  color: #999;
}

.metadata-form h3 {
  margin-bottom: 20px;
  color: #2c3e50;
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
.form-group select {
  width: 100%;
  padding: 12px 15px;
  border: 2px solid #e1e7f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: border 0.3s;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #4a6491;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.modal-footer {
  padding: 20px 25px;
  border-top: 1px solid #eaeaea;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.btn {
  padding: 12px 25px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  transition: all 0.3s;
}

.btn-primary {
  background: #4a6491;
  color: white;
}

.btn-primary:hover {
  background: #3a5479;
}

.btn-secondary {
  background: #eef2f7;
  color: #4a6491;
}

.btn-secondary:hover {
  background: #dfe6f0;
}

.btn-cancel {
  background: transparent;
  color: #666;
  margin-right: 10px;
}

.btn-cancel:hover {
  background: #f5f5f5;
}

.footer-right {
  display: flex;
}

.progress-bar {
  width: 100%;
  height: 20px;
  background: #e1e7f0;
  border-radius: 10px;
  margin-top: 10px;
  overflow: hidden;
  position: relative;
}

.progress-fill {
  height: 100%;
  background: #4a6491;
  transition: width 0.3s ease;
}

.progress-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 0.8rem;
  font-weight: bold;
}

.error {
  border-color: #e74c3c !important;
}

.error-text {
  color: #e74c3c;
  font-size: 0.8rem;
  margin-top: 5px;
  display: block;
}

.form-group textarea {
  width: 100%;
  padding: 12px 15px;
  border: 2px solid #e1e7f0;
  border-radius: 8px;
  font-size: 1rem;
  resize: vertical;
  transition: border 0.3s;
}

.form-group textarea:focus {
  outline: none;
  border-color: #4a6491;
}
</style>
