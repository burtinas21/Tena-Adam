<script setup>
const networks = [
  {
    name: "Black Lion Hospital",
    code: "SCH-001",
    location: "Addis Ababa",
    region: "Central Region",
    doctors: "452",
    patients: "12,450",
    status: "Active",
    statusClass: "bg-emerald-50 text-emerald-700 border-emerald-200",
  },
  {
    name: "St. Paul's Hospital",
    code: "SCH-012",
    location: "Addis Ababa",
    region: "Central Region",
    doctors: "310",
    patients: "8,920",
    status: "Active",
    statusClass: "bg-emerald-50 text-emerald-700 border-emerald-200",
  },
  {
    name: "Gondar University Clinic",
    code: "SCH-045",
    location: "Gondar",
    region: "Amhara",
    doctors: "0",
    patients: "0",
    status: "Suspended",
    statusClass:
      "bg-rose-50 text-rose-700 border-rose-200 line-through text-gray-400",
  },
];
</script>

<template>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr
          class="border-b border-gray-200 text-[11px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50"
        >
          <th class="py-3 px-4">Hospital</th>
          <th class="py-3 px-4">Code</th>
          <th class="py-3 px-4">Location</th>
          <th class="py-3 px-4">Doctors</th>
          <th class="py-3 px-4">Patients</th>
          <th class="py-3 px-4">Status</th>
          <th class="py-3 px-4 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 text-xs">
        <tr
          v-for="net in networks"
          :key="net.code"
          :class="{ 'opacity-75 bg-slate-50/30': net.status === 'Suspended' }"
          class="hover:bg-slate-50/40 transition-colors"
        >
          <!-- Hospital Name with mini Icon structure -->
          <td class="py-3.5 px-4">
            <div class="flex items-center gap-3">
              <div
                :class="
                  net.status === 'Suspended'
                    ? 'bg-slate-100 text-slate-400'
                    : 'bg-blue-50 text-blue-600'
                "
                class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
              >
                <svg
                  xmlns="http://w3.org"
                  width="15"
                  height="15"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M3 21h18" />
                  <path d="M3 7v1a3 3 0 0 0 6 0v-1H3Z" />
                  <path d="M9 7v1a3 3 0 0 0 6 0v-1H9Z" />
                  <path d="M15 7v1a3 3 0 0 0 6 0v-1h-6Z" />
                  <path d="M19 21V11" />
                  <path d="M5 21V11" />
                </svg>
              </div>
              <span
                :class="{
                  'text-gray-400': net.status === 'Suspended',
                  'text-gray-800 font-bold': net.status !== 'Suspended',
                }"
                class="font-bold"
                >{{ net.name }}</span
              >
            </div>
          </td>

          <!-- Code Cell Row -->
          <td class="py-3.5 px-4 text-gray-500 font-semibold">
            {{ net.code }}
          </td>

          <!-- Location breakdown line data -->
          <td class="py-3.5 px-4">
            <div
              :class="{
                'text-gray-400': net.status === 'Suspended',
                'text-gray-700': net.status !== 'Suspended',
              }"
              class="font-bold"
            >
              {{ net.location }}
            </div>
            <div class="text-[10px] text-gray-400 font-medium mt-0.5">
              {{ net.region }}
            </div>
          </td>

          <!-- Provider headcount metrics -->
          <td class="py-3.5 px-4 text-gray-600 font-bold tracking-tight">
            {{ net.doctors }}
          </td>

          <!-- Total Patient payload counts metrics -->
          <td class="py-3.5 px-4 text-gray-600 font-bold tracking-tight">
            {{ net.patients }}
          </td>

          <!-- Dynamic Status badge triggers -->
          <td class="py-3.5 px-4">
            <span
              :class="net.statusClass"
              class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-bold rounded-full border"
            >
              <span class="w-1 h-1 rounded-full bg-current"></span>
              {{ net.status }}
            </span>
          </td>

          <!-- Action list trigger buttons -->
          <td class="py-3.5 px-4 text-right">
            <button
              class="p-1.5 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100 transition-colors"
            >
              <svg
                xmlns="http://w3.org"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="12" cy="12" r="1" />
                <circle cx="12" cy="5" r="1" />
                <circle cx="12" cy="19" r="1" />
              </svg>
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination Actions Footer Module Panel footer -->
    <div
      class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400 font-semibold"
    >
      <div>Showing 1 to 3 of 124 entries</div>
      <div class="flex items-center gap-1">
        <button
          class="p-1 border border-gray-200 rounded-md bg-white hover:bg-gray-50 transition-colors"
        >
          <svg
            xmlns="http://w3.org"
            width="12"
            height="12"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <polyline points="15 18 9 12 15 6" />
          </svg>
        </button>
        <button
          class="w-6 h-6 bg-blue-600 text-white rounded-md font-bold shadow-sm flex items-center justify-center"
        >
          1
        </button>
        <button
          class="w-6 h-6 border border-gray-200 rounded-md bg-white text-gray-500 hover:bg-gray-50 flex items-center justify-center transition-colors"
        >
          2
        </button>
        <button
          class="w-6 h-6 border border-gray-200 rounded-md bg-white text-gray-500 hover:bg-gray-50 flex items-center justify-center transition-colors"
        >
          3
        </button>
        <span class="px-1 text-gray-300">...</span>
        <button
          class="w-6 h-6 border border-gray-200 rounded-md bg-white text-gray-500 hover:bg-gray-50 flex items-center justify-center transition-colors"
        >
          13
        </button>
        <button
          class="p-1 border border-gray-200 rounded-md bg-white hover:bg-gray-50 transition-colors"
        >
          <svg
            xmlns="http://w3.org"
            width="12"
            height="12"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <polyline points="9 18 15 12 9 6" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>
