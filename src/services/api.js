// src/services/api.js
import axios from "axios";

const API_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

const api = axios.create({
  baseURL: API_URL,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
  withCredentials: true,
});

// Request interceptor for adding token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response interceptor for handling errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      window.location.href = "/login";
    }
    return Promise.reject(error);
  }
);

export default {
  // Auth
  register(userData) {
    return api.post("/register", userData);
  },

  login(credentials) {
    return api.post("/login", credentials);
  },

  logout() {
    return api.post("/logout");
  },

  getUser() {
    return api.get("/user");
  },

  // Sheet Music
  getSheetMusic(params = {}) {
    return api.get("/sheet-music", { params });
  },

  getFilters() {
    return api.get("/sheet-music/filters");
  },

  getStats() {
    return api.get("/sheet-music/stats");
  },

  getSheetMusicById(id) {
    return api.get(`/sheet-music/${id}`);
  },

  createSheetMusic(formData, config = {}) {
    return api.post("/sheet-music", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
      ...config,
    });
  },

  updateSheetMusic(id, data) {
    return api.put(`/sheet-music/${id}`, data);
  },

  deleteSheetMusic(id) {
    return api.delete(`/sheet-music/${id}`);
  },

  downloadSheetMusic(id) {
    return api.get(`/sheet-music/${id}/download`, {
      responseType: "blob",
    });
  },

  getMySheets(params = {}) {
    return api.get("/my-sheets", { params });
  },

  // Upload
  uploadChunk(formData) {
    return api.post("/upload/chunk", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
  },

  cancelUpload(data) {
    return api.post("/upload/cancel", data);
  },

  // Events
  getEvents(params = {}) {
    return api.get("/events", { params });
  },

  getEventById(id) {
    return api.get(`/events/${id}`);
  },

  createEvent(data) {
    return api.post("/events", data);
  },

  updateEvent(id, data) {
    return api.put(`/events/${id}`, data);
  },

  deleteEvent(id) {
    return api.delete(`/events/${id}`);
  },

  addSheetMusicToEvent(eventId, data) {
    return api.post(`/events/${eventId}/sheet-music`, data);
  },

  removeSheetMusicFromEvent(eventId, sheetMusicId) {
    return api.delete(`/events/${eventId}/sheet-music/${sheetMusicId}`);
  },

  updateEventSheetMusic(eventId, sheetMusicId, data) {
    return api.put(`/events/${eventId}/sheet-music/${sheetMusicId}`, data);
  },

  getSheetMusicEvents(sheetMusicId) {
    return api.get(`/sheet-music/${sheetMusicId}/events`);
  },

  // Categories
  getCategories(params = {}) {
    return api.get("/categories", { params });
  },

  getCategoriesGrouped() {
    return api.get("/categories/grouped");
  },

  createCategory(data) {
    return api.post("/categories", data);
  },

  updateCategory(id, data) {
    return api.put(`/categories/${id}`, data);
  },

  deleteCategory(id) {
    return api.delete(`/categories/${id}`);
  },
};
