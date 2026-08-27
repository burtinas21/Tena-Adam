<script setup>
import Sidebar from "../components/layout/Sidebar.vue";
import TopNavbar from "../components/layout/TopNavbar.vue";
import ToastContainer from "../components/common/ToastContainer.vue";
</script>

<template>
  <div
    class="flex h-screen overflow-hidden bg-gray-50 dark:bg-[#0f172a] transition-colors duration-300"
  >
    <Sidebar />
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <TopNavbar />
      <!--
        This <main> is the scroll container that wraps the router-view.
        The child view components each have their OWN <main> with bg-[#F8FAFC].
        theme.css targets html.dark main to override all of them to #0f172a.
      -->
      <div class="flex-1 overflow-y-auto bg-gray-50 dark:bg-[#0f172a] transition-colors duration-300">
        <router-view />
      </div>
    </div>

    <!-- Global toast notifications — renders into <body> via Teleport -->
    <ToastContainer />
  </div>
</template>
