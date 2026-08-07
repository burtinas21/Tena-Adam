import { ref, computed } from "vue";

// Desktop state
const isOpen = ref(true);

// Mobile state
const isMobileOpen = ref(false);

// Enable hover only after collapsing
const hoverEnabled = ref(false);

// Mouse is currently over the sidebar
const isHovering = ref(false);

// Sidebar should appear expanded if:
// 1. User explicitly opened it
// OR
// 2. It is collapsed but currently hovered
const expanded = computed(() => {
  return isOpen.value || (hoverEnabled.value && isHovering.value);
});

export function useSidebar() {
  function toggle() {
    if (window.innerWidth < 1024) {
      isMobileOpen.value = !isMobileOpen.value;
      return;
    }

    if (isOpen.value) {
      // Collapse
      isOpen.value = false;
      hoverEnabled.value = true;
    } else {
      // Expand permanently
      isOpen.value = true;
      hoverEnabled.value = false;
      isHovering.value = false;
    }
  }

  function mouseEnter() {
    if (hoverEnabled.value) {
      isHovering.value = true;
    }
  }

  function mouseLeave() {
    if (hoverEnabled.value) {
      isHovering.value = false;
    }
  }

  function closeMenu() {
    isMobileOpen.value = false;
  }

  return {
    isOpen,
    expanded,
    isMobileOpen,
    hoverEnabled,
    isHovering,
    toggle,
    mouseEnter,
    mouseLeave,
    closeMenu,
  };
}
