import { ref, computed } from "vue";
export function usePagination(items, defaultPerPage = 10) {
  const page = ref(1);
  const perPage = ref(defaultPerPage);

  const total = computed(() => items.value.length);
  const totalPages = computed(() =>
    Math.max(1, Math.ceil(total.value / perPage.value)),
  );
  function reset() {
    page.value = 1;
  }

  const paginated = computed(() => {
    const start = (page.value - 1) * perPage.value;
    return items.value.slice(start, start + perPage.value);
  });

  function prev() {
    if (page.value > 1) page.value--;
  }
  function next() {
    if (page.value < totalPages.value) page.value++;
  }
  function goTo(n) {
    page.value = Math.min(Math.max(1, n), totalPages.value);
  }

  return {
    page,
    perPage,
    total,
    totalPages,
    paginated,
    reset,
    prev,
    next,
    goTo,
  };
}
