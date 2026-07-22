<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <!-- Card header -->
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
      <h2 class="text-sm font-bold text-gray-800">Departments</h2>
      <span class="text-xs text-gray-400 font-medium">{{ departments.length }} total</span>
    </div>

    <!-- Empty state -->
    <div v-if="departments.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
      <Building2 class="w-10 h-10 mb-3 text-gray-300" />
      <p class="text-sm font-medium">No departments yet</p>
      <p class="text-xs mt-1">Create your first department to get started.</p>
    </div>

    <!-- Table -->
    <div v-else class="w-full">
      <table class="w-full text-xs table-fixed">
        <!-- Explicit column widths — evenly balanced -->
        <colgroup>
          <col style="width: 22%" />   <!-- Name -->
          <col style="width: 38%" />   <!-- Description -->
          <col style="width: 16%" />   <!-- Status -->
          <col class="hidden md:table-column" style="width: 16%" /> <!-- Created -->
          <col style="width: 15%" />    <!-- Actions -->
        </colgroup>

        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Name</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Description</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Status</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Created</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
          <tr
            v-for="dept in departments"
            :key="dept.id"
            class="hover:bg-gray-50/50 transition-colors align-top"
          >
            <!-- Name -->
            <td class="px-2 sm:px-4 py-2 sm:py-3">
              <div class="flex items-start gap-1.5">
                <div class="w-5 h-5 sm:w-6 sm:h-6 mt-0.5 rounded-md bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
                  <Building2 class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-[#004795]" />
                </div>
                <span class="font-semibold text-[10px] sm:text-xs text-gray-800 leading-snug break-words min-w-0">
                  {{ dept.name }}
                </span>
              </div>
            </td>

            <!-- Description -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-[10px] sm:text-xs text-gray-500 leading-relaxed break-words">
              {{ dept.description || '—' }}
            </td>

            <!-- Status -->
            <td class="px-2 sm:px-4 py-2 sm:py-3">
              <span
                :class="dept.is_active
                  ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                  : 'bg-gray-50 text-gray-500 border-gray-200'"
                class="inline-flex items-center gap-1 text-[9px] sm:text-[10px] font-semibold px-1.5 sm:px-2 py-0.5 rounded-full border whitespace-nowrap"
              >
                <span :class="dept.is_active ? 'bg-emerald-500' : 'bg-gray-400'"
                  class="w-1.5 h-1.5 rounded-full flex-shrink-0" />
                {{ dept.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>

            <!-- Created -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-[10px] sm:text-xs text-gray-400 whitespace-nowrap">
              {{ formatDate(dept.created_at) }}
            </td>

            <!-- Actions -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-right">
              <div class="relative inline-block" @click.stop>
                <button
                  @click="toggleMenu(dept.id)"
                  class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                >
                  <MoreVertical class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </button>
                <div
                  v-if="openMenuId === dept.id"
                  class="absolute right-0 mt-1 w-32 bg-white border border-gray-100 rounded-xl shadow-lg z-30 py-1"
                >
                  <button
                    @click="emit('edit', dept); closeMenu()"
                    class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition"
                  >
                    <Pencil class="w-3.5 h-3.5 text-[#004795]" /> Edit
                  </button>
                  <button
                    @click="emit('delete', dept); closeMenu()"
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
import { Building2, Pencil, Trash2, MoreVertical } from "lucide-vue-next";

defineProps({ departments: { type: Array, default: () => [] } });
const emit = defineEmits(["edit", "delete"]);

const openMenuId = ref(null);

function toggleMenu(id) {
  openMenuId.value = openMenuId.value === id ? null : id;
}
function closeMenu() {
  openMenuId.value = null;
}
function handleOutsideClick() {
  openMenuId.value = null;
}

onMounted(() => document.addEventListener("click", handleOutsideClick));
onUnmounted(() => document.removeEventListener("click", handleOutsideClick));

function formatDate(val) {
  if (!val) return "—";
  return new Date(val).toLocaleDateString("en-ET", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
}
</script>
