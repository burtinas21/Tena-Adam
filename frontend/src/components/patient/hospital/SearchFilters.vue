<template>
  <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm mb-8">
    <!-- Top Search Bar Row -->
    <div class="flex gap-4 mb-4">
      <div class="relative flex-1">
        <span
          class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400"
        >
          🔍
        </span>
        <input
          type="text"
          placeholder="Search Hospital Name..."
          class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#004bb5]"
        />
      </div>
      <button
        class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 flex items-center gap-2 hover:bg-gray-50"
      >
        🎛️ Advanced Filters
      </button>
      <button
        class="px-6 py-2 bg-[#004bb5] text-white text-sm font-medium rounded-lg hover:bg-[#003da1]"
      >
        Search
      </button>
    </div>

    <!-- Bottom Dropdowns Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
      <div>
        <label
          class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1"
          >Region</label
        >
        <select
          class="w-full p-2 border border-gray-200 rounded-lg text-sm text-gray-600 bg-white focus:outline-none"
        >
          <option>Select Region</option>
        </select>
      </div>

      <div>
        <label
          class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1"
          >City</label
        >
        <select
          class="w-full p-2 border border-gray-200 rounded-lg text-sm text-gray-600 bg-white focus:outline-none"
        >
          <option>Select City</option>
        </select>
      </div>

      <div>
        <label
          class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1"
          >Department</label
        >
        <select
          class="w-full p-2 border border-gray-200 rounded-lg text-sm text-gray-600 bg-white focus:outline-none"
        >
          <option>All Departments</option>
        </select>
      </div>

      <div class="flex items-center pt-5">
        <label
          class="relative flex items-center p-2 rounded-lg border border-gray-200 cursor-pointer w-full justify-between hover:bg-gray-50"
        >
          <div class="flex items-center gap-2">
            <input
              type="checkbox"
              class="w-4 h-4 text-[#004bb5] border-gray-300 rounded focus:ring-[#004bb5]"
            />
            <span class="text-sm font-medium text-gray-700"
              >Telemedicine Available</span
            >
          </div>
          <span class="text-emerald-500">📹</span>
        </label>
      </div>
    </div>
  </div>
</template>
