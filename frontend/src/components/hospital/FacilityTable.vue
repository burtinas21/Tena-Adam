<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
      <h2 class="text-sm font-bold text-gray-800">Facilities</h2>
      <span class="text-xs text-gray-400 font-medium">{{ facilities.length }} total</span>
    </div>

    <div v-if="facilities.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
      <Layers class="w-10 h-10 mb-3 text-gray-300" />
      <p class="text-sm font-medium">No facilities yet</p>
      <p class="text-xs mt-1">Add your first facility to get started.</p>
    </div>

    <!--
      All columns always visible — no hidden columns.
      Mobile: smaller text (text-[10px]) + tighter padding (px-2 py-2).
      table-fixed + colgroup keeps gaps proportional at every screen size.
      Name 20% | Type 12% | Status 14% | Description 28% | Created 18% | Actions 8%
    -->
    <div v-else class="w-full">
      <table class="w-full table-fixed">
        <colgroup>
          <col style="width:20%" />
          <col style="width:12%" />
          <col style="width:14%" />
          <col style="width:28%" />
          <col style="width:18%" />
          <col style="width:8%" />
        </colgroup>

        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Name</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Type</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Status</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Description</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Created</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
          <tr
            v-for="facility in facilities"
            :key="facility.id"
            class="hover:bg-gray-50/50 transition-colors align-top"
          >
            <!-- Name -->
            <td class="px-2 sm:px-4 py-2 sm:py-3">
              <div class="flex items-start gap-1.5">
                <div class="w-5 h-5 sm:w-6 sm:h-6 mt-0.5 rounded-md bg-blue-50 flex items-center justify-center flex-shrink-0">
                  <Layers class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-blue-500" />
                </div>
                <span class="font-semibold text-[10px] sm:text-xs text-gray-800 leading-snug break-words min-w-0">
                  {{ facility.name }}
                </span>
              </div>
            </td>

            <!-- Type -->
            <td class="px-2 sm:px-4 py-2 sm:py-3">
              <span class="text-[9px] sm:text-[10px] font-medium text-gray-600 capitalize bg-gray-100 px-1.5 sm:px-2 py-0.5 rounded-md whitespace-nowrap">
                {{ facility.type }}
              </span>
            </td>

            <!-- Status -->
            <td class="px-2 sm:px-4 py-2 sm:py-3">
              <span
                :class="statusClass(facility.status)"
                class="inline-flex items-center gap-1 text-[9px] sm:text-[10px] font-semibold px-1.5 sm:px-2 py-0.5 rounded-full border whitespace-nowrap capitalize"
              >
                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :class="statusDotClass(facility.status)" />
                {{ facility.status }}
              </span>
            </td>

            <!-- Description -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-[10px] sm:text-xs text-gray-500 leading-relaxed break-words">
              {{ facility.description || '—' }}
            </td>

            <!-- Created -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-[10px] sm:text-xs text-gray-400 whitespace-nowrap">
              {{ formatDate(facility.created_at) }}
            </td>

            <!-- Actions -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-right">
              <div class="relative inline-block" @click.stop>
                <button
                  @click="toggleMenu(facility.id)"
                  class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                >
                  <MoreVertical class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </button>
                <div
                  v-if="openMenuId === facility.id"
                  class="absolute right-0 mt-1 w-32 bg-white border border-gray-100 rounded-xl shadow-lg z-30 py-1"
                >
                  <button
                    @click="emit('edit', facility); closeMenu()"
                    class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition"
                  >
                    <Pencil class="w-3.5 h-3.5 text-[#004795]" /> Edit
                  </button>
                  <button
                    @click="emit('delete', facility); closeMenu()"
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
import { ref, onMounted, onUnmounted } from "vue";
import { Layers, Pencil, Trash2, MoreVertical } from "lucide-vue-next";

defineProps({ facilities: { type: Array, default: () => [] } });
const emit = defineEmits(["edit", "delete"]);

const openMenuId = ref(null);

function toggleMenu(id) {
  openMenuId.value = openMenuId.value === id ? null : id;
}
function closeMenu() { openMenuId.value = null; }
function handleOutsideClick() { openMenuId.value = null; }

onMounted(() => document.addEventListener("click", handleOutsideClick));
onUnmounted(() => document.removeEventListener("click", handleOutsideClick));

function formatDate(val) {
  if (!val) return "—";
  return new Date(val).toLocaleDateString("en-ET", {
    day: "numeric", month: "short", year: "numeric",
  });
}

function statusClass(status) {
  return {
    available:   "bg-emerald-50 text-emerald-700 border-emerald-200",
    occupied:    "bg-blue-50 text-blue-700 border-blue-200",
    maintenance: "bg-amber-50 text-amber-700 border-amber-200",
    reserved:    "bg-purple-50 text-purple-700 border-purple-200",
  }[status] ?? "bg-gray-50 text-gray-500 border-gray-200";
}

function statusDotClass(status) {
  return {
    available:   "bg-emerald-500",
    occupied:    "bg-blue-500",
    maintenance: "bg-amber-500",
    reserved:    "bg-purple-500",
  }[status] ?? "bg-gray-400";
}
</script>
