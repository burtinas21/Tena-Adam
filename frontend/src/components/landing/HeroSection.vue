<script setup>
import {
  ArrowRight,
  Play,
  CheckCircle2,
  ShieldCheck,
  Stethoscope,
  CalendarCheck,
  HeartPulse,
  Sparkles,
} from "lucide-vue-next";

const scrollToServices = () => {
  document.querySelector("#services")?.scrollIntoView({
    behavior: "smooth",
  });
};
</script>

<template>
  <section
    id="home"
    class="relative min-h-screen overflow-hidden bg-white pt-20"
  >
    <!-- Background Decorations -->
    <div
      class="pointer-events-none absolute -left-40 top-20 h-96 w-96 rounded-full bg-blue-100/60 blur-3xl"
    ></div>

    <div
      class="pointer-events-none absolute -right-40 top-32 h-[500px] w-[500px] rounded-full bg-cyan-100/50 blur-3xl"
    ></div>

    <div
      class="pointer-events-none absolute bottom-0 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-blue-50 blur-3xl"
    ></div>

    <!-- Subtle Grid -->
    <div
      class="pointer-events-none absolute inset-0 opacity-[0.035]"
      style="
        background-image:
          linear-gradient(#004795 1px, transparent 1px),
          linear-gradient(90deg, #004795 1px, transparent 1px);
        background-size: 50px 50px;
      "
    ></div>

    <div
      class="relative mx-auto grid min-h-[calc(100vh-5rem)] max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-20"
    >

      <div class="max-w-2xl">
        <!-- Badge -->
        <div
          class="mb-6 inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-4 py-2"
        >
          <span class="relative flex h-2.5 w-2.5">
            <span
              class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"
            ></span>

            <span
              class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"
            ></span>
          </span>

          <span
            class="text-xs font-bold uppercase tracking-wider text-[#004795] sm:text-sm"
          >
            The Future of Digital Healthcare
          </span>

          <Sparkles class="h-4 w-4 text-cyan-500" />
        </div>

        <!-- Heading -->
        <h1
          class="text-4xl font-extrabold leading-[1.08] tracking-tight text-slate-950 sm:text-5xl lg:text-6xl xl:text-7xl"
        >
          Smarter Healthcare.
          <span
            class="mt-2 block bg-gradient-to-r from-[#004795] via-[#0067b9] to-cyan-500 bg-clip-text text-transparent"
          >
            Better Care.
          </span>
        </h1>

        <!-- Description -->
        <p
          class="mt-6 max-w-xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8"
        >
          TENA-ADAM connects patients, doctors, and healthcare organizations
          through one intelligent digital healthcare platform—making healthcare
          management simpler, faster, and more accessible.
        </p>

        <!-- CTA Buttons -->
        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
          <router-link
            to="/register"
            class="group inline-flex items-center justify-center gap-2 rounded-xl bg-[#004795] px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-blue-900/20 transition-all duration-300 hover:-translate-y-1 hover:bg-[#003b7d] hover:shadow-2xl hover:shadow-blue-900/25"
          >
            Get Started

            <ArrowRight
              class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
            />
          </router-link>

          <button
            type="button"
            class="group inline-flex items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-6 py-3.5 text-sm font-bold text-slate-700 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-200 hover:bg-blue-50 hover:text-[#004795]"
            @click="scrollToServices"
          >
            <span
              class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-50 text-[#004795] transition group-hover:bg-white"
            >
              <Play class="ml-0.5 h-3.5 w-3.5 fill-current" />
            </span>

            Explore Platform
          </button>
        </div>

        <!-- Trust Points -->
        <div class="mt-8 grid gap-3 sm:grid-cols-3">
          <div class="flex items-center gap-2">
            <CheckCircle2 class="h-5 w-5 shrink-0 text-emerald-500" />

            <span class="text-xs font-semibold text-slate-600">
              Secure Platform
            </span>
          </div>

          <div class="flex items-center gap-2">
            <CheckCircle2 class="h-5 w-5 shrink-0 text-emerald-500" />

            <span class="text-xs font-semibold text-slate-600">
              Patient-Centered
            </span>
          </div>

          <div class="flex items-center gap-2">
            <CheckCircle2 class="h-5 w-5 shrink-0 text-emerald-500" />

            <span class="text-xs font-semibold text-slate-600">
              Easy to Use
            </span>
          </div>
        </div>
      </div>

      <!-- ================================================= -->
      <!-- RIGHT VISUAL -->
      <!-- ================================================= -->

      <div class="relative mx-auto w-full max-w-xl lg:max-w-none">
        <!-- Main Glow -->
        <div
          class="absolute inset-10 rounded-[3rem] bg-gradient-to-br from-blue-300/30 via-cyan-200/20 to-transparent blur-3xl"
        ></div>

        <!-- Dashboard Container -->
        <div
          class="relative rounded-[2rem] border border-slate-200/80 bg-white/90 p-3 shadow-2xl shadow-blue-950/10 backdrop-blur-xl"
        >
          <!-- Browser/Dashboard Header -->
          <div
            class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3"
          >
            <div class="flex items-center gap-2">
              <div class="h-2.5 w-2.5 rounded-full bg-red-400"></div>
              <div class="h-2.5 w-2.5 rounded-full bg-amber-400"></div>
              <div class="h-2.5 w-2.5 rounded-full bg-emerald-400"></div>
            </div>

            <div
              class="hidden rounded-md bg-white px-5 py-1.5 text-[9px] font-medium text-slate-400 shadow-sm sm:block"
            >
              app.tena-adam.com
            </div>

            <div class="h-5 w-12 rounded-md bg-slate-200"></div>
          </div>

          <!-- Dashboard -->
          <div class="mt-3 overflow-hidden rounded-xl bg-slate-50">
            <!-- Dashboard Top -->
            <div
              class="flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4"
            >
              <div>
                <p class="text-[10px] font-medium text-slate-400">
                  SMART CARE PLATFORM
                </p>

                <h3 class="mt-0.5 text-sm font-bold text-slate-900">
                  Healthcare Dashboard
                </h3>
              </div>

              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50"
              >
                <HeartPulse class="h-4 w-4 text-[#004795]" />
              </div>
            </div>

            <!-- Dashboard Content -->
            <div class="grid gap-3 p-4 sm:grid-cols-2">
              <!-- Appointment Card -->
              <div
                class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm"
              >
                <div class="flex items-center justify-between">
                  <div
                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50"
                  >
                    <CalendarCheck class="h-4 w-4 text-[#004795]" />
                  </div>

                  <span
                    class="rounded-full bg-emerald-50 px-2 py-1 text-[9px] font-bold text-emerald-600"
                  >
                    Today
                  </span>
                </div>

                <p class="mt-4 text-[10px] font-medium text-slate-400">
                  APPOINTMENTS
                </p>

                <p class="mt-1 text-2xl font-extrabold text-slate-900">128</p>

                <div
                  class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100"
                >
                  <div
                    class="h-full w-[72%] rounded-full bg-gradient-to-r from-[#004795] to-cyan-400"
                  ></div>
                </div>
              </div>

              <!-- Doctors Card -->
              <div
                class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm"
              >
                <div class="flex items-center justify-between">
                  <div
                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-cyan-50"
                  >
                    <Stethoscope class="h-4 w-4 text-cyan-600" />
                  </div>

                  <span
                    class="rounded-full bg-emerald-50 px-2 py-1 text-[9px] font-bold text-emerald-600"
                  >
                    Online
                  </span>
                </div>

                <p class="mt-4 text-[10px] font-medium text-slate-400">
                  AVAILABLE DOCTORS
                </p>

                <p class="mt-1 text-2xl font-extrabold text-slate-900">42</p>

                <div class="mt-2 flex -space-x-2">
                  <div
                    v-for="n in 4"
                    :key="n"
                    class="flex h-6 w-6 items-center justify-center rounded-full border-2 border-white bg-gradient-to-br from-blue-300 to-blue-600 text-[7px] font-bold text-white"
                  >
                    D
                  </div>

                  <div
                    class="flex h-6 w-6 items-center justify-center rounded-full border-2 border-white bg-slate-100 text-[7px] font-bold text-slate-500"
                  >
                    +38
                  </div>
                </div>
              </div>

              <!-- Patient Activity -->
              <div
                class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm sm:col-span-2"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-[10px] font-medium text-slate-400">
                      PATIENT ACTIVITY
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-900">
                      Healthcare Overview
                    </p>
                  </div>

                  <div
                    class="rounded-lg bg-emerald-50 px-2.5 py-1.5 text-[9px] font-bold text-emerald-600"
                  >
                    +18.6%
                  </div>
                </div>

                <!-- Fake Chart -->
                <div class="mt-5 flex h-24 items-end gap-2">
                  <div
                    v-for="(height, index) in [
                      35, 48, 42, 62, 55, 75, 68, 86, 72, 94,
                    ]"
                    :key="index"
                    class="group relative flex-1"
                  >
                    <div
                      class="absolute bottom-0 w-full rounded-t-md bg-gradient-to-t from-[#004795] to-cyan-400 opacity-80 transition-all duration-300 group-hover:opacity-100"
                      :style="{ height: `${height}%` }"
                    ></div>
                  </div>
                </div>

                <div
                  class="mt-2 flex justify-between text-[8px] text-slate-400"
                >
                  <span>Mon</span>
                  <span>Tue</span>
                  <span>Wed</span>
                  <span>Thu</span>
                  <span>Fri</span>
                  <span>Sat</span>
                  <span>Sun</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Floating Security Card -->
        <div
          class="absolute -left-4 top-16 hidden animate-[float_4s_ease-in-out_infinite] rounded-2xl border border-white bg-white p-3 shadow-xl shadow-slate-900/10 sm:block lg:-left-8"
        >
          <div class="flex items-center gap-3">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50"
            >
              <ShieldCheck class="h-5 w-5 text-emerald-500" />
            </div>

            <div>
              <p class="text-[10px] font-bold text-slate-900">
                Secure & Protected
              </p>

              <p class="mt-0.5 text-[9px] text-slate-400">Your data matters</p>
            </div>
          </div>
        </div>

        <!-- Floating Healthcare Card -->
        <div
          class="absolute -bottom-5 -right-3 hidden animate-[float_5s_ease-in-out_infinite] rounded-2xl border border-white bg-white p-3 shadow-xl shadow-slate-900/10 sm:block lg:-right-8"
        >
          <div class="flex items-center gap-3">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50"
            >
              <HeartPulse class="h-5 w-5 text-[#004795]" />
            </div>

            <div>
              <p class="text-[10px] font-bold text-slate-900">Connected Care</p>

              <p class="mt-0.5 text-[9px] text-slate-400">
                One platform, better care
              </p>
            </div>
          </div>
        </div>

        <!-- Decorative Dots -->
        <div
          class="absolute -right-5 top-10 grid grid-cols-4 gap-1.5 opacity-50"
        >
          <span
            v-for="n in 16"
            :key="n"
            class="h-1.5 w-1.5 rounded-full bg-[#004795]"
          ></span>
        </div>
      </div>
    </div>

    <!-- Bottom Wave -->
    <div class="absolute bottom-0 left-0 right-0 h-16 overflow-hidden">
      <svg
        class="absolute bottom-0 h-full w-full"
        viewBox="0 0 1440 80"
        preserveAspectRatio="none"
        fill="none"
      >
        <path
          d="M0 48C240 90 480 10 720 40C960 70 1200 20 1440 45V80H0V48Z"
          fill="#f8fafc"
        />
      </svg>
    </div>
  </section>
</template>

<style scoped>
@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-8px);
  }
}
</style>
