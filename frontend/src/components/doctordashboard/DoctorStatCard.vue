<template>
  <div
    :class="[
      'p-4 rounded-xl border flex flex-col justify-between shadow-sm min-h-[105px] transition-colors bg-white',
      isAlert
        ? 'border-red-100 bg-red-50/30'
        : 'border-gray-100 hover:border-gray-200',
    ]"
  >
    <div class="flex items-start justify-between">
      <span
        class="text-[10px] font-bold tracking-wider text-gray-400 uppercase"
        >{{ label }}</span
      >
      <component
        :is="icon"
        :class="['w-4 h-4', isAlert ? 'text-red-500' : 'text-gray-500']"
      />
    </div>

    <div class="flex items-baseline justify-between mt-2">
      <div class="flex items-baseline gap-x-2">
        <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">
          {{ value }}
        </h3>
        <!-- Sub-badge modification indicator like (+2) -->
        <span
          v-if="subBadge"
          class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-1 rounded"
        >
          {{ subBadge }}
        </span>
      </div>

      <span
        v-if="isAlert"
        class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wide"
      >
        High
      </span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: [String, Number],
  icon: Object,
  subBadge: String,
  isAlert: { type: Boolean, default: false },
});
</script>
