<template>
  <!-- Fixed top-right portal — sits above everything -->
  <Teleport to="body">
    <div
      class="fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 pointer-events-none"
      aria-live="polite"
      aria-atomic="false"
    >
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="variantClasses(toast.type)"
          class="pointer-events-auto flex items-start gap-3 min-w-[280px] max-w-[380px] rounded-xl shadow-lg border px-4 py-3.5 text-sm font-medium"
          role="alert"
        >
          <!-- Icon -->
          <span class="flex-shrink-0 mt-0.5 text-base leading-none">
            {{ iconFor(toast.type) }}
          </span>

          <!-- Message -->
          <span class="flex-1 leading-snug">{{ toast.message }}</span>

          <!-- Close button -->
          <button
            @click="removeToast(toast.id)"
            class="flex-shrink-0 opacity-60 hover:opacity-100 transition ml-1"
            :class="closeButtonClass(toast.type)"
            aria-label="Dismiss"
          >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { useToast } from "../../composables/useToast";

const { toasts, removeToast } = useToast();

function iconFor(type) {
  return {
    success: "✅",
    error:   "❌",
    warning: "⚠️",
    info:    "ℹ️",
  }[type] ?? "🔔";
}

function variantClasses(type) {
  return {
    success: "bg-emerald-50 border-emerald-200 text-emerald-800",
    error:   "bg-red-50   border-red-200   text-red-800",
    warning: "bg-amber-50  border-amber-200  text-amber-800",
    info:    "bg-blue-50   border-blue-200   text-blue-800",
  }[type] ?? "bg-white border-gray-200 text-gray-800";
}

function closeButtonClass(type) {
  return {
    success: "text-emerald-700",
    error:   "text-red-700",
    warning: "text-amber-700",
    info:    "text-blue-700",
  }[type] ?? "text-gray-600";
}
</script>

<style scoped>
/* Slide-in from the right */
.toast-enter-active,
.toast-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(60px);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(60px);
}
/* Smooth re-layout when a toast is removed */
.toast-move {
  transition: transform 0.25s ease;
}
</style>
