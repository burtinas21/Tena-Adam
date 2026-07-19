<template>
  <div class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.01)] overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">Department Performance</h3>
      <slot name="actions" />
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="p-5 space-y-3">
      <div v-for="n in 5" :key="n" class="h-10 bg-slate-100 rounded-lg animate-pulse" />
    </div>

    <!-- Empty state -->
    <div v-else-if="!departments.length" class="p-10 flex flex-col items-center justify-center text-center">
      <BarChart3 class="w-8 h-8 text-slate-300 mb-2" />
      <p class="text-xs font-semibold text-slate-400">No department data available</p>
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-xs">
        <thead>
          <tr class="border-b border-slate-100 bg-slate-50/50">
            <th class="text-left px-5 py-3 font-bold text-slate-500 tracking-wide">Department</th>
            <th class="text-right px-4 py-3 font-bold text-slate-500 tracking-wide">Doctors</th>
            <th class="text-right px-4 py-3 font-bold text-slate-500 tracking-wide">Appointments</th>
            <th class="text-right px-4 py-3 font-bold text-slate-500 tracking-wide">Completed</th>
            <th class="text-right px-4 py-3 font-bold text-slate-500 tracking-wide">Patients Served</th>
            <th class="text-right px-5 py-3 font-bold text-slate-500 tracking-wide">Avg Rating</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <tr
            v-for="dept in departments"
            :key="dept.department_id"
            class="hover:bg-slate-50/60 transition-colors"
          >
            <td class="px-5 py-3.5">
              <span class="font-semibold text-slate-800">{{ dept.department_name }}</span>
            </td>
            <td class="px-4 py-3.5 text-right font-semibold text-slate-600">
              {{ dept.total_doctors }}
            </td>
            <td class="px-4 py-3.5 text-right font-semibold text-slate-600">
              {{ dept.total_appointments.toLocaleString() }}
            </td>
            <td class="px-4 py-3.5 text-right">
              <span class="inline-flex items-center gap-1 font-semibold text-emerald-600">
                <span>{{ dept.completed_consultations.toLocaleString() }}</span>
                <span
                  v-if="dept.total_appointments > 0"
                  class="text-[10px] text-slate-400"
                >
                  ({{ Math.round((dept.completed_consultations / dept.total_appointments) * 100) }}%)
                </span>
              </span>
            </td>
            <td class="px-4 py-3.5 text-right font-semibold text-slate-600">
              {{ dept.patients_served.toLocaleString() }}
            </td>
            <td class="px-5 py-3.5 text-right">
              <div class="flex items-center justify-end gap-1">
                <Star class="w-3 h-3 text-amber-400 fill-amber-400" />
                <span class="font-bold text-slate-700">{{ dept.average_rating || '–' }}</span>
                <span class="text-slate-400 text-[10px]">({{ dept.total_reviews }})</span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { BarChart3, Star } from 'lucide-vue-next';

defineProps({
  departments: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
});
</script>
