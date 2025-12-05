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
        <div class="upload-area">
          <i class="fas fa-file-pdf upload-icon"></i>
          <h3>Drop PDF files here or click to browse</h3>
          <p>Supported format: PDF only</p>
          <p class="file-size">Max file size: 50MB</p>
        </div>

        <div class="metadata-form">
          <h3>Metadata</h3>
          <div class="form-group">
            <label>Title</label>
            <input
              type="text"
              placeholder="Enter sheet music title"
              v-model="form.title"
            />
          </div>
          <div class="form-group">
            <label>Composer</label>
            <input
              type="text"
              placeholder="Enter composer name"
              v-model="form.composer"
            />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Instrument</label>
              <select v-model="form.instrument">
                <option value="">Select instrument</option>
                <option v-for="instrument in instruments" :key="instrument">
                  {{ instrument }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Difficulty</label>
              <select v-model="form.difficulty">
                <option value="">Select difficulty</option>
                <option v-for="difficulty in difficulties" :key="difficulty">
                  {{ difficulty }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" @click="$emit('add-sample')">
          <i class="fas fa-plus"></i> Add Sample Sheet
        </button>
        <div class="footer-right">
          <button class="btn btn-cancel" @click="$emit('close')">Cancel</button>
          <button class="btn btn-primary" @click="uploadSheet">Upload</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from "vue";

export default {
  name: "UploadModal",
  props: {
    instruments: {
      type: Array,
      default: () => [
        "Piano",
        "Guitar",
        "Violin",
        "Flute",
        "Cello",
        "Saxophone",
      ],
    },
    difficulties: {
      type: Array,
      default: () => ["Beginner", "Intermediate", "Advanced", "Professional"],
    },
  },
  emits: ["close", "add-sample"],
  setup(props, { emit }) {
    const form = ref({
      title: "",
      composer: "",
      instrument: "",
      difficulty: "",
      genre: "Classical",
      pages: 1,
    });

    const uploadSheet = () => {
      if (!form.value.title || !form.value.composer) {
        alert("Please fill in all required fields");
        return;
      }

      // In a real app, you would upload the file here
      console.log("Uploading sheet:", form.value);
      alert(
        "In a real app, this would upload the PDF file and metadata to your server."
      );
      emit("close");
    };

    return {
      form,
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
</style>
