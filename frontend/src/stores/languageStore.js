import { defineStore } from "pinia";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import translationService from "../services/translationService";

export const useLanguageStore = defineStore("language", () => {
  const currentLanguage = ref(localStorage.getItem("language") || "en");

  async function loadTranslations() {
    const messages = await translationService.loadTranslations(
      currentLanguage.value,
    );

    const { locale, setLocaleMessage } = useI18n();

    setLocaleMessage(currentLanguage.value, messages);

    locale.value = currentLanguage.value;
  }

  async function changeLanguage(language) {
    currentLanguage.value = language;

    localStorage.setItem("language", language);

    await loadTranslations();
  }

  return {
    currentLanguage,

    loadTranslations,

    changeLanguage,
  };
});
