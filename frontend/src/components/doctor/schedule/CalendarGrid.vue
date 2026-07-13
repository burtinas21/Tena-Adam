<template>
  <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden flex flex-col">
    <!-- Header -->
    <div class="p-4 border-b border-gray-100 flex items-center justify-between gap-3 bg-white">
      <h3 class="text-xs font-bold text-gray-800">Weekly Schedule</h3>
      <div class="flex items-center gap-2">
        <button
          @click="$emit('add')"
          class="px-3 py-1.5 text-xs font-bold text-white bg-[#004795] hover:bg-[#003670] rounded-lg shadow-sm flex items-center gap-1 transition-colors"
        >
          <Plus class="w-3 h-3" /> Add Day
        </button>
      </div>
    </div>
    <!-- Empty state -->
    <div v-if="!schedules.length" class="py-12 flex flex-col items-center justify-center text-gray-400">
      <Calendar class="w-8 h-8 mb-2 text-gray-300" />
      <p class="text-sm font-medium">No schedule set</p>
      <p class="text-xs mt-1">Add working days to see your schedule.</p>
    </div>

    <!-- Schedule table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-xs text-left border-collapse min-w-[600px]">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th class="px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider w-24">Day</th>
            <th class="px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Working Hours</th>
            <th class="px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Lunch Break</th>
            <th class="px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Slot</th>
            <th class="px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Status</th>
            <th class="px-4 py-2.5 text-right text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr
            v-for="s in sortedSchedules"
            :key="s.id"
            class="hover:bg-gray-50/60 transition-colors"
            :class="{ 'opacity-50': !s.is_available }"
          >
            <td class="px-4 py-3 font-bold text-gray-800">{{ DAY_LABELS[s.day_of_week] }}</td>
            <td class="px-4 py-3 text-gray-700 font-semibold text-blue-700">
              {{ s.start_time }} – {{ s.end_time }}
            </td>
            <td class="px-4 py-3 text-gray-500">
              <span v-if="s.lunch_start && s.lunch_end">{{ s.lunch_start }} – {{ s.lunch_end }}</span>
              <span v-else class="text-gray-300">—</span>
            </td>
            <td class="px-4 py-3 text-gray-500">{{ s.slot_duration_min }}m</td>
            <td class="px-4 py-3">
              <span
                :class="s.is_available
                  ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                  : 'bg-gray-50 text-gray-400 border-gray-200'"
                class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
              >
                {{ s.is_available ? 'Available' : 'Unavailable' }}
              </span>
            </td>
            <td class="px-4 py-3 text-right">
              <div class="flex items-center justify-end gap-1">
                <button @click="$emit('edit', s)" title="Edit"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-[#004795] hover:bg-[#004795]/10 transition">
                  <Pencil class="w-3.5 h-3.5" />
                </button>
                <button @click="$emit('delete', s)" title="Delete"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
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
import { Plus, Calendar, Pencil, Trash2 } from "lucide-vue-next";

const DAY_LABELS = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

const props = defineProps({
  schedules: { type: Array, default: () => [] },
});
defineEmits(["add", "edit", "delete"]);

const sortedSchedules = computed(() =>
  [...props.schedules].sort((a, b) => a.day_of_week - b.day_of_week)
);
</script>
