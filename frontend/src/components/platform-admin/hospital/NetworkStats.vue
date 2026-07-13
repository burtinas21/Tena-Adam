<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <!-- Total Hospitals Card -->
    <div
      class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4"
    >
      <div
        class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0"
      >
        <svg
          xmlns="http://w3.org"
          width="22"
          height="22"
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
          <path d="M9 21v-4a2 2 0 0 1 4 0v4" />
        </svg>
      </div>
      <div>
        <p class="text-[10px] font-bold text-gray-400 tracking-wider uppercase">
          Total Hospitals
        </p>
        <h3 class="text-2xl font-black text-gray-900 tracking-tight mt-0.5">
          124
        </h3>
      </div>
    </div>

    <!-- Active Hospitals Card -->
    <div
      class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4"
    >
      <div
        class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0"
      >
        <svg
          xmlns="http://w3.org"
          width="22"
          height="22"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <circle cx="12" cy="12" r="10" />
          <polyline points="12 6 12 12 16 14" />
        </svg>
      </div>
      <div>
        <p class="text-[10px] font-bold text-gray-400 tracking-wider uppercase">
          Active Hospitals
        </p>
        <h3 class="text-2xl font-black text-gray-900 tracking-tight mt-0.5">
          118
        </h3>
      </div>
    </div>

    <!-- Suspended Hospitals Card -->
    <div
      class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4"
    >
      <div
        class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0"
      >
        <svg
          xmlns="http://w3.org"
          width="22"
          height="22"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <circle cx="12" cy="12" r="10" />
          <line x1="15" x2="9" y1="9" y2="15" />
          <line x1="9" x2="15" y1="9" y2="15" />
        </svg>
      </div>
      <div>
        <p class="text-[10px] font-bold text-gray-400 tracking-wider uppercase">
          Suspended
        </p>
        <h3 class="text-2xl font-black text-gray-900 tracking-tight mt-0.5">
          6
        </h3>
      </div>
    </div>
  </div>
</template>
