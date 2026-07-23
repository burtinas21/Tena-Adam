import translationApi from "../api/translationApi";

const translationService = {
  async fetchTranslations(language) {
    try {
      const response = await translationApi.getTranslations(language);

      return response.data;
    } catch (error) {
      console.error("Translation loading failed:", error);

      return {};
    }
  },
};

export default translationService;
