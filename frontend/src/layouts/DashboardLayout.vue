<script setup>
import Sidebar from "../components/layout/Sidebar.vue";
import TopNavbar from "../components/layout/TopNavbar.vue";
</script>

<template>
  <div class="flex h-screen overflow-hidden bg-gray-50">
    <Sidebar />
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <TopNavbar />
      <main class="flex-1 overflow-y-auto">
        <router-view />
      </main>
    </div>
  </div>
</template>
