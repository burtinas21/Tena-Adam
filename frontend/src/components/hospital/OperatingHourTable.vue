<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <h2 class="text-sm font-bold text-gray-800">Operating Hours</h2>
      <span class="text-xs text-gray-400 font-medium">{{ hours.length }} / 7 days</span>
    </div>

    <div v-if="hours.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
      <Clock class="w-10 h-10 mb-3 text-gray-300" />
      <p class="text-sm font-medium">No hours configured</p>
      <p class="text-xs mt-1">Set operating hours for each day of the week.</p>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead>
          <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
            <th class="px-5 py-3 font-semibold">Day</th>
            <th class="px-5 py-3 font-semibold">Open</th>
            <th class="px-5 py-3 font-semibold">Close</th>
            <th class="px-5 py-3 font-semibold">Holiday</th>
            <th class="px-5 py-3 font-semibold text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr
            v-for="hour in sortedHours"
            :key="hour.id"
            class="hover:bg-gray-50/60 transition-colors"
          >
            <td class="px-5 py-3.5 font-semibold text-gray-800">
              {{ DAY_LABELS[hour.day_of_week] }}
            </td>
            <td class="px-5 py-3.5 text-gray-600">{{ formatTime(hour.open_time) }}</td>
            <td class="px-5 py-3.5 text-gray-600">{{ formatTime(hour.close_time) }}</td>
            <td class="px-5 py-3.5">
              <span
                :class="hour.is_holiday
                  ? 'bg-red-50 text-red-600 border-red-200'
                  : 'bg-gray-50 text-gray-400 border-gray-200'"
                class="text-xs font-semibold px-2.5 py-0.5 rounded-full border"
              >
                {{ hour.is_holiday ? 'Holiday' : 'Open' }}
              </span>
            </td>
            <td class="px-5 py-3.5 text-right">
              <div class="flex items-center justify-end gap-2">
                <button
                  @click="$emit('edit', hour)"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-[#004795] hover:bg-[#004795]/10 transition"
                  title="Edit"
                >
                  <Pencil class="w-3.5 h-3.5" />
                </button>
                <button
                  @click="$emit('delete', hour)"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                  title="Delete"
                >
                  <Trash2 class="w-3.5 h-3.5" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { Clock, Pencil, Trash2 } from "lucide-vue-next";

const DAY_LABELS = [
  "Sunday", "Monday", "Tuesday", "Wednesday",
  "Thursday", "Friday", "Saturday",
];

const props = defineProps({
  hours: { type: Array, default: () => [] },
});
defineEmits(["edit", "delete"]);

const sortedHours = computed(() =>
  [...props.hours].sort((a, b) => a.day_of_week - b.day_of_week)
);

function formatTime(t) {
  if (!t) return "—";
  const [h, m] = t.split(":");
  const hour = parseInt(h, 10);
  const ampm = hour >= 12 ? "PM" : "AM";
  const display = hour % 12 || 12;
  return `${display}:${m} ${ampm}`;
}
</script>
