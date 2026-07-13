<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <!-- Table header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <h2 class="text-sm font-bold text-gray-800">Departments</h2>
      <span class="text-xs text-gray-400 font-medium">
        {{ departments.length }} total
      </span>
    </div>

    <!-- Empty state -->
    <div
      v-if="departments.length === 0"
      class="flex flex-col items-center justify-center py-16 text-gray-400"
    >
      <Building2 class="w-10 h-10 mb-3 text-gray-300" />
      <p class="text-sm font-medium">No departments yet</p>
      <p class="text-xs mt-1">Create your first department to get started.</p>
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead>
          <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
            <th class="px-5 py-3 font-semibold">Name</th>
            <th class="px-5 py-3 font-semibold">Description</th>
            <th class="px-5 py-3 font-semibold">Status</th>
            <th class="px-5 py-3 font-semibold text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr
            v-for="dept in departments"
            :key="dept.id"
            class="hover:bg-gray-50/60 transition-colors"
          >
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
                  <Building2 class="w-3.5 h-3.5 text-[#004795]" />
                </div>
                <span class="font-semibold text-gray-800">{{ dept.name }}</span>
              </div>
            </td>
            <td class="px-5 py-3.5 text-gray-500 max-w-xs truncate">
              {{ dept.description || '—' }}
            </td>
            <td class="px-5 py-3.5">
              <span
                :class="dept.is_active
                  ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                  : 'bg-gray-50 text-gray-500 border border-gray-200'"
                class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-0.5 rounded-full"
              >
                <span
                  :class="dept.is_active ? 'bg-emerald-500' : 'bg-gray-400'"
                  class="w-1.5 h-1.5 rounded-full"
                />
                {{ dept.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-5 py-3.5 text-right">
              <div class="flex items-center justify-end gap-2">
                <button
                  @click="$emit('edit', dept)"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-[#004795] hover:bg-[#004795]/10 transition"
                  title="Edit"
                >
                  <Pencil class="w-3.5 h-3.5" />
                </button>
                <button
                  @click="$emit('delete', dept)"
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
import { Building2, Pencil, Trash2 } from "lucide-vue-next";

defineProps({
  departments: {
    type: Array,
    default: () => [],
  },
});

defineEmits(["edit", "delete"]);
</script>
