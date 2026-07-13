<template>
  <div
    class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden flex flex-col h-full"
  >
    <!-- Table Toolbar Header -->
    <div
      class="p-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 bg-white"
    >
      <div class="relative w-full max-w-xs">
        <span
          class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
          </svg>
        </span>
        <input
          type="text"
          placeholder="Filter patients..."
          class="w-full pl-9 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-blue-500 bg-gray-50/50"
        />
      </div>

      <div class="flex gap-2">
        <select
          class="p-1.5 border border-gray-200 rounded-lg text-xs text-gray-600 bg-white focus:outline-none"
        >
          <option>All Statuses</option>
        </select>
        <select
          class="p-1.5 border border-gray-200 rounded-lg text-xs text-gray-600 bg-white focus:outline-none"
        >
          <option>All Types</option>
        </select>
      </div>
    </div>

    <!-- Core Table Grid Platform -->
    <div class="overflow-x-auto flex-1">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr
            class="border-b border-gray-100 bg-slate-50/50 text-[11px] font-bold text-gray-400 uppercase tracking-wider"
          >
            <th class="py-3 px-4 font-semibold">Time</th>
            <th class="py-3 px-4 font-semibold">Patient</th>
            <th class="py-3 px-4 font-semibold">Department</th>
            <th class="py-3 px-4 font-semibold">Type</th>
            <th class="py-3 px-4 font-semibold">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 text-xs">
          <tr
            v-for="appt in appointments"
            :key="appt.id"
            :class="[
              'hover:bg-slate-50/40 cursor-pointer transition-colors',
              selectedId === appt.id ? 'bg-blue-50/30' : '',
            ]"
            @click="$emit('select-patient', appt)"
          >
            <td class="py-3.5 px-4">
              <p class="font-bold text-gray-800">{{ appt.time }}</p>
              <p class="text-[11px] text-gray-400 mt-0.5">
                {{ appt.duration }}
              </p>
            </td>
            <td class="py-3.5 px-4">
              <div class="flex items-center gap-3">
                <div
                  class="w-8 h-8 rounded-full overflow-hidden bg-slate-100 border border-gray-100 flex items-center justify-center font-semibold text-gray-500 shrink-0"
                >
                  <img
                    v-if="appt.avatar"
                    :src="appt.avatar"
                    :alt="appt.patientName"
                    class="w-full h-full object-cover"
                  />
                  <span v-else class="text-[11px]">{{ appt.initials }}</span>
                </div>
                <div>
                  <p class="font-bold text-gray-800">{{ appt.patientName }}</p>
                  <p
                    class="text-[10px] text-gray-400 font-mono tracking-tight mt-0.5"
                  >
                    ID: {{ appt.patientId }}
                  </p>
                </div>
              </div>
            </td>
            <td class="py-3.5 px-4 text-gray-600 font-medium">
              {{ appt.department }}
            </td>
            <td class="py-3.5 px-4 text-gray-500">
              <span class="inline-flex items-center gap-1">
                <svg
                  v-if="appt.type === 'In-Person'"
                  class="w-3.5 h-3.5 text-gray-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                  />
                </svg>
                <svg
                  v-else
                  class="w-3.5 h-3.5 text-blue-500"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                  />
                </svg>
                {{ appt.type }}
              </span>
            </td>
            <td class="py-3.5 px-4">
              <span
                :class="[
                  'px-2 py-0.5 rounded text-[11px] font-semibold',
                  appt.status === 'Confirmed'
                    ? 'bg-emerald-50 text-emerald-600'
                    : '',
                  appt.status === 'Pending'
                    ? 'bg-orange-50 text-orange-500'
                    : '',
                  appt.status === 'Completed' ? 'bg-blue-50 text-blue-500' : '',
                ]"
              >
                {{ appt.status }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Table Pagination Row Footer -->
    <div
      class="p-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400 bg-white"
    >
      <span>Showing 1-3 of 12 appointments</span>
      <div class="flex gap-1">
        <button
          class="p-1 border border-gray-200 rounded hover:bg-gray-50 text-gray-400"
        >
          <svg
            class="w-3 h-3"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7"
            />
          </svg>
        </button>
        <button
          class="p-1 border border-gray-200 rounded hover:bg-gray-50 text-gray-400"
        >
          <svg
            class="w-3 h-3"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 5l7 7-7 7"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  appointments: Array,
  selectedId: Number,
});
defineEmits(["select-patient"]);
</script>
