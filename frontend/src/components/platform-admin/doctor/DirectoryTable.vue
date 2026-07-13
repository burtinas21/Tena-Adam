<script setup>
const doctors = [
  {
    name: "Dr. Sarah Jenkins",
    specialty: "Cardiology",
    avatar: "https://unsplash.com",
    dept: "Cardiovascular Dept",
    deptClass: "bg-blue-50 text-blue-700 border-blue-100",
    license: "LIC-84920",
    experience: "12 Years",
    status: "Online",
    statusClass: "bg-emerald-500",
    telehealth: true,
  },
  {
    name: "Dr. Marcus Chen",
    specialty: "Neurology",
    avatar: "https://unsplash.com",
    dept: "Neuroscience Center",
    deptClass: "bg-purple-50 text-purple-700 border-purple-100",
    license: "LIC-55102",
    experience: "8 Years",
    status: "Offline",
    statusClass: "bg-gray-400",
    telehealth: false,
  },
  {
    name: "Dr. Elena Patel",
    specialty: "Pediatrics",
    initials: "EP",
    dept: "Children's Ward",
    deptClass: "bg-indigo-50 text-indigo-700 border-indigo-100",
    license: "LIC-99234",
    experience: "15 Years",
    status: "Away (In Surgery)",
    statusClass: "bg-rose-500",
    telehealth: true,
  },
  {
    name: "Dr. Robert Wilson",
    specialty: "Orthopedics",
    avatar: "https://unsplash.com",
    dept: "Orthopedic Surgery",
    deptClass: "bg-slate-50 text-slate-700 border-slate-200",
    license: "LIC-22841",
    experience: "22 Years",
    status: "Online",
    statusClass: "bg-emerald-500",
    telehealth: false,
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
          <th class="py-3 px-4">Doctor</th>
          <th class="py-3 px-4">Department</th>
          <th class="py-3 px-4">License & Exp.</th>
          <th class="py-3 px-4">Status</th>
          <th class="py-3 px-4">Telemedicine</th>
          <th class="py-3 px-4 text-center">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 text-xs">
        <tr
          v-for="doc in doctors"
          :key="doc.license"
          class="hover:bg-slate-50/40 transition-colors"
        >
          <!-- Doctor Column info -->
          <td class="py-3.5 px-4">
            <div class="flex items-center gap-3">
              <div
                v-if="doc.initials"
                class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-[10px] flex items-center justify-center tracking-wider shadow-sm"
              >
                {{ doc.initials }}
              </div>
              <img
                v-else
                :src="doc.avatar"
                class="w-8 h-8 rounded-full object-cover shadow-sm bg-gray-100"
                alt=""
              />
              <div>
                <span class="font-bold text-gray-800 block leading-tight">{{
                  doc.name
                }}</span>
                <span
                  class="text-[10px] text-gray-400 font-medium block mt-0.5"
                  >{{ doc.specialty }}</span
                >
              </div>
            </div>
          </td>

          <!-- Department column tag container -->
          <td class="py-3.5 px-4">
            <span
              :class="doc.deptClass"
              class="px-2.5 py-1 text-[11px] font-bold rounded-lg border"
            >
              {{ doc.dept }}
            </span>
          </td>

          <!-- License metric rows -->
          <td class="py-3.5 px-4">
            <div class="font-bold text-gray-700">{{ doc.license }}</div>
            <div class="text-[10px] text-gray-400 font-medium mt-0.5">
              {{ doc.experience }}
            </div>
          </td>

          <!-- Status badge indicator tracking row -->
          <td class="py-3.5 px-4">
            <div
              class="flex items-center gap-1.5 font-bold"
              :class="
                doc.status.includes('Away') ? 'text-rose-600' : 'text-gray-700'
              "
            >
              <span
                :class="doc.statusClass"
                class="w-1.5 h-1.5 rounded-full"
              ></span>
              {{ doc.status }}
            </div>
          </td>

          <!-- Telemedicine enabled toggle indicator action card -->
          <td class="py-3.5 px-4">
            <div
              :class="
                doc.telehealth
                  ? 'bg-blue-600 text-white shadow-sm'
                  : 'bg-slate-100 text-gray-400'
              "
              class="w-7 h-7 flex items-center justify-center rounded-lg transition-all"
            >
              <svg
                xmlns="http://w3.org"
                width="13"
                height="13"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect width="18" height="12" x="3" y="4" rx="2" ry="2" />
                <line x1="2" x2="22" y1="20" y2="20" />
                <line x1="12" x2="12" y1="16" y2="20" />
              </svg>
            </div>
          </td>

          <!-- Standard Operations management action items cell -->
          <td class="py-3.5 px-4 text-center">
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

    <!-- Pagination Actions Footer Section layout -->
    <div
      class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400 font-semibold"
    >
      <div>Showing 1 to 4 of 124 doctors</div>
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
          31
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
