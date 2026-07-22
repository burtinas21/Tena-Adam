<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
      <h2 class="text-sm font-bold text-gray-800">Operating Hours</h2>
      <span class="text-xs text-gray-400 font-medium">{{ hours.length }} / 7 days</span>
    </div>

    <div v-if="hours.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
      <Clock class="w-10 h-10 mb-3 text-gray-300" />
      <p class="text-sm font-medium">No hours configured</p>
      <p class="text-xs mt-1">Set operating hours for each day of the week.</p>
    </div>

    <div v-else class="w-full">
      <table class="w-full table-fixed">
        <!--
          All columns always visible.
          On mobile: smaller text + tighter padding = fits without scroll.
          Day 22% | Open 16% | Close 16% | Status 20% | Created 18% | Actions 8%
        -->
        <colgroup>
          <col style="width:22%" />
          <col style="width:16%" />
          <col style="width:16%" />
          <col style="width:20%" />
          <col style="width:18%" />
          <col style="width:8%" />
        </colgroup>

        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Day</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Open</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Close</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Status</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Created</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
          <tr
            v-for="hour in sortedHours"
            :key="hour.id"
            class="hover:bg-gray-50/50 transition-colors"
          >
            <!-- Day -->
            <td class="px-2 sm:px-4 py-2 sm:py-3">
              <div class="flex items-center gap-1.5">
                <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
                  <Clock class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-[#004795]" />
                </div>
                <span class="font-semibold text-gray-800 text-[10px] sm:text-xs truncate">
                  <span class="hidden xs:inline sm:inline">{{ DAY_LABELS[hour.day_of_week] }}</span>
                  <span class="inline sm:hidden">{{ DAY_SHORT[hour.day_of_week] }}</span>
                </span>
              </div>
            </td>

            <!-- Open -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-[10px] sm:text-xs text-gray-600 whitespace-nowrap">
              {{ formatTime(hour.open_time) }}
            </td>

            <!-- Close -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-[10px] sm:text-xs text-gray-600 whitespace-nowrap">
              {{ formatTime(hour.close_time) }}
            </td>

            <!-- Status -->
            <td class="px-2 sm:px-4 py-2 sm:py-3">
              <span
                :class="hour.is_holiday
                  ? 'bg-red-50 text-red-600 border-red-200'
                  : 'bg-emerald-50 text-emerald-700 border-emerald-200'"
                class="inline-flex items-center gap-1 text-[9px] sm:text-[10px] font-semibold px-1.5 sm:px-2 py-0.5 rounded-full border whitespace-nowrap"
              >
                <span :class="hour.is_holiday ? 'bg-red-500' : 'bg-emerald-500'"
                  class="w-1.5 h-1.5 rounded-full flex-shrink-0" />
                {{ hour.is_holiday ? 'Holiday' : 'Open' }}
              </span>
            </td>

            <!-- Created -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-[10px] sm:text-xs text-gray-400 whitespace-nowrap">
              {{ formatDate(hour.created_at) }}
            </td>

            <!-- Actions -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-right">
              <div class="relative inline-block" @click.stop>
                <button
                  @click="toggleMenu(hour.id)"
                  class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                >
                  <MoreVertical class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </button>
                <div
                  v-if="openMenuId === hour.id"
                  class="absolute right-0 mt-1 w-32 bg-white border border-gray-100 rounded-xl shadow-lg z-30 py-1"
                >
                  <button
                    @click="emit('edit', hour); closeMenu()"
                    class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition"
                  >
                    <Pencil class="w-3.5 h-3.5 text-[#004795]" /> Edit
                  </button>
                  <button
                    @click="emit('delete', hour); closeMenu()"
                    class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition"
                  >
                    <Trash2 class="w-3.5 h-3.5" /> Delete
                  </button>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { Clock, Pencil, Trash2, MoreVertical } from "lucide-vue-next";

const DAY_LABELS = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
const DAY_SHORT  = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];

const props = defineProps({ hours: { type: Array, default: () => [] } });
const emit = defineEmits(["edit", "delete"]);

const openMenuId = ref(null);

const sortedHours = computed(() =>
  [...props.hours].sort((a, b) => a.day_of_week - b.day_of_week)
);

function toggleMenu(id) {
  openMenuId.value = openMenuId.value === id ? null : id;
}
function closeMenu() { openMenuId.value = null; }
function handleOutsideClick() { openMenuId.value = null; }

onMounted(() => document.addEventListener("click", handleOutsideClick));
onUnmounted(() => document.removeEventListener("click", handleOutsideClick));

function formatTime(t) {
  if (!t) return "—";
  const [h, m] = t.split(":");
  const hour = parseInt(h, 10);
  const ampm = hour >= 12 ? "PM" : "AM";
  const display = hour % 12 || 12;
  return `${display}:${m} ${ampm}`;
}

function formatDate(val) {
  if (!val) return "—";
  return new Date(val).toLocaleDateString("en-ET", {
    day: "numeric", month: "short", year: "numeric",
  });
}
</script>
