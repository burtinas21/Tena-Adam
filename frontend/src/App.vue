<template>
  <router-view />
</template>
<script setup>
import { onMounted } from "vue";
import { useThemeStore } from "./stores/themeStore";
import { useLanguageStore } from "./stores/languageStore";
const theme = useThemeStore();
onMounted(() => {
  theme.initializeTheme();
});
const language = useLanguageStore();

onMounted(async () => {
  await language.loadTranslations("am");
});
</script>
