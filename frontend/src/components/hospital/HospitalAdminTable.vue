<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <h2 class="text-sm font-bold text-gray-800">Hospital Admins</h2>
      <span class="text-xs text-gray-400 font-medium">{{ admins.length }} total</span>
    </div>

    <div v-if="admins.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
      <UserCog class="w-10 h-10 mb-3 text-gray-300" />
      <p class="text-sm font-medium">No hospital admins yet</p>
      <p class="text-xs mt-1">Add an admin to manage a hospital.</p>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead>
          <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
            <th class="px-5 py-3 font-semibold">Name</th>
            <th class="px-5 py-3 font-semibold">Email</th>
            <th class="px-5 py-3 font-semibold">Phone</th>
            <th class="px-5 py-3 font-semibold">Status</th>
            <th class="px-5 py-3 font-semibold text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr
            v-for="admin in admins"
            :key="admin.id"
            class="hover:bg-gray-50/60 transition-colors"
            :class="{ 'opacity-50': !admin.is_active }"
          >
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
                  <span class="text-xs font-bold text-[#004795]">{{ initials(admin) }}</span>
                </div>
                <span class="font-semibold text-gray-800">{{ fullName(admin) }}</span>
              </div>
            </td>
            <td class="px-5 py-3.5 text-gray-600">{{ admin.email }}</td>
            <td class="px-5 py-3.5 text-gray-600">{{ admin.phone || '—' }}</td>
            <td class="px-5 py-3.5">
              <span
                :class="admin.is_active
                  ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                  : 'bg-gray-50 text-gray-500 border-gray-200'"
                class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-0.5 rounded-full border"
              >
                <span :class="admin.is_active ? 'bg-emerald-500' : 'bg-gray-400'" class="w-1.5 h-1.5 rounded-full" />
                {{ admin.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-5 py-3.5 text-right">
              <div class="flex items-center justify-end gap-1">
                <!-- Edit -->
                <button
                  @click="$emit('edit', admin)"
                  title="Edit"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-[#004795] hover:bg-[#004795]/10 transition"
                >
                  <Pencil class="w-3.5 h-3.5" />
                </button>
                <!-- Delete -->
                <button
                  @click="$emit('delete', admin)"
                  title="Delete"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
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
import { UserCog, Pencil, Trash2 } from "lucide-vue-next";

defineProps({
  admins: { type: Array, default: () => [] },
});
defineEmits(["edit", "delete"]);

function fullName(a) {
  return `${a.first_name ?? ""} ${a.last_name ?? ""}`.trim();
}
function initials(a) {
  return ((a.first_name?.[0] ?? "") + (a.last_name?.[0] ?? "")).toUpperCase() || "?";
}
</script>
