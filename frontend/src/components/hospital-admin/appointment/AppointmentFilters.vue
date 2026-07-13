<template>
  <div
    class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 pb-4 border-b border-gray-100 mb-4"
  >
    <!-- View Switchers -->
    <div class="flex gap-1.5 bg-gray-100/80 p-1 rounded-lg self-start">
      <button
        class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-blue-600 bg-white rounded-md shadow-sm border border-gray-200/50"
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
          <rect width="7" height="9" x="3" y="3" rx="1" />
          <rect width="7" height="5" x="14" y="3" rx="1" />
          <rect width="7" height="9" x="14" y="12" rx="1" />
          <rect width="7" height="5" x="3" y="16" rx="1" />
        </svg>
        Registry View
      </button>
      <button
        class="flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-gray-500 hover:text-gray-900 transition-colors"
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
          <path d="M8 2v4" />
          <path d="M16 2v4" />
          <rect width="18" height="18" x="3" y="4" rx="2" />
          <path d="M3 10h18" />
        </svg>
        Calendar View
      </button>
    </div>

    <!-- Filter Tools -->
    <div class="flex flex-wrap items-center gap-2 w-full xl:w-auto">
      <div class="relative flex-1 sm:flex-initial min-w-[240px]">
        <input
          type="text"
          placeholder="Search Patient Name or ID..."
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

      <div class="relative flex-1 sm:flex-initial">
        <select
          class="w-full appearance-none pl-3 pr-8 py-2 text-xs bg-white border border-gray-200 rounded-lg text-gray-600 focus:outline-none focus:border-blue-500"
        >
          <option>All Doctors</option>
        </select>
        <span class="absolute right-2.5 top-3 pointer-events-none text-gray-400"
          ><svg
            xmlns="http://w3.org"
            width="10"
            height="10"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path d="m6 9 6 6 6-6" /></svg
        ></span>
      </div>

      <div class="relative flex-1 sm:flex-initial">
        <select
          class="w-full appearance-none pl-3 pr-8 py-2 text-xs bg-white border border-gray-200 rounded-lg text-gray-600 focus:outline-none focus:border-blue-500"
        >
          <option>All Departments</option>
        </select>
        <span class="absolute right-2.5 top-3 pointer-events-none text-gray-400"
          ><svg
            xmlns="http://w3.org"
            width="10"
            height="10"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path d="m6 9 6 6 6-6" /></svg
        ></span>
      </div>

      <div class="relative flex-1 sm:flex-initial">
        <select
          class="w-full appearance-none pl-3 pr-8 py-2 text-xs bg-white border border-gray-200 rounded-lg text-gray-600 focus:outline-none focus:border-blue-500"
        >
          <option>Today</option>
        </select>
        <span class="absolute right-2.5 top-3 pointer-events-none text-gray-400"
          ><svg
            xmlns="http://w3.org"
            width="10"
            height="10"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path d="m6 9 6 6 6-6" /></svg
        ></span>
      </div>

      <button
        class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-500 transition-colors"
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
      </button>
    </div>
  </div>
</template>
