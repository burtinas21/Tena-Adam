<template>
  <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-[0_1px_2px_rgba(0,0,0,0.01)] flex flex-col justify-between h-[125px] relative overflow-hidden">
    <!-- Main Content Layout -->
    <div class="space-y-1">
      <span class="text-xs font-bold text-slate-400 tracking-tight block">{{ title }}</span>
      <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight font-sans pt-1">
        {{ value }}
      </h3>
    </div>

    <!-- Bottom Trend pill indicator matching color rules -->
    <div class="mt-auto flex items-center space-x-1 text-[11px] font-bold text-emerald-500 bg-emerald-50/50 border border-emerald-100/30 px-2 py-0.5 rounded-md w-max">
      <span>↗</span>
      <span>{{ trend }}</span>
    </div>

    <!-- Right absolute ghost icon matching decoration pattern -->
    <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-20 text-slate-400 pointer-events-none">
      <component :is="icon" class="w-10 h-10 stroke-[1.25]" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  value: String,
  trend: String,
  icon: Object
});
</script>
