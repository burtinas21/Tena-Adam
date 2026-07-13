<script setup>
import RegisterForm from "../../components/auth/Register.vue";
</script>
<template>
  <div
    class="min-h-screen bg-slate-50 flex flex-col justify-between font-sans text-slate-700 select-none"
  >
    <!-- Header -->
    <header
      class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shadow-sm"
    >
      <div
        class="flex items-center gap-2 text-blue-600 font-bold text-xl tracking-tight"
      >
        <span
          class="bg-blue-600 text-white rounded-md p-1.5 flex items-center justify-center text-sm font-black w-7 h-7"
        >
          ✚
        </span>

        <span>Smart Care</span>
      </div>

      <a
        href="#"
        class="text-xs text-slate-500 hover:text-slate-800 transition-colors"
      >
        Help Center
      </a>
    </header>

    <!-- Main -->
    <main class="flex-1 flex items-center justify-center my-3 px-4">
      <RegisterForm />
    </main>

    <!-- Footer -->
    <footer
      class="bg-white border-t border-slate-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-center text-[11px] text-slate-400 gap-2"
    >
      <div>
        <span class="font-bold text-slate-600 mr-2">
          Smart Care
        </span>

        <span>
          © 2024 Smart Care Ethiopia. All Rights Reserved. Secure &
          HIPAA Compliant.
        </span>
      </div>

      <div class="flex gap-4">
        <a href="#" class="hover:text-slate-600">
          Privacy Policy
        </a>

        <a href="#" class="hover:text-slate-600">
          Terms of Service
        </a>

        <a href="#" class="hover:text-slate-600">
          Help Center
        </a>

        <a href="#" class="hover:text-slate-600">
          Contact Support
        </a>
      </div>
    </footer>
  </div>
</template>

