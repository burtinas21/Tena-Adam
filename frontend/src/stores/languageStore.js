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
   * Always fetches from backend (skips only if already loaded AND messages are non-empty).
   * Pass force=true to force a re-fetch even if already cached (used after login).
   */
  async function loadTranslations(lang, { force = false } = {}) {
    const locale = lang ?? currentLanguage.value;

    // Only skip the fetch if already loaded AND messages are actually present
    if (!force && loaded.value.has(locale)) {
      const existing = i18n.global.getLocaleMessage(locale);
      if (existing && Object.keys(existing).length > 0) {
        i18n.global.locale.value = locale;
        currentLanguage.value = locale;
        return;
      }
    }

    const messages = await translationService.fetchTranslations(locale);

    // If fetch returned empty (e.g. network error, 401), fall back to English
    // but don't overwrite a previously loaded locale with empty messages
    if (!messages || Object.keys(messages).length === 0) {
      const existing = i18n.global.getLocaleMessage(locale);
      const hasExisting = existing && Object.keys(existing).length > 0;
      if (!hasExisting) {
        // Nothing cached either — keep locale but don't wipe it
        i18n.global.locale.value = locale;
        currentLanguage.value = locale;
        return;
      }
      // Keep the cached messages, just activate the locale
      i18n.global.locale.value = locale;
      currentLanguage.value = locale;
      return;
    }

    i18n.global.setLocaleMessage(locale, messages);
    i18n.global.locale.value = locale;
    currentLanguage.value = locale;
    loaded.value.add(locale);
  }

  /**
   * Switch language: persist to localStorage + backend (if authenticated),
   * fetch translations, update active i18n locale.
   */
  async function changeLanguage(lang) {
    currentLanguage.value = lang;
    localStorage.setItem("language", lang);
    // Force re-fetch to ensure fresh translations when user explicitly switches
    await loadTranslations(lang, { force: true });

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
