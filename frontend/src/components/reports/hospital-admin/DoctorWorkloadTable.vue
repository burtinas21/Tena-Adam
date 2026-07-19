<template>
  <div class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.01)] overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">Doctor Workload</h3>
      <slot name="actions" />
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="p-5 space-y-3">
      <div v-for="n in 5" :key="n" class="h-10 bg-slate-100 rounded-lg animate-pulse" />
    </div>

    <!-- Empty state -->
    <div v-else-if="!doctors.length" class="p-10 flex flex-col items-center justify-center text-center">
      <Users class="w-8 h-8 text-slate-300 mb-2" />
      <p class="text-xs font-semibold text-slate-400">No doctor data available</p>
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-xs">
        <thead>
          <tr class="border-b border-slate-100 bg-slate-50/50">
            <th class="text-left px-5 py-3 font-bold text-slate-500 tracking-wide">Doctor</th>
            <th class="text-left px-4 py-3 font-bold text-slate-500 tracking-wide">Department</th>
            <th class="text-right px-4 py-3 font-bold text-slate-500 tracking-wide">Appointments</th>
            <th class="text-right px-4 py-3 font-bold text-slate-500 tracking-wide">Completed</th>
            <th class="text-right px-4 py-3 font-bold text-slate-500 tracking-wide">Telehealth</th>
            <th class="text-right px-5 py-3 font-bold text-slate-500 tracking-wide">Rating</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <tr
            v-for="doc in doctors"
            :key="doc.doctor_id"
            class="hover:bg-slate-50/60 transition-colors"
          >
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                  <span class="text-[10px] font-bold text-blue-600">
                    {{ initials(doc.doctor_name) }}
                  </span>
                </div>
                <span class="font-semibold text-slate-800">{{ doc.doctor_name }}</span>
              </div>
            </td>
            <td class="px-4 py-3.5 text-slate-500">{{ doc.department ?? '—' }}</td>
            <td class="px-4 py-3.5 text-right font-semibold text-slate-600">
              {{ doc.total_appointments }}
            </td>
            <td class="px-4 py-3.5 text-right font-semibold text-emerald-600">
              {{ doc.completed_encounters }}
            </td>
            <td class="px-4 py-3.5 text-right font-semibold text-indigo-600">
              {{ doc.telehealth_sessions }}
            </td>
            <td class="px-5 py-3.5 text-right">
              <div class="flex items-center justify-end gap-1">
                <Star class="w-3 h-3 text-amber-400 fill-amber-400" />
                <span class="font-bold text-slate-700">
                  {{ doc.average_rating > 0 ? doc.average_rating : '–' }}
                </span>
                <span class="text-slate-400 text-[10px]">({{ doc.total_reviews }})</span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { Users, Star } from 'lucide-vue-next';

defineProps({
  doctors: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

function initials(name = '') {
  return name
    .split(' ')
    .slice(0, 2)
    .map((n) => n[0])
    .join('')
    .toUpperCase();
}
</script>
