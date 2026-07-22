<template>
  <button
    @click="toggleTheme"
    :title="dark ? 'Switch to light mode' : 'Switch to dark mode'"
    class="relative p-2 rounded-lg transition-all duration-200 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 flex items-center justify-center"
    aria-label="Toggle theme"
  >
    <!-- Sun icon (shown in dark mode to switch to light) -->
    <svg
      v-if="dark"
      xmlns="http://www.w3.org/2000/svg"
      class="w-5 h-5 text-amber-400"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    >
      <circle cx="12" cy="12" r="4" />
      <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" />
    </svg>
    <!-- Moon icon (shown in light mode to switch to dark) -->
    <svg
      v-else
      xmlns="http://www.w3.org/2000/svg"
      class="w-5 h-5 text-slate-600"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    >
      <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
    </svg>
  </button>
</template>

<script setup>
import { useTheme } from "@/composables/useTheme";

const { dark, toggleTheme } = useTheme();
</script>
