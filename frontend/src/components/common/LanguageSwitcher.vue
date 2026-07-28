<script setup>
import { ref, computed } from "vue";
import { useLanguageStore } from "../../stores/languageStore";
import { ChevronDown, Check } from "lucide-vue-next";

const languageStore = useLanguageStore();
const open = ref(false);

const languages = [
  { code: "en", label: "English",      native: "English",       flag: "🇬🇧" },
  { code: "am", label: "Amharic",      native: "አማርኛ",          flag: "🇪🇹" },
  { code: "om", label: "Afaan Oromo",  native: "Afaan Oromoo",  flag: "🇪🇹" },
  { code: "ti", label: "Tigrinya",     native: "ትግርኛ",          flag: "🇪🇹" },
];

const current = computed(
  () => languages.find((l) => l.code === languageStore.currentLanguage) ?? languages[0]
);

async function select(code) {
  open.value = false;
  if (code !== languageStore.currentLanguage) {
    await languageStore.changeLanguage(code);
  }
}
</script>

<template>
  <div class="relative">
    <!-- Trigger button -->
    <button
      @click="open = !open"
      class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-sm text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition select-none"
      aria-label="Change language"
    >
      <span class="text-base leading-none">{{ current.flag }}</span>
      <span class="hidden sm:inline font-medium">{{ current.native }}</span>
      <ChevronDown
        class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
        :class="{ 'rotate-180': open }"
      />
    </button>

    <!-- Dropdown -->
    <Transition name="dropdown">
      <div
        v-if="open"
        class="absolute right-0 top-full mt-2 w-44 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-100 dark:border-slate-700 py-1 z-50"
      >
        <button
          v-for="lang in languages"
          :key="lang.code"
          @click="select(lang.code)"
          class="w-full flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition"
          :class="lang.code === current.code
            ? 'text-[#004795] dark:text-blue-400 font-semibold'
            : 'text-gray-700 dark:text-slate-300'"
        >
          <span class="text-base leading-none">{{ lang.flag }}</span>
          <span class="flex-1 text-left">{{ lang.native }}</span>
          <Check
            v-if="lang.code === current.code"
            class="w-3.5 h-3.5 flex-shrink-0"
          />
        </button>
      </div>
    </Transition>

    <!-- Click outside to close -->
    <div
      v-if="open"
      class="fixed inset-0 z-40"
      @click="open = false"
    />
  </div>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.15s, transform 0.15s;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
