<template>
  <div
    class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-gray-100 mb-4"
  >
    <!-- Input Field -->
    <div class="relative w-full md:w-[280px]">
      <input
        type="text"
        placeholder="Search hospitals..."
        class="w-full pl-9 pr-4 py-2 text-xs bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-700"
      />
      <span class="absolute left-3 top-2.5 text-gray-400">
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
          <circle cx="11" cy="11" r="8" />
          <path d="m21 21-4.3-4.3" />
        </svg>
      </span>
    </div>

    <!-- Dropdown Elements Filter Set -->
    <div class="flex flex-wrap items-center gap-2">
      <div class="relative flex-1 sm:flex-initial">
        <select
          class="w-full appearance-none pl-3 pr-8 py-2 text-xs bg-white border border-gray-200 rounded-lg text-gray-600 focus:outline-none focus:border-blue-500"
        >
          <option>All Statuses</option>
        </select>
        <span
          class="absolute right-2.5 top-3 pointer-events-none text-gray-400"
        >
          <svg
            xmlns="http://w3.org"
            width="10"
            height="10"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path d="m6 9 6 6 6-6" />
          </svg>
        </span>
      </div>

      <div class="relative flex-1 sm:flex-initial">
        <select
          class="w-full appearance-none pl-3 pr-8 py-2 text-xs bg-white border border-gray-200 rounded-lg text-gray-600 focus:outline-none focus:border-blue-500"
        >
          <option>All Regions</option>
        </select>
        <span
          class="absolute right-2.5 top-3 pointer-events-none text-gray-400"
        >
          <svg
            xmlns="http://w3.org"
            width="10"
            height="10"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path d="m6 9 6 6 6-6" />
          </svg>
        </span>
      </div>

      <button
        class="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors"
      >
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
          <line x1="21" x2="14" y1="4" y2="4" />
          <line x1="10" x2="3" y1="4" y2="4" />
          <line x1="21" x2="12" y1="12" y2="12" />
          <line x1="8" x2="3" y1="12" y2="12" />
          <line x1="21" x2="16" y1="20" y2="20" />
          <line x1="12" x2="3" y1="20" y2="20" />
          <line x1="14" x2="14" y1="2" y2="6" />
          <line x1="8" x2="8" y1="10" y2="14" />
          <line x1="16" x2="16" y1="18" y2="22" />
        </svg>
        More Filters
      </button>
    </div>
  </div>
</template>
