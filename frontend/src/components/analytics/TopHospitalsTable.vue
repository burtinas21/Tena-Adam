<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_2px_rgba(0,0,0,0.01)] p-5 flex flex-col justify-between h-[300px]"
  >
    <!-- Table Header Panel Rows -->
    <div class="flex items-center justify-between pb-3">
      <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">
        Top Hospitals by Volume
      </h3>
      <button
        class="text-xs font-bold text-blue-600 hover:text-blue-700 tracking-tight hover:underline"
      >
        View All
      </button>
    </div>

    <!-- Data Layout Matrix Frame -->
    <div class="overflow-x-auto flex-1 mt-2">
      <table
        class="w-full text-left border-collapse text-xs font-medium text-slate-600"
      >
        <thead>
          <tr
            class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider"
          >
            <th class="pb-2.5 font-semibold">Hospital Name</th>
            <th class="pb-2.5 font-semibold">Region</th>
            <th class="pb-2.5 font-semibold">Patient Vol.</th>
            <th class="pb-2.5 text-right pr-4 font-semibold">
              Completion Rate
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <!-- Row 1 -->
          <tr>
            <td class="py-3 font-bold text-slate-800 tracking-tight">
              Central Metro General
            </td>
            <td class="py-3 text-slate-500">North</td>
            <td class="py-3 font-semibold text-slate-700">12,450</td>
            <td class="py-3">
              <div class="flex items-center justify-end space-x-2.5 pr-2">
                <span class="font-bold text-slate-800 text-[11px]">94%</span>
                <div
                  class="w-16 bg-slate-100 h-1.5 rounded-full overflow-hidden"
                >
                  <div
                    class="bg-emerald-500 h-full rounded-full"
                    style="width: 94%"
                  ></div>
                </div>
              </div>
            </td>
          </tr>
          <!-- Row 2 -->
          <tr>
            <td class="py-3 font-bold text-slate-800 tracking-tight">
              Westside Medical Center
            </td>
            <td class="py-3 text-slate-500">West</td>
            <td class="py-3 font-semibold text-slate-700">9,820</td>
            <td class="py-3">
              <div class="flex items-center justify-end space-x-2.5 pr-2">
                <span class="font-bold text-slate-800 text-[11px]">88%</span>
                <div
                  class="w-16 bg-slate-100 h-1.5 rounded-full overflow-hidden"
                >
                  <div
                    class="bg-emerald-500 h-full rounded-full"
                    style="width: 88%"
                  ></div>
                </div>
              </div>
            </td>
          </tr>
          <!-- Row 3 -->
          <tr>
            <td class="py-3 font-bold text-slate-800 tracking-tight">
              University Health Network
            </td>
            <td class="py-3 text-slate-500">Central</td>
            <td class="py-3 font-semibold text-slate-700">8,105</td>
            <td class="py-3">
              <div class="flex items-center justify-end space-x-2.5 pr-2">
                <span class="font-bold text-slate-800 text-[11px]">91%</span>
                <div
                  class="w-16 bg-slate-100 h-1.5 rounded-full overflow-hidden"
                >
                  <div
                    class="bg-emerald-500 h-full rounded-full"
                    style="width: 91%"
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

<script setup></script>
