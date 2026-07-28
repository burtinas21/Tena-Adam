import { createI18n } from "vue-i18n";

const i18n = createI18n({
  legacy: false,          // must be false to use Composition API ($t, useI18n)
  locale: localStorage.getItem("language") || "en",
  fallbackLocale: "en",
  // Messages start empty — they are loaded dynamically from the backend API
  // via languageStore.loadTranslations() called in main.js on app start.
  messages: {
    en: {},
    am: {},
    om: {},
    ti: {},
  },
});

export default i18n;
