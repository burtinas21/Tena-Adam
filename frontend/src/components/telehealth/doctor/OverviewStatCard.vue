<template>
  <div 
    class="flex items-start p-5 bg-white border rounded-xl shadow-[0_1px_2px_0_rgba(0,0,0,0.02)] relative overflow-hidden"
    :class="isActive ? 'border-blue-600 ring-[0.2px] ring-blue-600' : 'border-slate-200'"
  >
    <!-- Left Accent Side Badge Marker for Active State -->
    <div v-if="isActive" class="absolute left-0 top-0 bottom-0 w-[3px] bg-blue-600"></div>

    <div class="flex space-x-4">
      <!-- Icon Frame Wrapper Box -->
      <div 
        class="w-10 h-10 flex items-center justify-center rounded-lg border shrink-0"
        :class="isActive ? 'bg-blue-50/50 border-blue-100' : 'bg-slate-50 border-slate-100'"
      >
        <component :is="icon" class="w-4 h-4" :class="isActive ? 'text-blue-600' : 'text-slate-400'" />
      </div>

      <!-- Text Stack -->
      <div class="space-y-1">
        <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">
          {{ title }}
        </span>
        <div class="flex items-baseline space-x-0.5">
          <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ value }}</span>
          <span v-if="pulse" class="w-1.5 h-1.5 rounded-full bg-blue-600 self-center ml-1 animate-pulse"></span>
        </div>
        <p class="text-xs text-slate-400 font-medium">
          {{ description }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  value: [String, Number],
  description: String,
  icon: Object,
  isActive: { type: Boolean, default: false },
  pulse: { type: Boolean, default: false }
});
</script>
