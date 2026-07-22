import { defineStore } from "pinia";

export const useThemeStore = defineStore("theme", {
  state: () => ({
    dark: false,
  }),

  actions: {
    initializeTheme() {
      const savedTheme = localStorage.getItem("theme");

      if (savedTheme) {
        this.dark = savedTheme === "dark";
      } else {
        this.dark = window.matchMedia("(prefers-color-scheme: dark)").matches;
      }

      this.applyTheme();
    },

    toggleTheme() {
      this.dark = !this.dark;

      localStorage.setItem(
        "theme",

        this.dark ? "dark" : "light",
      );

      this.applyTheme();
    },

    applyTheme() {
      document.documentElement.classList.toggle(
        "dark",

        this.dark,
      );
    },
  },
});
