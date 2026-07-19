<template>
  <div class="relative flex items-start space-x-3.5 pb-6 last:pb-2">
    <!-- Vertical connector line -->
    <div
      class="absolute top-5 left-[11px] bottom-0 w-0.5 bg-gray-100 last:hidden"
    ></div>

    <!-- Icon Container -->
    <div
      class="relative flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-400 shadow-sm z-10"
    >
      <component :is="icon" class="w-3.5 h-3.5" />
    </div>

    <!-- Note Content -->
    <div class="flex-1 min-w-0">
      <div class="flex items-center justify-between">
        <h4 class="text-xs font-bold text-gray-900 tracking-tight">
          {{ title }}
        </h4>
        <button
          v-if="hasMenu"
          class="text-gray-400 hover:text-gray-600 p-0.5 rounded transition"
        >
          <MoreHorizontal class="w-3.5 h-3.5" />
        </button>
      </div>
      <p class="text-xs text-gray-500 font-normal leading-relaxed mt-1 pr-2">
        {{ content }}
      </p>
      <span
        class="inline-block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mt-1.5"
      >
        {{ timestamp }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { MoreHorizontal } from "lucide-vue-next";

defineProps({
  title: String,
  content: String,
  timestamp: String,
  icon: Object,
  hasMenu: { type: Boolean, default: false },
});
</script>
