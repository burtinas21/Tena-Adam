import { defineStore } from "pinia";
import authService from "../services/authService";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: JSON.parse(localStorage.getItem("user")) || null,
    token: localStorage.getItem("token") || null,
    loading: false,
    error: null,
  }),

  actions: {
    async login(credentials) {
      try {
        this.loading = true;
        this.error = null;

        const response = await authService.login(credentials);

        this.user = response.user;
        this.token = response.token;

        localStorage.setItem("token", this.token);
        localStorage.setItem("user", JSON.stringify(this.user));

        // Apply user's saved language preference from backend
        // Import lazily to avoid circular deps
        const { useLanguageStore } = await import("./languageStore");
        const langStore = useLanguageStore();
        const savedLang = response.user?.language?.code;
        if (savedLang && savedLang !== langStore.currentLanguage) {
          await langStore.changeLanguage(savedLang);
        } else {
          // Ensure current locale translations are loaded
          await langStore.loadTranslations(langStore.currentLanguage);
        }

        return response;
      } catch (error) {
        this.error = error.response?.data?.message || "Login failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async register(data) {
      return await authService.register(data);
    },

    async forgotPassword(data) {
      return await authService.forgotPassword(data);
    },

    async resetPassword(data) {
      return await authService.resetPassword(data);
    },

    async logout() {
      try {
        await authService.logout();
      } catch (error) {
        console.log("Logout error:", error);
      } finally {
        this.token = null;
        this.user = null;
        localStorage.removeItem("token");
        localStorage.removeItem("user");
      }
    },
  },
});
