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
    <div v-else>
      <table class="w-full table-fixed text-[10px] sm:text-xs">
        <colgroup>
          <col style="width:30%" />
          <col style="width:20%" />
          <col style="width:13%" />
          <col style="width:13%" />
          <col style="width:13%" />
          <col style="width:11%" />
        </colgroup>
        <thead>
          <tr class="border-b border-slate-100 bg-slate-50/50">
            <th class="text-left px-1.5 sm:px-5 py-2 sm:py-3 font-bold text-slate-500 tracking-wide">Doctor</th>
            <th class="text-left px-1 sm:px-4 py-2 sm:py-3 font-bold text-slate-500 tracking-wide">Dept</th>
            <th class="text-right px-1 sm:px-4 py-2 sm:py-3 font-bold text-slate-500 tracking-wide">Appts</th>
            <th class="text-right px-1 sm:px-4 py-2 sm:py-3 font-bold text-slate-500 tracking-wide">Done</th>
            <th class="text-right px-1 sm:px-4 py-2 sm:py-3 font-bold text-slate-500 tracking-wide">Tel</th>
            <th class="text-right px-1.5 sm:px-5 py-2 sm:py-3 font-bold text-slate-500 tracking-wide">★</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <tr
            v-for="doc in doctors"
            :key="doc.doctor_id"
            class="hover:bg-slate-50/60 transition-colors"
          >
            <td class="px-1.5 sm:px-5 py-1.5 sm:py-3.5">
              <div class="flex items-center gap-1 sm:gap-2.5">
                <div class="w-5 h-5 sm:w-7 sm:h-7 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                  <span class="text-[8px] sm:text-[10px] font-bold text-blue-600">
                    {{ initials(doc.doctor_name) }}
                  </span>
                </div>
                <span class="font-semibold text-slate-800 truncate leading-tight">{{ doc.doctor_name }}</span>
              </div>
            </td>
            <td class="px-1 sm:px-4 py-1.5 sm:py-3.5 text-slate-500 truncate">{{ doc.department ?? '—' }}</td>
            <td class="px-1 sm:px-4 py-1.5 sm:py-3.5 text-right font-semibold text-slate-600">
              {{ doc.total_appointments }}
            </td>
            <td class="px-1 sm:px-4 py-1.5 sm:py-3.5 text-right font-semibold text-emerald-600">
              {{ doc.completed_encounters }}
            </td>
            <td class="px-1 sm:px-4 py-1.5 sm:py-3.5 text-right font-semibold text-indigo-600">
              {{ doc.telehealth_sessions }}
            </td>
            <td class="px-1.5 sm:px-5 py-1.5 sm:py-3.5 text-right">
              <div class="flex items-center justify-end gap-0.5">
                <Star class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-amber-400 fill-amber-400 flex-shrink-0" />
                <span class="font-bold text-slate-700">
                  {{ doc.average_rating > 0 ? doc.average_rating : '–' }}
                </span>
              </div>
              <span class="text-[9px] sm:text-[10px] text-slate-400 block text-right">({{ doc.total_reviews }})</span>
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
