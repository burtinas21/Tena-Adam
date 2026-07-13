<template>
  <div
    class="bg-[#004795] text-white p-6 rounded-2xl shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[160px]"
  >
    <div>
      <h2 class="text-2xl font-bold tracking-tight">
        Good {{ greeting }}, {{ name }}
      </h2>
      <p class="text-xs text-white/80 font-medium mt-1">
        Here is your healthcare summary for today.
      </p>
    </div>

    <!-- Progress Track Bar Section -->
    <div class="mt-6 max-w-xl flex items-center justify-between gap-x-6">
      <div class="flex-1">
        <div class="flex justify-between text-[11px] font-semibold text-white/90 mb-1.5">
          <span>Profile Completion</span>
          <span>{{ completion }}%</span>
        </div>
        <div class="w-full bg-white/20 h-2 rounded-full overflow-hidden">
          <div
            class="bg-cyan-400 h-full rounded-full transition-all duration-500"
            :style="{ width: completion + '%' }"
          ></div>
        </div>
      </div>
      <router-link
        to="/patient/profile"
        class="bg-white hover:bg-gray-50 text-[#004795] font-bold text-xs py-2 px-4 rounded-lg shadow-sm transition flex-shrink-0"
      >
        Complete Profile
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  name: { type: String, default: "there" },
  completion: { type: Number, default: 0 },
});

const greeting = computed(() => {
  const h = new Date().getHours();
  if (h < 12) return "Morning";
  if (h < 17) return "Afternoon";
  return "Evening";
});
</script>
