<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <h2 class="text-sm font-bold text-gray-800">Facilities</h2>
      <span class="text-xs text-gray-400 font-medium">{{ facilities.length }} total</span>
    </div>

    <div v-if="facilities.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
      <Layers class="w-10 h-10 mb-3 text-gray-300" />
      <p class="text-sm font-medium">No facilities yet</p>
      <p class="text-xs mt-1">Add your first facility to get started.</p>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead>
          <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
            <th class="px-5 py-3 font-semibold">Name</th>
            <th class="px-5 py-3 font-semibold">Type</th>
            <th class="px-5 py-3 font-semibold">Status</th>
            <th class="px-5 py-3 font-semibold">Description</th>
            <th class="px-5 py-3 font-semibold text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr
            v-for="facility in facilities"
            :key="facility.id"
            class="hover:bg-gray-50/60 transition-colors"
          >
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                  <Layers class="w-3.5 h-3.5 text-blue-500" />
                </div>
                <span class="font-semibold text-gray-800">{{ facility.name }}</span>
              </div>
            </td>
            <td class="px-5 py-3.5">
              <span class="text-xs font-medium text-gray-600 capitalize bg-gray-100 px-2 py-0.5 rounded-md">
                {{ facility.type }}
              </span>
            </td>
            <td class="px-5 py-3.5">
              <span
                :class="statusClass(facility.status)"
                class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-0.5 rounded-full border"
              >
                <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass(facility.status)" />
                {{ facility.status }}
              </span>
            </td>
            <td class="px-5 py-3.5 text-gray-500 max-w-xs truncate">
              {{ facility.description || '—' }}
            </td>
            <td class="px-5 py-3.5 text-right">
              <div class="flex items-center justify-end gap-2">
                <button
                  @click="$emit('edit', facility)"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-[#004795] hover:bg-[#004795]/10 transition"
                  title="Edit"
                >
                  <Pencil class="w-3.5 h-3.5" />
                </button>
                <button
                  @click="$emit('delete', facility)"
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
import { Layers, Pencil, Trash2 } from "lucide-vue-next";

defineProps({
  facilities: { type: Array, default: () => [] },
});
defineEmits(["edit", "delete"]);

function statusClass(status) {
  return {
    available: "bg-emerald-50 text-emerald-700 border-emerald-200",
    occupied: "bg-blue-50 text-blue-700 border-blue-200",
    maintenance: "bg-amber-50 text-amber-700 border-amber-200",
    reserved: "bg-purple-50 text-purple-700 border-purple-200",
  }[status] ?? "bg-gray-50 text-gray-500 border-gray-200";
}

function statusDotClass(status) {
  return {
    available: "bg-emerald-500",
    occupied: "bg-blue-500",
    maintenance: "bg-amber-500",
    reserved: "bg-purple-500",
  }[status] ?? "bg-gray-400";
}
</script>
