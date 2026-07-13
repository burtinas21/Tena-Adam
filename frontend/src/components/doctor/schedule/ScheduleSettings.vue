<template>
  <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-5">
    <h3 class="text-xs font-bold text-gray-900 tracking-tight pb-3 border-b border-gray-100">
      Schedule Settings
    </h3>

    <!-- Working days -->
    <div>
      <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-3">Working Days</span>
      <div class="flex items-center gap-1.5 flex-wrap">
        <button
          v-for="(label, idx) in DAY_ABBR"
          :key="idx"
          :title="DAY_LABELS[idx]"
          :class="scheduledDaySet.has(idx)
            ? 'bg-blue-600 text-white shadow-sm'
            : 'border border-gray-200 bg-white text-gray-400 hover:text-gray-700'"
          class="w-7 h-7 rounded-full font-black text-xs flex items-center justify-center transition"
        >
          {{ label }}
        </button>
      </div>
    </div>

    <!-- Schedule list -->
    <div v-if="schedules.length">
      <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-2">Your Schedule</span>
      <div class="space-y-1.5">
        <div
          v-for="s in sortedSchedules"
          :key="s.id"
          class="flex items-center justify-between text-xs py-1.5 px-2.5 bg-gray-50 rounded-lg"
        >
          <span class="font-semibold text-gray-700 w-8">{{ DAY_ABBR[s.day_of_week] }}</span>
          <span class="text-gray-500">{{ s.start_time }} – {{ s.end_time }}</span>
          <span
            :class="s.is_available ? 'text-emerald-600' : 'text-gray-400'"
            class="text-[10px] font-bold"
          >{{ s.is_available ? '✓' : '✗' }}</span>
        </div>
      </div>
    </div>

    <!-- Add schedule button -->
    <button
      @click="$emit('add')"
      :disabled="loading"
      class="w-full bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 rounded-xl shadow-sm transition-colors disabled:opacity-50"
    >
      + Add Working Day
    </button>

    <!-- Error -->
    <p v-if="error" class="text-xs text-red-600 font-medium">{{ error }}</p>
  </div>
</template>

<script setup>
import { computed } from "vue";

const DAY_ABBR   = ["S","M","T","W","T","F","S"];
const DAY_LABELS = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];

const props = defineProps({
  schedules: { type: Array,   default: () => [] },
  loading:   { type: Boolean, default: false },
  error:     { type: String,  default: null },
});
defineEmits(["add"]);

const scheduledDaySet = computed(() =>
  new Set(props.schedules.map((s) => s.day_of_week))
);

const sortedSchedules = computed(() =>
  [...props.schedules].sort((a, b) => a.day_of_week - b.day_of_week)
);
</script>
