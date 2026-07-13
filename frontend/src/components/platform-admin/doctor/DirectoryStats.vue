<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Stat 1 -->
    <div
      class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between"
    >
      <div>
        <div
          class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"
        >
          <svg
            xmlns="http://w3.org"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
          </svg>
        </div>
        <h3 class="text-3xl font-black text-gray-900 mt-4 tracking-tight">
          124
        </h3>
      </div>
      <p
        class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mt-1"
      >
        Total Doctors
      </p>
    </div>

    <!-- Stat 2 -->
    <div
      class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between"
    >
      <div>
        <div class="flex items-center justify-between">
          <div
            class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"
          >
            <svg
              xmlns="http://w3.org"
              width="16"
              height="16"
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
            </svg>
          </div>
          <span
            class="inline-flex items-center text-[10px] font-bold bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full border border-emerald-100"
          >
            +12 this week
          </span>
        </div>
        <h3 class="text-3xl font-black text-gray-900 mt-4 tracking-tight">
          118
        </h3>
      </div>
      <p
        class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mt-1"
      >
        Active Providers
      </p>
    </div>

    <!-- Stat 3 -->
    <div
      class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between"
    >
      <div>
        <div
          class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center"
        >
          <svg
            xmlns="http://w3.org"
            width="16"
            height="16"
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
        </div>
        <h3 class="text-3xl font-black text-gray-900 mt-4 tracking-tight">
          45
        </h3>
      </div>
      <p
        class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mt-1"
      >
        Telemedicine Enabled
      </p>
    </div>

    <!-- Stat 4 -->
    <div
      class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between"
    >
      <div>
        <div
          class="w-8 h-8 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center"
        >
          <svg
            xmlns="http://w3.org"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <rect width="18" height="18" x="3" y="4" rx="2" />
            <path d="M3 10h18" />
            <path d="M8 14h.01" />
            <path d="M12 14h.01" />
            <path d="M16 14h.01" />
            <path d="M8 18h.01" />
            <path d="M12 18h.01" />
            <path d="M16 18h.01" />
          </svg>
        </div>
        <h3 class="text-3xl font-black text-gray-900 mt-4 tracking-tight">
          32
        </h3>
      </div>
      <p
        class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mt-1"
      >
        Available Today
      </p>
    </div>
  </div>
</template>
