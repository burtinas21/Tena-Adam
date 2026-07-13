import { ref } from "vue";

// Shared singleton state so Sidebar and TopNavbar both see the same value
const isOpen = ref(true);      // desktop: show full sidebar
const isMobileOpen = ref(false); // mobile: overlay open

export function useSidebar() {
  function toggle() {
    // On mobile (< lg), toggle overlay; on desktop, collapse/expand
    if (window.innerWidth < 1024) {
      isMobileOpen.value = !isMobileOpen.value;
    } else {
      isOpen.value = !isOpen.value;
    }
  }

  function closeMenu() {
    isMobileOpen.value = false;
  }

  return { isOpen, isMobileOpen, toggle, closeMenu };
}
