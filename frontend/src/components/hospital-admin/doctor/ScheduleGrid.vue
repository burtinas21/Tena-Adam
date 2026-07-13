<template>
  <div class="border border-gray-200 rounded-xl overflow-hidden mt-4">
    <!-- Column headers: Time + Mon–Sat -->
    <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
      <div class="text-center py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider border-r border-gray-200">
        Time
      </div>
      <div
        v-for="(label, idx) in dayLabels"
        :key="idx"
        class="text-center py-2.5 text-[10px] font-semibold uppercase tracking-wider border-r border-gray-200 last:border-r-0"
        :class="scheduledDaySet.has(idx + 1) ? 'text-blue-600' : 'text-gray-300'"
      >
        {{ label }}
      </div>
    </div>

    <!-- Empty state -->
    <div v-if="!schedules.length" class="py-12 text-center text-gray-400">
      <Clock class="w-8 h-8 mx-auto mb-2 text-gray-300" />
      <p class="text-sm font-medium">No schedule configured</p>
      <p class="text-xs mt-1">Add working days using the panel above.</p>
    </div>

    <!-- Schedule rows -->
    <div v-else class="bg-white">
      <div
        v-for="schedule in sortedSchedules"
        :key="schedule.id"
        class="grid grid-cols-7 border-b border-gray-100 last:border-b-0 hover:bg-gray-50/40 transition-colors"
      >
        <!-- Day name cell -->
        <div class="px-3 py-3 border-r border-gray-100 flex items-center justify-center">
          <span class="text-xs font-bold text-gray-700">
            {{ DAY_LABELS[schedule.day_of_week] }}
          </span>
        </div>

        <!-- Remaining columns with schedule info -->
        <div class="col-span-6 px-4 py-3 flex items-center justify-between gap-4">
          <div class="flex items-center gap-4 flex-wrap text-xs text-gray-600">
            <span class="flex items-center gap-1 font-semibold text-blue-700">
              <Clock class="w-3.5 h-3.5" />
              {{ schedule.start_time }} – {{ schedule.end_time }}
            </span>
            <span v-if="schedule.lunch_start && schedule.lunch_end" class="text-gray-400">
              Lunch: {{ schedule.lunch_start }} – {{ schedule.lunch_end }}
            </span>
            <span class="text-gray-400">
              Slot: {{ schedule.slot_duration_min }}m
            </span>
            <span
              :class="schedule.is_available ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : 'text-gray-400 bg-gray-50 border-gray-200'"
              class="px-2 py-0.5 rounded-full border text-[10px] font-semibold"
            >
              {{ schedule.is_available ? 'Available' : 'Unavailable' }}
            </span>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-1 flex-shrink-0">
            <button
              @click="$emit('edit-schedule', schedule)"
              class="p-1.5 rounded-lg text-gray-400 hover:text-[#004795] hover:bg-[#004795]/10 transition"
              title="Edit"
            >
              <Pencil class="w-3.5 h-3.5" />
            </button>
            <button
              @click="$emit('delete-schedule', schedule)"
              class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
              title="Delete"
            >
              <Trash2 class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// Moving the named export to a normal script block allows other files to import DAY_LABELS perfectly
export const DAY_LABELS = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
</script>

<script setup>
import { computed } from "vue";
import { Clock, Pencil, Trash2 } from "lucide-vue-next";

const dayLabels = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

const props = defineProps({
  schedules: { type: Array, default: () => [] },
});
defineEmits(["edit-schedule", "delete-schedule"]);

const sortedSchedules = computed(() =>
  [...props.schedules].sort((a, b) => a.day_of_week - b.day_of_week)
);

// day_of_week: 1=Mon … 6=Sat
const scheduledDaySet = computed(
  () => new Set(props.schedules.map((s) => s.day_of_week))
);
</script>
