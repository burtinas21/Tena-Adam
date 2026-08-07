<template>
  <router-view />
</template>
<script setup>
import { onMounted } from "vue";
import { useThemeStore } from "./stores/themeStore";
const theme = useThemeStore();
onMounted(() => {
  theme.initializeTheme();
});
</script>
