<template>
  <div
    class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex flex-col"
  >
    <div class="p-4 border-b border-gray-50 flex items-center gap-x-2">
      <h3 class="text-sm font-bold text-gray-800">Patient Queue</h3>
      <!-- Small notification number count indicator badge bubble -->
      <span
        class="w-5 h-5 bg-red-600 text-white font-bold text-xs rounded-full flex items-center justify-center"
        >3</span
      >
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr
            class="bg-[#F8FAFC] text-gray-400 font-bold text-[10px] uppercase tracking-wider border-b border-gray-100"
          >
            <th class="py-3 px-5">No.</th>
            <th class="py-3 px-5">Patient Name</th>
            <th class="py-3 px-5">Waiting Time</th>
            <th class="py-3 px-5">Priority</th>
            <th class="py-3 px-5 text-right">Action</th>
          </tr>
        </thead>
        <tbody
          class="text-xs font-medium text-gray-700 divide-y divide-gray-50"
        >
          <tr class="hover:bg-gray-50/50 transition-colors">
            <td class="py-3 px-5 font-bold text-gray-400">01</td>
            <td class="py-3 px-5 font-bold text-gray-900">Robert Davis</td>
            <td class="py-3 px-5 text-red-600 font-semibold">25 min</td>
            <td class="py-3 px-5">
              <span
                class="bg-red-50 text-red-600 text-[10px] font-bold px-1.5 py-0.5 rounded"
                >HIGH</span
              >
            </td>
            <td class="py-3 px-5 text-right">
              <button class="text-blue-600 font-bold hover:underline">
                Call In
              </button>
            </td>
          </tr>
          <tr class="hover:bg-gray-50/50 transition-colors">
            <td class="py-3 px-5 font-bold text-gray-400">02</td>
            <td class="py-3 px-5 font-bold text-gray-900">James Smith</td>
            <td class="py-3 px-5 text-gray-600">10 min</td>
            <td class="py-3 px-5">
              <span
                class="bg-blue-50 text-blue-600 text-[10px] font-bold px-1.5 py-0.5 rounded"
                >NORMAL</span
              >
            </td>
            <td class="py-3 px-5 text-right">
              <button class="text-blue-600 font-bold hover:underline">
                Call In
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
