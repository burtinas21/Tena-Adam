<script setup>
import { ref } from "vue";

import { useRouter } from "vue-router";

import { useAuthStore } from "@/stores/authStore";

const router = useRouter();

const authStore = useAuthStore();

const email = ref("");

const loading = ref(false);

const message = ref("");

const errorMessage = ref("");

const handleSendResetLink = async () => {
  message.value = "";

  errorMessage.value = "";

  try {
    loading.value = true;

    const response = await authStore.forgotPassword({
      email: email.value,
    });

    message.value = response.message || "Reset link sent successfully";

    email.value = "";
  } catch (error) {
    errorMessage.value =
      error.response?.data?.message || "Unable to send reset link";
  } finally {
    loading.value = false;
  }
};
</script>
<template>
  <div
    class="min-h-screen bg-[#fafbfe] flex flex-col justify-center items-center p-6 font-sans text-slate-700 select-none"
  >
    <header class="text-center mb-6">
      <div
        class="text-[#004494] font-bold text-xl tracking-tight flex flex-col items-center gap-1"
      >
        <span class="font-extrabold text-2xl">Smart Care (TENA-ADAM)</span>
        <span class="text-xs text-slate-400 font-medium tracking-wide"
          >Secure Medical Access</span
        >
      </div>
    </header>
    <main class="w-full max-w-md">
      <div
        class="bg-white rounded-xl border-t-4 border-t-[#004494] border-x border-b border-slate-200 p-8 shadow-[0_4px_20px_rgba(0,0,0,0.02)]"
      >
        <div class="text-center mb-6">
          <div
            class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-50 text-[#004494] mb-4"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 11H19M9 11a3 3 0 116 0 3 3 0 01-6 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 14v3"
              />
            </svg>
          </div>

          <h1 class="text-2xl font-bold text-slate-800 mb-2">
            Forgot Password?
          </h1>
          <p
            class="text-xs text-slate-400 leading-relaxed max-w-[280px] mx-auto"
          >
            Enter your email address and we will send a secure password reset
            link.
          </p>
        </div>

        <form @submit.prevent="handleSendResetLink" class="space-y-5">
          <div
            v-if="message"
            class="bg-green-100 text-green-700 p-3 rounded text-sm"
          >
            {{ message }}
          </div>

          <div
            v-if="errorMessage"
            class="bg-red-100 text-red-700 p-3 rounded text-sm"
          >
            {{ errorMessage }}
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-bold text-slate-700 tracking-wide"
              >Email Address</label
            >
            <div class="relative">
              <span
                class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                  />
                </svg>
              </span>
              <input
                v-model="email"
                type="email"
                placeholder="name@example.com"
                required
                class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 placeholder-slate-400 bg-white text-slate-600"
              />
            </div>
          </div>
          <div class="pt-2">
            <button
              type="submit"
              class="w-full bg-[#0052cc] hover:bg-[#0041a3] text-white font-medium py-2.5 rounded-md shadow-sm flex items-center justify-center gap-1.5 transition-colors text-xs tracking-wider uppercase font-bold"
            >
              <span>
                {{ loading ? "Sending..." : "Send Reset Link" }}
              </span>
              <svg
                class="w-3.5 h-3.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 10V3L4 14h7v7l9-11h-7z"
                />
              </svg>
            </button>
          </div>
        </form>
      </div>
    </main>

    <footer class="mt-6">
      <router-link
        to="/login"
        class="inline-flex items-center gap-1.5 text-xs text-slate-500 font-bold hover:text-slate-800 transition-colors"
      >
        <span>←</span>
        <span>Back to Login</span>
      </router-link>
    </footer>
  </div>
</template>
