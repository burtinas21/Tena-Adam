<template>
  <div
    :class="[
      'p-4 rounded-xl border flex flex-col justify-between shadow-sm min-h-[105px] transition-all',
      isAlert
        ? 'bg-red-50 border-red-200'
        : 'bg-white border-gray-100 hover:border-gray-200',
    ]"
  >
    <!-- Card Header Row: Text Label and Visual Icon -->
    <div class="flex items-start justify-between">
      <span
        :class="[
          'text-[11px] font-bold tracking-wider uppercase',
          isAlert ? 'text-red-700' : 'text-gray-400',
        ]"
      >
        {{ label }}
      </span>
      <component
        :is="icon"
        :class="['w-4 h-4', isAlert ? 'text-red-600' : 'text-[#0A3D80]']"
      />
    </div>

    <!-- Card Bottom Row: Main Numerical Figure and Badges -->
    <div class="flex items-baseline justify-between mt-2">
      <h3
        :class="[
          'text-2xl font-bold tracking-tight',
          isAlert ? 'text-red-900' : 'text-gray-800',
        ]"
      >
        {{ value }}
      </h3>

      <!-- Growth Indicator Status Badge -->
      <span
        v-if="trend"
        :class="[
          'text-[11px] font-semibold flex items-center gap-x-0.5',
          trendColor,
        ]"
      >
        {{ trend }}
      </span>

      <!-- High Priority Alert Badge Component -->
      <span
        v-if="isAlert"
        class="bg-red-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wide"
      >
        High
      </span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: String,
  icon: Object,
  trend: String,
  trendColor: { type: String, default: "text-emerald-500" },
  isAlert: { type: Boolean, default: false },
});
</script>
