<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
      <h2 class="text-sm font-bold text-gray-800">Hospital Admins</h2>
      <span class="text-xs text-gray-400 font-medium">{{ admins.length }} total</span>
    </div>

    <div v-if="admins.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
      <UserCog class="w-10 h-10 mb-3 text-gray-300" />
      <p class="text-sm font-medium">No hospital admins yet</p>
      <p class="text-xs mt-1">Add an admin to manage a hospital.</p>
    </div>

    <!-- Responsive table: fixed layout, tighter padding, no horizontal scroll -->
    <div v-else class="w-full">
      <table class="w-full table-fixed">
        <colgroup>
          <col style="width:28%" />
          <col style="width:30%" />
          <col style="width:18%" />
          <col style="width:14%" />
          <col style="width:10%" />
        </colgroup>
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Name</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Email</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Phone</th>
            <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-left">Status</th>
            <th class="px-1 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr
            v-for="admin in admins"
            :key="admin.id"
            class="hover:bg-gray-50/60 transition-colors align-middle"
            :class="{ 'opacity-50': !admin.is_active }"
          >
            <!-- Name -->
            <td class="px-2 sm:px-4 py-2 sm:py-3">
              <div class="flex items-center gap-1.5">
                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
                  <span class="text-[9px] sm:text-[10px] font-bold text-[#004795]">{{ initials(admin) }}</span>
                </div>
                <span class="font-semibold text-[10px] sm:text-xs text-gray-800 truncate">{{ fullName(admin) }}</span>
              </div>
            </td>
            <!-- Email -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-[10px] sm:text-xs text-gray-500 truncate">{{ admin.email }}</td>
            <!-- Phone -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-[10px] sm:text-xs text-gray-500 truncate">{{ admin.phone || '—' }}</td>
            <!-- Status -->
            <td class="px-2 sm:px-4 py-2 sm:py-3">
              <span
                :class="admin.is_active
                  ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                  : 'bg-gray-50 text-gray-500 border-gray-200'"
                class="inline-flex items-center gap-1 text-[9px] sm:text-[10px] font-semibold px-1.5 sm:px-2 py-0.5 rounded-full border whitespace-nowrap"
              >
                <span :class="admin.is_active ? 'bg-emerald-500' : 'bg-gray-400'" class="w-1.5 h-1.5 rounded-full flex-shrink-0" />
                {{ admin.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <!-- Actions — three-dot dropdown -->
            <td class="px-2 sm:px-4 py-2 sm:py-3 text-right">
              <div class="relative inline-block" @click.stop>
                <button
                  @click="toggleMenu(admin.id)"
                  class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                >
                  <MoreVertical class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </button>
                <div
                  v-if="openMenuId === admin.id"
                  class="absolute right-0 mt-1 w-32 bg-white border border-gray-100 rounded-xl shadow-lg z-30 py-1"
                >
                  <button
                    @click="$emit('edit', admin); closeMenu()"
                    class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition"
                  >
                    <Pencil class="w-3.5 h-3.5 text-[#004795]" /> Edit
                  </button>
                  <button
                    @click="$emit('delete', admin); closeMenu()"
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
import { UserCog, Pencil, Trash2, MoreVertical } from "lucide-vue-next";

defineProps({
  admins: { type: Array, default: () => [] },
});
defineEmits(["edit", "delete"]);

const openMenuId = ref(null);

function toggleMenu(id) {
  openMenuId.value = openMenuId.value === id ? null : id;
}
function closeMenu() { openMenuId.value = null; }
function handleOutsideClick() { openMenuId.value = null; }

onMounted(() => document.addEventListener("click", handleOutsideClick));
onUnmounted(() => document.removeEventListener("click", handleOutsideClick));

function fullName(a) {
  return `${a.first_name ?? ""} ${a.last_name ?? ""}`.trim();
}
function initials(a) {
  return ((a.first_name?.[0] ?? "") + (a.last_name?.[0] ?? "")).toUpperCase() || "?";
}
</script>
