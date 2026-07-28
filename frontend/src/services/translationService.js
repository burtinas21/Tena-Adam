import translationApi from "../api/translationApi";

const translationService = {
  /**
   * Fetch all translations for a given language code.
   * Returns a flat key→value object like { "dashboard.title": "ዳሽቦርድ", ... }
   */
  async fetchTranslations(language) {
    try {
      const response = await translationApi.getAll(language);
      // Backend returns the object directly (not nested under data.data)
      return response.data ?? {};
    } catch (error) {
      console.error("Translation loading failed:", error);
      return {};
    }
  },
};

export default translationService;
