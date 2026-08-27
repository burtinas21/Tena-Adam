<script setup>
import {
  ArrowRight,
  CheckCircle2,
  HeartPulse,
  ShieldCheck,
  Sparkles,
} from "lucide-vue-next";

const benefits = [
  "Easy and secure registration",
  "Access healthcare services in one place",
  "Connect with healthcare providers",
];
</script>

<template>
  <section id="cta" class="relative overflow-hidden bg-slate-50 py-20 sm:py-24">
    <!-- Background Decorations -->
    <div
      class="pointer-events-none absolute -left-32 top-1/2 h-80 w-80 -translate-y-1/2 rounded-full bg-blue-200/30 blur-3xl"
    ></div>

    <div
      class="pointer-events-none absolute -right-32 top-1/2 h-80 w-80 -translate-y-1/2 rounded-full bg-cyan-200/30 blur-3xl"
    ></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <!-- Main CTA Card -->
      <div
        class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#00346f] via-[#004795] to-[#0077b6] shadow-2xl shadow-blue-950/20"
      >
        <!-- Decorative Circles -->
        <div
          class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full border border-white/10"
        ></div>

        <div
          class="pointer-events-none absolute -bottom-40 -left-32 h-[28rem] w-[28rem] rounded-full border border-white/10"
        ></div>

        <div
          class="pointer-events-none absolute right-20 top-20 h-32 w-32 rounded-full bg-cyan-400/10 blur-3xl"
        ></div>

        <div
          class="relative grid gap-10 p-8 sm:p-10 lg:grid-cols-[1.4fr_0.8fr] lg:items-center lg:p-14"
        >
          <!-- ================================================= -->
          <!-- LEFT CONTENT -->
          <!-- ================================================= -->

          <div>
            <!-- Badge -->
            <div
              class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 backdrop-blur-sm"
            >
              <Sparkles class="h-4 w-4 text-cyan-300" />

              <span
                class="text-xs font-bold uppercase tracking-[0.15em] text-white"
              >
                Start Your Smart Care Journey
              </span>
            </div>

            <!-- Heading -->
            <h2
              class="mt-6 max-w-2xl text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl"
            >
              Better Healthcare Starts
              <span class="text-cyan-300"> With a Smarter Connection. </span>
            </h2>

            <!-- Description -->
            <p
              class="mt-5 max-w-2xl text-base leading-7 text-blue-100 sm:text-lg"
            >
              Join TENA-ADAM and experience a connected healthcare platform
              designed to make appointments, care, communication, and healthcare
              management simpler.
            </p>

            <!-- Buttons -->
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
              <!-- Primary -->
              <router-link
                to="/register"
                class="group inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-[#004795] shadow-lg transition-all duration-300 hover:-translate-y-1 hover:bg-blue-50"
              >
                Create Your Account

                <ArrowRight
                  class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
                />
              </router-link>

              <!-- Secondary -->
              <router-link
                to="/login"
                class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-6 py-3.5 text-sm font-bold text-white backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:bg-white/15"
              >
                Already have an account?
              </router-link>
            </div>

            <!-- Trust -->
            <div class="mt-7 flex flex-wrap items-center gap-x-6 gap-y-3">
              <div
                v-for="benefit in benefits"
                :key="benefit"
                class="flex items-center gap-2"
              >
                <CheckCircle2 class="h-4 w-4 text-emerald-300" />

                <span class="text-xs font-medium text-blue-100">
                  {{ benefit }}
                </span>
              </div>
            </div>
          </div>

          <!-- ================================================= -->
          <!-- RIGHT VISUAL -->
          <!-- ================================================= -->

          <div class="relative hidden lg:block">
            <!-- Main Glass Card -->
            <div
              class="relative mx-auto max-w-sm rounded-3xl border border-white/15 bg-white/10 p-6 shadow-2xl backdrop-blur-md"
            >
              <!-- Icon -->
              <div
                class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10"
              >
                <HeartPulse class="h-8 w-8 text-cyan-300" :stroke-width="2" />
              </div>

              <p
                class="mt-6 text-xs font-bold uppercase tracking-[0.15em] text-cyan-300"
              >
                Smart Care
              </p>

              <h3 class="mt-2 text-xl font-extrabold text-white">
                Your healthcare,
                <br />
                connected.
              </h3>

              <!-- Mini Stats -->
              <div class="mt-6 grid grid-cols-2 gap-3">
                <div class="rounded-xl border border-white/10 bg-white/10 p-4">
                  <p class="text-xs text-blue-200">Healthcare</p>

                  <p class="mt-1 text-sm font-bold text-white">Connected</p>
                </div>

                <div class="rounded-xl border border-white/10 bg-white/10 p-4">
                  <p class="text-xs text-blue-200">Experience</p>

                  <p class="mt-1 text-sm font-bold text-white">Simplified</p>
                </div>
              </div>

              <!-- Secure Badge -->
              <div
                class="mt-4 flex items-center gap-3 rounded-xl border border-emerald-300/10 bg-emerald-400/10 p-3"
              >
                <div
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-400/15"
                >
                  <ShieldCheck class="h-5 w-5 text-emerald-300" />
                </div>

                <div>
                  <p class="text-xs font-bold text-white">Secure Healthcare</p>

                  <p class="mt-0.5 text-[10px] text-blue-200">
                    Built with trust in mind
                  </p>
                </div>
              </div>
            </div>

            <!-- Floating Decoration -->
            <div
              class="absolute -right-5 top-10 flex h-12 w-12 items-center justify-center rounded-2xl border border-white/15 bg-white/10 shadow-lg backdrop-blur-md"
            >
              <CheckCircle2 class="h-5 w-5 text-emerald-300" />
            </div>

            <div
              class="absolute -bottom-5 -left-5 flex h-12 w-12 items-center justify-center rounded-2xl border border-white/15 bg-white/10 shadow-lg backdrop-blur-md"
            >
              <HeartPulse class="h-5 w-5 text-cyan-300" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
