import { createApp } from "vue";
import App from "./App.vue";

// Global styles
import "./assets/style.css";

// Import Font Awesome CSS (you can use a package or CDN)
const fontAwesomeLink = document.createElement("link");
fontAwesomeLink.rel = "stylesheet";
fontAwesomeLink.href =
  "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css";
document.head.appendChild(fontAwesomeLink);

createApp(App).mount("#app");
