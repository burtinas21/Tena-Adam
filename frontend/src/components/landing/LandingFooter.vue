<script setup>
import {
  HeartPulse,
  Facebook,
  Instagram,
  Linkedin,
  Twitter,
  Mail,
  Phone,
  MapPin,
  ArrowUp,
} from "lucide-vue-next";

const currentYear = new Date().getFullYear();

const footerLinks = {
  platform: [
    { name: "Home", href: "#home" },
    { name: "Services", href: "#services" },
    { name: "About Us", href: "#about" },
    { name: "Contact", href: "#contact" },
  ],

  services: [
    { name: "Appointments", href: "#services" },
    { name: "Electronic Medical Records", href: "#services" },
    { name: "Telemedicine", href: "#services" },
    { name: "Queue Management", href: "#services" },
  ],

  support: [
    { name: "Help Center", href: "#contact" },
    { name: "Contact Support", href: "#contact" },
    { name: "Privacy Policy", href: "#" },
    { name: "Terms of Service", href: "#" },
  ],
};

const socialLinks = [
  {
    name: "Facebook",
    icon: Facebook,
    href: "#",
  },
  {
    name: "Twitter",
    icon: Twitter,
    href: "#",
  },
  {
    name: "Instagram",
    icon: Instagram,
    href: "#",
  },
  {
    name: "LinkedIn",
    icon: Linkedin,
    href: "#",
  },
];

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
};
</script>

<template>
  <footer class="relative overflow-hidden bg-slate-950 text-white">
    <!-- Decorative Background -->
    <div
      class="pointer-events-none absolute -left-32 -top-32 h-80 w-80 rounded-full bg-blue-600/10 blur-3xl"
    ></div>

    <div
      class="pointer-events-none absolute -bottom-40 right-0 h-96 w-96 rounded-full bg-cyan-500/10 blur-3xl"
    ></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <!-- Main Footer -->
      <div class="grid gap-12 py-16 md:grid-cols-2 lg:grid-cols-5">
        <!-- Brand -->
        <div class="lg:col-span-2">
          <a href="#home" class="group inline-flex items-center gap-3">
            <div
              class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-[#0ea5e9] to-[#004795] shadow-lg shadow-blue-950/30 transition-transform duration-300 group-hover:scale-105"
            >
              <HeartPulse class="h-6 w-6 text-white" :stroke-width="2.2" />
            </div>

            <div>
              <div class="text-xl font-extrabold tracking-tight">
                TENA<span class="text-cyan-400">-ADAM</span>
              </div>

              <p
                class="text-[10px] font-medium uppercase tracking-[0.18em] text-slate-400"
              >
                Smart Healthcare
              </p>
            </div>
          </a>

          <p class="mt-6 max-w-md text-sm leading-7 text-slate-400">
            A modern digital healthcare platform connecting patients, healthcare
            providers, and hospitals through secure and intelligent healthcare
            technology.
          </p>

          <!-- Contact Info -->
          <div class="mt-7 space-y-3">
            <a
              href="mailto:info@tena-adam.com"
              class="flex items-center gap-3 text-sm text-slate-400 transition hover:text-cyan-400"
            >
              <Mail class="h-4 w-4 text-cyan-400" />
              info@tena-adam.com
            </a>

            <a
              href="tel:+251000000000"
              class="flex items-center gap-3 text-sm text-slate-400 transition hover:text-cyan-400"
            >
              <Phone class="h-4 w-4 text-cyan-400" />
              +251 00 000 0000
            </a>

            <div class="flex items-center gap-3 text-sm text-slate-400">
              <MapPin class="h-4 w-4 text-cyan-400" />
              Ethiopia
            </div>
          </div>
        </div>

        <!-- Platform -->
        <div>
          <h3
            class="mb-5 text-sm font-bold uppercase tracking-wider text-white"
          >
            Platform
          </h3>

          <ul class="space-y-3">
            <li v-for="link in footerLinks.platform" :key="link.name">
              <a
                :href="link.href"
                class="text-sm text-slate-400 transition hover:text-cyan-400"
              >
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Services -->
        <div>
          <h3
            class="mb-5 text-sm font-bold uppercase tracking-wider text-white"
          >
            Services
          </h3>

          <ul class="space-y-3">
            <li v-for="link in footerLinks.services" :key="link.name">
              <a
                :href="link.href"
                class="text-sm leading-6 text-slate-400 transition hover:text-cyan-400"
              >
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Support -->
        <div>
          <h3
            class="mb-5 text-sm font-bold uppercase tracking-wider text-white"
          >
            Support
          </h3>

          <ul class="space-y-3">
            <li v-for="link in footerLinks.support" :key="link.name">
              <a
                :href="link.href"
                class="text-sm text-slate-400 transition hover:text-cyan-400"
              >
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Newsletter / CTA -->
      <div
        class="flex flex-col gap-6 rounded-2xl border border-white/10 bg-white/[0.03] p-6 md:flex-row md:items-center md:justify-between"
      >
        <div>
          <h3 class="font-bold text-white">
            Better technology. Better healthcare.
          </h3>

          <p class="mt-1 text-sm text-slate-400">
            Experience smarter healthcare management with TENA-ADAM.
          </p>
        </div>

        <router-link
          to="/register"
          class="inline-flex items-center justify-center rounded-lg bg-[#004795] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#005ab8]"
        >
          Get Started
        </router-link>
      </div>

      <!-- Bottom Footer -->
      <div
        class="mt-10 flex flex-col gap-5 border-t border-white/10 py-7 md:flex-row md:items-center md:justify-between"
      >
        <p class="text-sm text-slate-500">
          © {{ currentYear }} TENA-ADAM. All rights reserved.
        </p>

        <!-- Social -->
        <div class="flex items-center gap-2">
          <a
            v-for="social in socialLinks"
            :key="social.name"
            :href="social.href"
            :aria-label="social.name"
            class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/5 text-slate-400 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#004795] hover:text-white"
          >
            <component :is="social.icon" class="h-4 w-4" />
          </a>

          <!-- Back To Top -->
          <button
            type="button"
            aria-label="Back to top"
            class="ml-2 flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 text-slate-400 transition-all duration-200 hover:-translate-y-0.5 hover:border-cyan-400 hover:text-cyan-400"
            @click="scrollToTop"
          >
            <ArrowUp class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>
  </footer>
</template>
