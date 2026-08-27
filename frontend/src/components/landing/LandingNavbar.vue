<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Menu, X, ChevronDown, Globe2, HeartPulse } from "lucide-vue-next";

const isMobileMenuOpen = ref(false);
const isScrolled = ref(false);

const navItems = [
  { name: "Home", href: "#home" },
  { name: "Services", href: "#services" },
  { name: "About", href: "#about" },
  { name: "Contact", href: "#contact" },
];

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false;
};

onMounted(() => {
  window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
  <header
    class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
    :class="
      isScrolled
        ? 'bg-white/95 shadow-lg shadow-slate-200/30 backdrop-blur-xl'
        : 'bg-white/80 backdrop-blur-md'
    "
  >
    <nav
      class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
      aria-label="Main navigation"
    >
      <!-- Logo -->
      <a
        href="#home"
        class="group flex items-center gap-3"
        @click="closeMobileMenu"
      >
        <!-- Logo Icon -->
        <div
          class="relative flex h-11 w-11 items-center justify-center overflow-hidden rounded-xl bg-[#004795] shadow-lg shadow-blue-900/20 transition-all duration-300 group-hover:scale-105"
        >
          <div
            class="absolute inset-0 bg-gradient-to-br from-[#0ea5e9] to-[#004795] opacity-0 transition-opacity duration-300 group-hover:opacity-100"
          ></div>

          <HeartPulse
            class="relative z-10 h-6 w-6 text-white"
            :stroke-width="2.2"
          />
        </div>

        <!-- Brand -->
        <div class="hidden sm:block">
          <div class="text-lg font-extrabold tracking-tight text-slate-900">
            TENA<span class="text-[#004795]">-ADAM</span>
          </div>

          <p
            class="text-[10px] font-medium uppercase tracking-[0.18em] text-slate-500"
          >
            Smart Healthcare
          </p>
        </div>
      </a>

      <!-- Desktop Navigation -->
      <div class="hidden items-center gap-1 lg:flex">
        <a
          v-for="item in navItems"
          :key="item.name"
          :href="item.href"
          class="relative rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 transition-all duration-200 hover:bg-blue-50 hover:text-[#004795]"
        >
          {{ item.name }}
        </a>
      </div>

      <!-- Desktop Actions -->
      <div class="hidden items-center gap-3 lg:flex">
        <!-- <button
          type="button"
          class="flex items-center gap-1.5 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-[#004795]"
          aria-label="Select language"
        > -->
        <!-- <Globe2 class="h-4 w-4" />
          <span>EN</span>
          <ChevronDown class="h-3.5 w-3.5" /> -->
        <!-- </button> -->

        <!-- Login -->
        <router-link
          to="/login"
          class="rounded-lg px-4 py-2.5 text-sm font-semibold text-[#004795] transition hover:bg-blue-50"
        >
          Login
        </router-link>

        <!-- Register -->
        <router-link
          to="/register"
          class="group relative overflow-hidden rounded-lg bg-[#004795] px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-900/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#003b7d] hover:shadow-lg"
        >
          <span class="relative z-10">Register </span>

          <span
            class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-700 group-hover:translate-x-full"
          ></span>
        </router-link>
      </div>

      <!-- Mobile Menu Button -->
      <button
        type="button"
        class="flex h-10 w-10 items-center justify-center rounded-lg text-slate-700 transition hover:bg-slate-100 lg:hidden"
        :aria-expanded="isMobileMenuOpen"
        aria-label="Toggle navigation menu"
        @click="isMobileMenuOpen = !isMobileMenuOpen"
      >
        <X v-if="isMobileMenuOpen" class="h-6 w-6" />
        <Menu v-else class="h-6 w-6" />
      </button>
    </nav>

    <!-- Mobile Navigation -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="translate-y-[-10px] opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-[-10px] opacity-0"
    >
      <div
        v-if="isMobileMenuOpen"
        class="border-t border-slate-100 bg-white shadow-xl lg:hidden"
      >
        <div class="mx-auto max-w-7xl space-y-1 px-4 py-5 sm:px-6">
          <a
            v-for="item in navItems"
            :key="item.name"
            :href="item.href"
            class="block rounded-lg px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-blue-50 hover:text-[#004795]"
            @click="closeMobileMenu"
          >
            {{ item.name }}
          </a>

          <div class="my-3 border-t border-slate-100"></div>

          <!-- Mobile Language -->
          <button
            type="button"
            class="flex w-full items-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50"
          >
            <Globe2 class="h-4 w-4" />
            English
            <ChevronDown class="ml-auto h-4 w-4" />
          </button>

          <!-- Mobile Actions -->
          <div class="grid grid-cols-2 gap-3 pt-2">
            <router-link
              to="/login"
              class="rounded-lg border border-[#004795] px-4 py-3 text-center text-sm font-semibold text-[#004795] transition hover:bg-blue-50"
              @click="closeMobileMenu"
            >
              Login
            </router-link>

            <router-link
              to="/register"
              class="rounded-lg bg-[#004795] px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-[#003b7d]"
              @click="closeMobileMenu"
            >
              Register
            </router-link>
          </div>
        </div>
      </div>
    </Transition>
  </header>
</template>
