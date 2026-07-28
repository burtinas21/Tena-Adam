import { defineStore } from "pinia";
import { ref } from "vue";
import i18n from "../i18n";
import translationService from "../services/translationService";
import axios from "../api/axios";

export const useLanguageStore = defineStore("language", () => {
  const currentLanguage = ref(localStorage.getItem("language") || "en");
  const loaded = ref(new Set());

  /**
   * Load translations for a locale from the backend and register with vue-i18n.
   * Skips the fetch if that locale was already loaded.
   */
  async function loadTranslations(lang) {
    const locale = lang ?? currentLanguage.value;

    if (loaded.value.has(locale)) {
      i18n.global.locale.value = locale;
      return;
    }

    const messages = await translationService.fetchTranslations(locale);
    i18n.global.setLocaleMessage(locale, messages);
    i18n.global.locale.value = locale;
    loaded.value.add(locale);
  }

  /**
   * Switch language: persist to localStorage + backend (if authenticated),
   * fetch translations, update active i18n locale.
   */
  async function changeLanguage(lang) {
    currentLanguage.value = lang;
    localStorage.setItem("language", lang);
    await loadTranslations(lang);

    // Persist to backend for authenticated users (fire-and-forget)
    try {
      await axios.put("/user/language", { language_code: lang });
    } catch {
      // Not logged in or request failed — silently ignore
    }
  }

  return {
    currentLanguage,
    loadTranslations,
    changeLanguage,
  };
});
