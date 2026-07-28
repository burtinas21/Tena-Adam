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
languageStore.loadTranslations().finally(() => {
  app.mount("#app");
});
