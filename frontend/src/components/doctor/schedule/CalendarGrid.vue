<template>
  <div
    class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden flex flex-col"
  >
    <!-- Header -->
    <div
      class="p-4 border-b border-gray-100 flex items-center justify-between gap-3 bg-white"
    >
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
    <div
      v-if="!schedules.length"
      class="py-12 flex flex-col items-center justify-center text-gray-400"
    >
      <Calendar class="w-8 h-8 mb-2 text-gray-300" />
      <p class="text-sm font-medium">No schedule set</p>
      <p class="text-xs mt-1">Add working days to see your schedule.</p>
    </div>

    <!-- Schedule table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-xs text-left border-collapse min-w-[600px]">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th
              class="px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider w-24"
            >
              Day
            </th>
            <th
              class="px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider"
            >
              Working Hours
            </th>
            <th
              class="px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider"
            >
              Lunch Break
            </th>
            <th
              class="px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider"
            >
              Slot
            </th>
            <th
              class="px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider"
            >
              Status
            </th>
            <th
              class="px-4 py-2.5 text-right text-[10px] font-semibold text-gray-400 uppercase tracking-wider"
            >
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr
            v-for="(s,index) in sortedSchedules"
            :key="s.id"
            class="hover:bg-gray-50/60 transition-colors"
            :class="{ 'opacity-50': !s.is_available }"
          >
            <td class="px-4 py-3 font-bold text-gray-800">
              {{ DAY_LABELS[s.day_of_week] }}
            </td>
            <td class="px-4 py-3 text-gray-700 font-semibold">
              {{ s.start_time }} – {{ s.end_time }}
            </td>
            <td class="px-4 py-3 text-gray-500">
              <span v-if="s.lunch_start && s.lunch_end"
                >{{ s.lunch_start }} – {{ s.lunch_end }}</span
              >
              <span v-else class="text-gray-300">—</span>
            </td>
            <td class="px-4 py-3 text-gray-500">{{ s.slot_duration_min }}m</td>
            <td class="px-4 py-3">
              <span
                :class="
                  s.is_available
                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                    : 'bg-gray-50 text-gray-400 border-gray-200'
                "
                class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
              >
                {{ s.is_available ? "Available" : "Unavailable" }}
              </span>
            </td>
            <!-- Three-dot action menu -->
            <td class="px-4 py-3 text-right">
              <div class="relative inline-block" @click.stop>
                <button
                  @click="toggleMenu(s.id)"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                  title="Actions"
                >
                  <MoreVertical class="w-4 h-4" />
                </button>
                <Transition name="dropdown">
                  <div
                    v-if="openMenuId === s.id"
                    :class="[
                      'absolute right-0 w-36 bg-white rounded-xl shadow-lg border border-gray-100 z-50 py-1',
                      index < 1 ? 'top-full mt-1' : 'bottom-full mb-1',
                    ]"
                  >
                    <button
                      @click="onEdit(s)"
                      class="w-full flex items-center gap-2 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 transition"
                    >
                      <Pencil class="w-3.5 h-3.5 text-[#004795]" /> Edit
                    </button>
                    <button
                      @click="onDelete(s)"
                      class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 hover:bg-red-50 transition"
                    >
                      <Trash2 class="w-3.5 h-3.5" /> Delete
                    </button>
                  </div>
                </Transition>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Click-outside overlay to close menu -->
    <div
      v-if="openMenuId"
      class="fixed inset-0 z-20"
      @click="openMenuId = null"
    />
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Plus, Calendar, Pencil, Trash2, MoreVertical } from "lucide-vue-next";

const DAY_LABELS = [
  "Sunday",
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
];

const props = defineProps({
  schedules: { type: Array, default: () => [] },
});
const emit = defineEmits(["add", "edit", "delete"]);

const openMenuId = ref(null);

const sortedSchedules = computed(() =>
  [...props.schedules].sort((a, b) => a.day_of_week - b.day_of_week),
);

function toggleMenu(id) {
  openMenuId.value = openMenuId.value === id ? null : id;
}

function onEdit(s) {
  openMenuId.value = null;
  emit("edit", s);
}

function onDelete(s) {
  openMenuId.value = null;
  emit("delete", s);
}
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition:
    opacity 0.12s,
    transform 0.12s;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
