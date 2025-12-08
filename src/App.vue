<template>
  <div id="app" class="container">
    <Header :app-name="appName" :app-description="appDescription" />

    <Stats
      :total-sheets="stats.total_sheets || 0"
      :filtered-sheets="filteredSheets.length"
      :instruments="stats.total_instruments || 0"
      :genres="stats.total_genres || 0"
    />

    <div class="main-layout">
      <FilterSidebar
        :instruments="instruments"
        :genres="genres"
        :difficulties="difficulties"
        :events="events"
        :active-filters="activeFilters"
        @toggle-filter="toggleFilter"
        @clear-filters="clearFilters"
        @show-upload="showUploadModal = true"
        @show-events="showEventsModal = true"
      />

      <div class="content">
        <div class="search-header">
          <h2 class="section-title">
            <i class="fas fa-list"></i> Sheet Music Collection
          </h2>
          <div class="search-label"><i class="fas fa-search"></i> Search:</div>
        </div>

        <input
          type="text"
          class="search-box"
          placeholder="Search by title, composer, or tags..."
          v-model="searchQuery"
        />

        <SheetGrid
          :sheets="filteredSheets"
          @view-sheet="viewSheet"
          @download-sheet="downloadSheet"
          @add-to-event="addToEvent"
        />
      </div>
    </div>

    <UploadModal
      v-if="showUploadModal"
      @close="showUploadModal = false"
      @uploaded="onSheetUploaded"
    />

    <EventModal
      :show="showEventsModal"
      @close="showEventsModal = false"
      @saved="onEventSaved"
    />

    <AddToEventModal
      :show="showAddToEventModal"
      :sheet="selectedSheetForEvent"
      @close="showAddToEventModal = false"
      @added="onSheetAddedToEvent"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from "vue";
import Header from "./components/Header.vue";
import Stats from "./components/Stats.vue";
import FilterSidebar from "./components/FilterSidebar.vue";
import SheetGrid from "./components/SheetGrid.vue";
import UploadModal from "./components/UploadModal.vue";
import EventModal from "./components/EventModal.vue";
import AddToEventModal from "./components/AddToEventModal.vue";
import api from "./services/api";

export default {
  name: "App",
  components: {
    Header,
    Stats,
    FilterSidebar,
    SheetGrid,
    UploadModal,
    EventModal,
    AddToEventModal,
  },
  setup() {
    // App data
    const appName = ref("Sheet Music Organizer");
    const appDescription = ref(
      "Organize your sheet music collection by instrument, genre, difficulty, and composer. Upload, categorize, and quickly find the perfect piece."
    );

    // Sample data
    const sheetMusic = ref([]);

    // Filter options
    const instruments = ref([]);
    const genres = ref([]);
    const difficulties = ref([]);
    const events = ref([]);
    const stats = ref({
      total_sheets: 0,
      total_instruments: 0,
      total_genres: 0,
      total_downloads: 0,
      total_views: 0,
    });

    // Methods to fetch from API
    const fetchSheetMusic = async (params = {}) => {
      try {
        const response = await api.getSheetMusic(params);
        sheetMusic.value = response.data.data;
      } catch (error) {
        console.error("Error fetching sheet music:", error);
      }
    };

    // Fetch with current filters
    const fetchFilteredSheetMusic = async () => {
      const params = {};
      if (activeFilters.value.instrument)
        params.instrument = activeFilters.value.instrument;
      if (activeFilters.value.genre) params.genre = activeFilters.value.genre;
      if (activeFilters.value.difficulty)
        params.difficulty = activeFilters.value.difficulty;
      if (searchQuery.value) params.search = searchQuery.value;
      await fetchSheetMusic(params);
    };

    const fetchFilters = async () => {
      try {
        const response = await api.getFilters();
        instruments.value = response.data.instruments;
        genres.value = response.data.genres;
        difficulties.value = response.data.difficulties;
        events.value = response.data.events;
      } catch (error) {
        console.error("Error fetching filters:", error);
      }
    };

    const fetchStats = async () => {
      try {
        const response = await api.getStats();
        stats.value = response.data;
      } catch (error) {
        console.error("Error fetching stats:", error);
      }
    };

    // Active filters
    const activeFilters = ref({
      instrument: null,
      genre: null,
      difficulty: null,
    });

    // Search query
    const searchQuery = ref("");

    // Initialize data
    onMounted(() => {
      fetchFilteredSheetMusic();
      fetchFilters();
      fetchStats();
    });

    // Watch for filter changes
    watch(activeFilters, fetchFilteredSheetMusic, { deep: true });
    watch(searchQuery, fetchFilteredSheetMusic);

    // UI state
    const showUploadModal = ref(false);
    const showEventsModal = ref(false);
    const showAddToEventModal = ref(false);
    const selectedSheetForEvent = ref(null);

    // Computed properties
    const filteredSheets = computed(() => {
      return sheetMusic.value;
    });

    const totalSheets = computed(() => sheetMusic.value.length);

    // Methods
    const toggleFilter = (filterType, value) => {
      if (activeFilters.value[filterType] === value) {
        activeFilters.value[filterType] = null;
      } else {
        activeFilters.value[filterType] = value;
      }
    };

    const clearFilters = () => {
      activeFilters.value = {
        instrument: null,
        genre: null,
        difficulty: null,
      };
      searchQuery.value = "";
    };

    const viewSheet = (sheet) => {
      if (sheet.file_url) {
        // Open PDF in new tab
        window.open(sheet.file_url, "_blank");
      } else {
        alert("PDF file not available for preview.");
      }
    };

    const downloadSheet = async (sheet) => {
      try {
        const response = await api.downloadSheetMusic(sheet.id);
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", sheet.file_name || `${sheet.title}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
      } catch (error) {
        console.error("Download failed:", error);
        alert("Failed to download the file. Please try again.");
      }
    };

    const onSheetUploaded = async (uploadedSheet) => {
      // Refresh the sheet music list
      await fetchFilteredSheetMusic();
      await fetchStats();
      showUploadModal.value = false;
      alert("Sheet music uploaded successfully!");
    };

    const addSampleSheet = () => {
      const newSheet = {
        id: sheetMusic.value.length + 1,
        title: "Sample Sheet Music",
        composer: "Sample Composer",
        instrument: "Piano",
        genre: "Classical",
        difficulty: "Intermediate",
        pages: 4,
        description:
          "This is a sample sheet added through the upload interface.",
        tags: ["sample", "demo"],
        fileUrl: "#",
      };

      sheetMusic.value.push(newSheet);
      showUploadModal.value = false;
      alert(
        "Sample sheet music added! In a real app, you would upload a PDF file."
      );
    };

    const onEventSaved = () => {
      // Refresh data if needed
      alert("Event saved successfully!");
    };

    const addToEvent = (sheet) => {
      selectedSheetForEvent.value = sheet;
      showAddToEventModal.value = true;
    };

    const onSheetAddedToEvent = () => {
      alert("Sheet music added to event successfully!");
    };

    return {
      appName,
      appDescription,
      sheetMusic,
      instruments,
      genres,
      difficulties,
      stats,
      activeFilters,
      searchQuery,
      showUploadModal,
      showEventsModal,
      filteredSheets,
      totalSheets,
      toggleFilter,
      clearFilters,
      viewSheet,
      downloadSheet,
      onSheetUploaded,
      addSampleSheet,
      onEventSaved,
      addToEvent,
      onSheetAddedToEvent,
    };
  },
};
</script>

<style scoped>
.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

.main-layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 25px;
  margin-top: 25px;
}

@media (max-width: 1024px) {
  .main-layout {
    grid-template-columns: 1fr;
  }
}

.content {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.search-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-title {
  font-size: 1.3rem;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 0;
}

.search-label {
  color: #4a6491;
  font-weight: 600;
}

.search-box {
  width: 100%;
  padding: 15px;
  border: 2px solid #e1e7f0;
  border-radius: 10px;
  font-size: 1rem;
  margin-bottom: 25px;
  transition: border 0.3s;
}

.search-box:focus {
  outline: none;
  border-color: #4a6491;
}
</style>
