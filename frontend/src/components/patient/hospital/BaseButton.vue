<template>
  <button
    :class="[
      'px-4 py-2 text-sm font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2',
      variant === 'primary'
        ? 'bg-[#004bb5] text-white hover:bg-[#003da1] focus:ring-[#004bb5]'
        : '',
      variant === 'secondary'
        ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-gray-500'
        : '',
    ]"
    @click="$emit('click')"
  >
    <slot />
  </button>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: "primary",
  },
});
defineEmits(["click"]);
</script>
