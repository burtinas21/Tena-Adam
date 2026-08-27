import { ref } from "vue";

const toasts = ref([]);
let nextId = 0;

/**
 * Shared toast composable.
 * Usage:
 *   const { showToast } = useToast();
 *   showToast("Hospital registered successfully", "success");
 *   showToast("Something went wrong", "error");
 */
export function useToast() {
  /**
   * @param {string} message  - Text to display
   * @param {"success"|"error"|"info"|"warning"} type - Toast variant
   * @param {number} duration - Auto-dismiss after ms (default 4000)
   */
  function showToast(message, type = "success", duration = 4000) {
    const id = ++nextId;
    toasts.value.push({ id, message, type });

    if (duration > 0) {
      setTimeout(() => removeToast(id), duration);
    }
  }

  function removeToast(id) {
    toasts.value = toasts.value.filter((t) => t.id !== id);
  }

  return { toasts, showToast, removeToast };
}
