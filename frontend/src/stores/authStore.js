import { defineStore } from "pinia";

import authService from "../services/authService";

export const useAuthStore = defineStore(
  "auth",

  {
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

          return response;
        } catch (error) {
          this.error = error.response?.data?.message || "Login failed";

          throw error;
        } finally {
          this.loading = false;
        }
      },

      async register(data) {
        const response = await authService.register(data);

        return response;
      },

      async forgotPassword(data) {
        const response = await authService.forgotPassword(data);

        return response;
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
  },
);
