<template>
  <select
    :value="languageStore.currentLanguage"
    @change="change"
    class="px-3 py-2 rounded-lg border text-sm"
  >
    <option value="en">English</option>

    <option value="am">አማርኛ</option>

    <option value="om">Afaan Oromoo</option>

    <option value="ti">ትግርኛ</option>
  </select>
</template>

<script setup>
import { useLanguageStore } from "../../stores/languageStore";

const languageStore = useLanguageStore();

async function change(event) {
  await languageStore.changeLanguage(event.target.value);
}
</script>
