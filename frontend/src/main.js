import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router";
import i18n from "./i18n";
import "./assets/main.css";
import "./assets/css/theme.css";
import VueGoogleMaps from "@fawmi/vue-google-maps";

const app = createApp(App);
const pinia = createPinia();
app.use(VueGoogleMaps, {

  load: {

    key: import.meta.env.VITE_GOOGLE_MAPS_KEY,

    libraries: "places",

  },

});
app.use(pinia);
app.use(i18n);
app.use(router);
import { useLanguageStore } from "./stores/languageStore";
const languageStore = useLanguageStore();
// Always force-fetch on startup so translations are loaded even if the
// cache is empty (e.g. first load, or after a hard refresh). The token is
// already in localStorage at this point for authenticated users, so the
// backend request will succeed with the correct Authorization header.
languageStore.loadTranslations(undefined, { force: true }).finally(() => {
  app.mount("#app");
});
