<template>
  <div class="bg-white border border-gray-200/90 rounded-2xl p-5 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-bold text-gray-800 tracking-tight">Live Registry</h3>
      <div class="text-[11px] text-gray-400 font-semibold">
        {{ activeCount }} active
      </div>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="space-y-2">
      <div v-for="n in 4" :key="n" class="h-12 bg-gray-50 rounded-lg animate-pulse" />
    </div>

    <!-- Empty -->
    <div v-else-if="!entries.length" class="py-10 flex flex-col items-center text-gray-400">
      <svg class="w-8 h-8 mb-2 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
      </svg>
      <p class="text-xs font-medium">No queue entries for this date</p>
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wide">
            <th class="py-3 px-2">#</th>
            <th class="py-3 px-2">Patient</th>
            <th class="py-3 px-2 hidden md:table-cell">Doctor & Dept</th>
            <th class="py-3 px-2 hidden sm:table-cell">Type</th>
            <th class="py-3 px-2">Status</th>
          </tr>
        </thead>
        <tbody class="text-xs divide-y divide-gray-50">
          <tr v-for="entry in entries" :key="entry.id"
            class="hover:bg-slate-50/50 transition-colors"
            :class="{ 'bg-blue-50/30': entry.status === 'in_consultation' }">
            <td class="py-3.5 px-2 font-bold text-blue-600/90">#{{ entry.queue_number }}</td>
            <td class="py-3.5 px-2">
              <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold flex items-center justify-center tracking-wider flex-shrink-0">
                  {{ initials(entry) }}
                </div>
                <div>
                  <span class="font-bold text-gray-800">{{ patientName(entry) }}</span>
                  <p v-if="entry.walk_in_phone" class="text-[10px] text-gray-400">{{ entry.walk_in_phone }}</p>
                </div>
              </div>
            </td>
            <td class="py-3.5 px-2 hidden md:table-cell">
              <div class="font-bold text-gray-700">{{ entry._doctorName ?? '—' }}</div>
              <div class="text-[10px] text-gray-400 font-medium mt-0.5">{{ entry._department ?? '—' }}</div>
            </td>
            <td class="py-3.5 px-2 hidden sm:table-cell">
              <span class="text-[10px] font-semibold px-2 py-0.5 rounded"
                :class="entry.appointment_id ? 'bg-blue-50 text-blue-700' : 'bg-orange-50 text-orange-700'">
                {{ entry.appointment_id ? 'Appointment' : 'Walk-in' }}
              </span>
            </td>
            <td class="py-3.5 px-2">
              <span :class="statusClass(entry.status)"
                class="inline-flex items-center gap-1 px-2.5 py-0.5 font-bold rounded-full border text-[10px] capitalize">
                <span v-if="entry.status !== 'skipped' && entry.status !== 'no_show'"
                  class="w-1.5 h-1.5 rounded-full bg-current"></span>
                {{ entry.status?.replace("_", " ") }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Footer count -->
    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 text-[11px] text-gray-400 font-semibold">
      <div>Showing {{ entries.length }} queue entries</div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  entries: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
});

const activeCount = props.entries.filter(
  (e) => e.status === "waiting" || e.status === "in_consultation"
).length;

function patientName(entry) {
  if (entry.walk_in_patient_name) return entry.walk_in_patient_name;
  const u = entry.appointment?.patient?.user;
  if (u) return `${u.first_name ?? ""} ${u.last_name ?? ""}`.trim();
  return `Patient #${entry.queue_number}`;
}

function initials(entry) {
  const name = patientName(entry);
  const parts = name.split(" ");
  return ((parts[0]?.[0] ?? "") + (parts[1]?.[0] ?? "")).toUpperCase() || "#";
}

function statusClass(status) {
  return {
    waiting:          "bg-amber-50 text-amber-700 border-amber-200",
    in_consultation:  "bg-teal-50 text-teal-700 border-teal-200",
    completed:        "bg-blue-50 text-blue-700 border-blue-200",
    skipped:          "bg-gray-100 text-gray-500 border-transparent",
    no_show:          "bg-gray-100 text-gray-400 border-transparent",
  }[status] ?? "bg-gray-100 text-gray-500 border-transparent";
}
</script>
