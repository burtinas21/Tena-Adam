<template>
  <div
    class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex flex-col"
  >
    <!-- Header Banner Panel -->
    <div class="p-5 flex items-center justify-between border-b border-gray-50">
      <h2 class="text-base font-bold text-gray-800">Live Queue Performance</h2>
      <a href="#" class="text-xs font-semibold text-blue-600 hover:underline"
        >View All</a
      >
    </div>

    <!-- Table Structure Element Box -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr
            class="bg-[#F8FAFC] text-gray-400 font-bold text-[11px] uppercase tracking-wider border-b border-gray-100"
          >
            <th class="py-3 px-5">Department</th>
            <th class="py-3 px-5 text-center">Patients Waiting</th>
            <th class="py-3 px-5 text-center">Avg Service Time</th>
            <th class="py-3 px-5 text-right">Status</th>
          </tr>
        </thead>
        <tbody
          class="text-xs font-medium text-gray-700 divide-y divide-gray-50"
        >
          <tr class="hover:bg-gray-50/50 transition-colors">
            <td class="py-3.5 px-5 font-bold text-gray-900">
              General Practice
            </td>
            <td class="py-3.5 px-5 text-center">12</td>
            <td class="py-3.5 px-5 text-center">15 mins</td>
            <td class="py-3.5 px-5 text-right">
              <span
                class="bg-emerald-50 text-emerald-600 font-semibold px-2 py-0.5 rounded-full text-[10px]"
                >On Track</span
              >
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
