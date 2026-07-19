<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.02)] overflow-hidden"
  >
    <!-- Top Filter Controls Bar Panel -->
    <div
      class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white"
    >
      <!-- Search Field Inputs -->
      <div class="relative w-full max-w-sm">
        <Search
          class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
        />
        <input
          type="text"
          placeholder="Search user or action..."
          class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
        />
      </div>

      <!-- Action Select Dropdowns Controls -->
      <div
        class="flex items-center space-x-2.5 shrink-0 self-start md:self-center"
      >
        <button
          class="flex items-center space-x-1.5 px-3.5 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition"
        >
          <span>All Action Types</span>
          <ChevronDown class="w-3.5 h-3.5 text-slate-400" />
        </button>
        <button
          class="flex items-center space-x-1.5 px-3.5 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition"
        >
          <span>Last 24 Hours</span>
          <ChevronDown class="w-3.5 h-3.5 text-slate-400" />
        </button>
        <button
          class="p-2 bg-white border border-slate-200 rounded-lg text-slate-400 hover:text-slate-600 transition shadow-sm"
        >
          <SlidersHorizontal class="w-3.5 h-3.5" />
        </button>
      </div>
    </div>

    <!-- Audit Logs Main Data Table Area Grid -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr
            class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider"
          >
            <th class="py-3 px-6 font-semibold tracking-wider">User</th>
            <th class="py-3 px-6 font-semibold tracking-wider">Action</th>
            <th class="py-3 px-6 font-semibold tracking-wider">Module</th>
            <th class="py-3 px-6 font-semibold tracking-wider">Timestamp</th>
            <th class="py-3 px-6 font-semibold tracking-wider">IP Address</th>
          </tr>
        </thead>
        <tbody
          class="divide-y divide-slate-100 text-xs font-medium text-slate-700"
        >
          <!-- Row 1: Sarah Jenkins -->
          <tr class="hover:bg-slate-50/20 transition">
            <td class="py-4 px-6">
              <div class="flex items-center space-x-3.5">
                <div
                  class="w-8 h-8 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center font-bold text-blue-600 text-[11px] tracking-wider shadow-inner shrink-0"
                >
                  SJ
                </div>
                <div>
                  <div class="font-bold text-slate-800 tracking-tight">
                    Sarah Jenkins
                  </div>
                  <div class="text-[10px] text-slate-400 font-semibold mt-0.5">
                    System Admin
                  </div>
                </div>
              </div>
            </td>
            <td class="py-4 px-6">
              <div
                class="flex items-center space-x-1.5 text-slate-800 font-bold tracking-tight"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>Hospital Registered</span>
              </div>
            </td>
            <td class="py-4 px-6">
              <span
                class="px-2 py-0.5 bg-blue-50 border border-blue-100/50 rounded text-[10px] font-bold text-blue-600 tracking-wide"
              >
                Hospitals
              </span>
            </td>
            <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
              Oct 24, 14:32:01
            </td>
            <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
              192.168.1.45
            </td>
          </tr>

          <!-- Row 2: Marcus Reed -->
          <tr class="hover:bg-slate-50/20 transition">
            <td class="py-4 px-6">
              <div class="flex items-center space-x-3.5">
                <div
                  class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-500 text-[11px] tracking-wider shadow-inner shrink-0"
                >
                  MR
                </div>
                <div>
                  <div class="font-bold text-slate-800 tracking-tight">
                    Marcus Reed
                  </div>
                  <div class="text-[10px] text-slate-400 font-semibold mt-0.5">
                    Support Agent
                  </div>
                </div>
              </div>
            </td>
            <td class="py-4 px-6">
              <div
                class="flex items-center space-x-1.5 text-slate-800 font-bold tracking-tight"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                <span>User Profile Updated</span>
              </div>
            </td>
            <td class="py-4 px-6">
              <span
                class="px-2 py-0.5 bg-slate-100 border border-slate-200 rounded text-[10px] font-bold text-slate-600 tracking-wide"
              >
                Users
              </span>
            </td>
            <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
              Oct 24, 13:15:44
            </td>
            <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
              10.0.4.112
            </td>
          </tr>

          <!-- Row 3: Automated System (Failed Attempt) -->
          <tr class="hover:bg-slate-50/20 transition">
            <td class="py-4 px-6">
              <div class="flex items-center space-x-3.5">
                <div
                  class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-500 text-[11px] tracking-wider shadow-inner shrink-0"
                >
                  SY
                </div>
                <div>
                  <div class="font-bold text-slate-800 tracking-tight">
                    System
                  </div>
                  <div class="text-[10px] text-slate-400 font-semibold mt-0.5">
                    Automated
                  </div>
                </div>
              </div>
            </td>
            <td class="py-4 px-6">
              <div
                class="flex items-center space-x-1.5 text-rose-600 font-bold tracking-tight"
              >
                <span
                  class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"
                ></span>
                <span>Failed Login Attempt</span>
              </div>
            </td>
            <td class="py-4 px-6">
              <span
                class="px-2 py-0.5 bg-indigo-50 border border-indigo-100/40 rounded text-[10px] font-bold text-indigo-600 tracking-wide"
              >
                Auth
              </span>
            </td>
            <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
              Oct 24, 11:05:12
            </td>
            <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
              203.0.113.42
            </td>
          </tr>

          <!-- Row 4: Sarah Jenkins (Billing) -->
          <tr class="hover:bg-slate-50/20 transition border-b border-slate-100">
            <td class="py-4 px-6">
              <div class="flex items-center space-x-3.5">
                <div
                  class="w-8 h-8 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center font-bold text-blue-600 text-[11px] tracking-wider shadow-inner shrink-0"
                >
                  SJ
                </div>
                <div>
                  <div class="font-bold text-slate-800 tracking-tight">
                    Sarah Jenkins
                  </div>
                  <div class="text-[10px] text-slate-400 font-semibold mt-0.5">
                    System Admin
                  </div>
                </div>
              </div>
            </td>
            <td class="py-4 px-6">
              <div
                class="flex items-center space-x-1.5 text-slate-800 font-bold tracking-tight"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                <span>Billing Cycle Renewed</span>
              </div>
            </td>
            <td class="py-4 px-6">
              <span
                class="px-2 py-0.5 bg-purple-50 border border-purple-100/40 rounded text-[10px] font-bold text-purple-600 tracking-wide"
              >
                Subscriptions
              </span>
            </td>
            <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
              Oct 24, 09:00:05
            </td>
            <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
              192.168.1.45
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Active Grid Pagination Controls Footer -->
    <div
      class="px-6 py-4.5 bg-white border-t border-slate-100 flex items-center justify-between text-xs text-slate-400 font-medium"
    >
      <span>Showing 1 to 4 of 2,451 entries</span>

      <div class="flex items-center space-x-1.5 font-sans">
        <button
          disabled
          class="p-1.5 bg-white border border-slate-200 rounded-md text-slate-200 cursor-not-allowed"
        >
          <ChevronLeft class="w-4 h-4" />
        </button>

        <button
          class="w-7 h-7 flex items-center justify-center bg-[#0252D7] text-white font-bold rounded-md shadow-sm"
        >
          1
        </button>
        <button
          class="w-7 h-7 flex items-center justify-center bg-white border border-slate-200 text-slate-600 rounded-md hover:bg-slate-50 transition shadow-sm"
        >
          2
        </button>
        <button
          class="w-7 h-7 flex items-center justify-center bg-white border border-slate-200 text-slate-600 rounded-md hover:bg-slate-50 transition shadow-sm"
        >
          3
        </button>
        <span class="text-slate-300 px-1 font-bold">...</span>
        <button
          class="w-9 h-7 flex items-center justify-center bg-white border border-slate-200 text-slate-600 rounded-md hover:bg-slate-50 transition shadow-sm font-mono"
        >
          409
        </button>

        <button
          class="p-1.5 bg-white border border-slate-200 rounded-md text-slate-500 hover:bg-slate-50 shadow-sm transition"
        >
          <ChevronRight class="w-4 h-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  Search,
  ChevronDown,
  SlidersHorizontal,
  ChevronLeft,
  ChevronRight,
} from "lucide-vue-next";
</script>
