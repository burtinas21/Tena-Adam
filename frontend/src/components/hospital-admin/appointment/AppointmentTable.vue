<script setup>
const appointments = [
  {
    id: "#APT-4091",
    patient: "Eleanor Vance",
    avatar: "https://unsplash.com",
    doctor: "Dr. Sarah Jenkins",
    dept: "Cardiology",
    dateTime: "Oct 24, 2023 09:00 AM",
    type: "In-person",
    status: "Confirmed",
  },
  {
    id: "#APT-4092",
    patient: "Marcus Thorne",
    avatar: "initials-mr",
    doctor: "Dr. Michael Chen",
    dept: "Neurology",
    dateTime: "Oct 24, 2023 09:30 AM",
    type: "Telehealth",
    status: "Pending",
  },
  {
    id: "#APT-4088",
    patient: "James Wilson",
    avatar: "https://unsplash.com",
    doctor: "Dr. Emily Stanton",
    dept: "Orthopedics",
    dateTime: "Oct 24, 2023 08:00 AM",
    type: "In-person",
    status: "Completed",
  },
];
</script>

<template>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr
          class="border-b border-gray-200 text-xs font-semibold text-gray-400 uppercase bg-gray-50/50"
        >
          <th class="py-3 px-4">Apt ID</th>
          <th class="py-3 px-4">Patient</th>
          <th class="py-3 px-4">Doctor & Dept</th>
          <th class="py-3 px-4">Date & Time</th>
          <th class="py-3 px-4">Type</th>
          <th class="py-3 px-4">Status</th>
          <th class="py-3 px-4 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 text-xs">
        <tr
          v-for="apt in appointments"
          :key="apt.id"
          class="hover:bg-gray-50/50 transition-colors"
        >
          <td class="py-3.5 px-4 font-medium text-gray-400">{{ apt.id }}</td>
          <td class="py-3.5 px-4">
            <div class="flex items-center gap-3">
              <div
                v-if="apt.avatar === 'initials-mr'"
                class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-[10px] flex items-center justify-center tracking-wider"
              >
                MR
              </div>
              <img
                v-else
                :src="apt.avatar"
                class="w-7 h-7 rounded-full object-cover grayscale-[20%]"
                alt=""
              />
              <span class="font-semibold text-gray-700">{{ apt.patient }}</span>
            </div>
          </td>
          <td class="py-3.5 px-4">
            <div class="font-semibold text-gray-700">{{ apt.doctor }}</div>
            <div class="text-[10px] text-gray-400 font-medium mt-0.5">
              {{ apt.dept }}
            </div>
          </td>
          <td class="py-3.5 px-4 text-gray-500 font-medium">
            {{ apt.dateTime }}
          </td>
          <td class="py-3.5 px-4 text-gray-500 font-medium">
            <span class="flex items-center gap-1.5">
              <span v-if="apt.type === 'In-person'" class="text-gray-400">
                <svg
                  xmlns="http://w3.org"
                  width="13"
                  height="13"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
              </span>
              <span v-else class="text-gray-400">
                <svg
                  xmlns="http://w3.org"
                  width="13"
                  height="13"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <rect width="18" height="12" x="3" y="4" rx="2" ry="2" />
                  <line x1="2" x2="22" y1="20" y2="20" />
                  <line x1="12" x2="12" y1="16" y2="20" />
                </svg>
              </span>
              {{ apt.type }}
            </span>
          </td>
          <td class="py-3.5 px-4">
            <span
              :class="{
                'bg-emerald-50 text-emerald-700 border-emerald-200/60':
                  apt.status === 'Confirmed',
                'bg-blue-50 text-blue-700 border-blue-200/60':
                  apt.status === 'Pending',
                'bg-gray-50 text-gray-500 border-gray-200':
                  apt.status === 'Completed',
              }"
              class="px-2 py-0.5 text-[11px] font-semibold rounded-md border"
            >
              {{ apt.status }}
            </span>
          </td>
          <td class="py-3.5 px-4 text-right">
            <div class="flex items-center justify-end gap-1.5">
              <button
                v-if="apt.status === 'Pending'"
                class="bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-medium px-3 py-1 rounded-md shadow-sm transition-colors"
              >
                Approve
              </button>
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
                  <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
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
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination Footer -->
    <div
      class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400 font-medium"
    >
      <div>Showing 1 to 3 of 142 entries</div>
      <div class="flex items-center gap-1">
        <button
          class="px-2.5 py-1.5 border border-gray-200 rounded-md bg-white text-gray-500 hover:bg-gray-50 transition-colors"
        >
          Prev
        </button>
        <button
          class="px-3 py-1.5 bg-blue-600 text-white rounded-md font-semibold shadow-sm"
        >
          1
        </button>
        <button
          class="px-3 py-1.5 border border-gray-200 rounded-md bg-white text-gray-500 hover:bg-gray-50 transition-colors"
        >
          2
        </button>
        <button
          class="px-3 py-1.5 border border-gray-200 rounded-md bg-white text-gray-500 hover:bg-gray-50 transition-colors"
        >
          3
        </button>
        <button
          class="px-2.5 py-1.5 border border-gray-200 rounded-md bg-white text-gray-500 hover:bg-gray-50 transition-colors"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>
