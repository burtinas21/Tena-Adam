<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_2px_rgba(0,0,0,0.01)] p-5 flex flex-col h-[300px]"
  >
    <!-- Header -->
    <div class="flex items-center justify-between pb-3 flex-shrink-0">
      <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">
        Top Hospitals by Volume
      </h3>
    </div>

    <!-- Loading -->
    <div v-if="store.loading" class="flex-1 flex items-center justify-center">
      <div class="w-full h-40 bg-slate-100 rounded-lg animate-pulse" />
    </div>

    <!-- Empty -->
    <div v-else-if="!hospitals.length" class="flex-1 flex items-center justify-center">
      <span class="text-xs text-slate-400 font-medium">No hospital data available</span>
    </div>

    <!-- Table -->
    <div v-else class="overflow-y-auto flex-1 mt-1">
      <table
        class="w-full text-left border-collapse text-xs font-medium text-slate-600"
      >
        <thead class="sticky top-0 bg-white">
          <tr
            class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider"
          >
            <th class="pb-2.5 font-semibold">Hospital Name</th>
            <th class="pb-2.5 font-semibold">City</th>
            <th class="pb-2.5 font-semibold">Patient Vol.</th>
            <th class="pb-2.5 text-right pr-4 font-semibold">
              Completion Rate
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <tr v-for="hospital in hospitals" :key="hospital.hospital_id">
            <td class="py-2.5 font-bold text-slate-800 tracking-tight pr-2">
              {{ hospital.hospital_name }}
            </td>
            <td class="py-2.5 text-slate-500">{{ hospital.region || 'N/A' }}</td>
            <td class="py-2.5 font-semibold text-slate-700">
              {{ formatNumber(hospital.patient_volume) }}
            </td>
            <td class="py-2.5">
              <div class="flex items-center justify-end space-x-2.5 pr-2">
                <span class="font-bold text-slate-800 text-[11px]">
                  {{ hospital.completion_rate }}%
                </span>
                <div class="w-16 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all duration-500"
                    :class="completionColor(hospital.completion_rate)"
                    :style="{ width: hospital.completion_rate + '%' }"
                  ></div>
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
import { computed } from 'vue';
import { useReportStore } from '../../stores/reportStore';

const store = useReportStore();

const hospitals = computed(() => store.topHospitals ?? []);

function formatNumber(val) {
  if (val === null || val === undefined) return '—';
  return Number(val).toLocaleString();
}

function completionColor(rate) {
  if (rate >= 85) return 'bg-emerald-500';
  if (rate >= 60) return 'bg-amber-400';
  return 'bg-red-400';
}
</script>
