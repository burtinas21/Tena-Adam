<script setup>
import {
  HeartPulse,
  ShieldCheck,
  Users,
  Network,
  Target,
  Eye,
  CheckCircle2,
  ArrowRight,
  Sparkles,
} from "lucide-vue-next";

const values = [
  {
    title: "Patient First",
    description:
      "We put patients at the center of the healthcare experience and make essential care services easier to access.",
    icon: HeartPulse,
  },
  {
    title: "Trust & Security",
    description:
      "We prioritize secure access and responsible healthcare information management across the platform.",
    icon: ShieldCheck,
  },
  {
    title: "Connected Care",
    description:
      "We connect patients, doctors, departments, and healthcare organizations through one digital ecosystem.",
    icon: Network,
  },
  {
    title: "Better Collaboration",
    description:
      "We help healthcare teams coordinate their work and provide more efficient and connected care.",
    icon: Users,
  },
];

const platformPoints = [
  "Connect patients and healthcare providers",
  "Simplify healthcare management",
  "Improve access to healthcare services",
  "Support data-driven healthcare decisions",
];
</script>

<template>
  <section id="about" class="relative overflow-hidden bg-white py-24 sm:py-28">
    <!-- Background Decorations -->
    <div
      class="pointer-events-none absolute -left-40 top-20 h-96 w-96 rounded-full bg-blue-50 blur-3xl"
    ></div>

    <div
      class="pointer-events-none absolute -right-40 bottom-20 h-96 w-96 rounded-full bg-cyan-50 blur-3xl"
    ></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <!-- ================================================= -->
      <!-- SECTION HEADER -->
      <!-- ================================================= -->

      <div class="mx-auto max-w-3xl text-center">
        <div
          class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-4 py-2"
        >
          <Sparkles class="h-4 w-4 text-[#004795]" />

          <span
            class="text-xs font-bold uppercase tracking-[0.15em] text-[#004795]"
          >
            About TENA-ADAM
          </span>
        </div>

        <h2
          class="text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl lg:text-5xl"
        >
          Transforming Healthcare Through
          <span
            class="bg-gradient-to-r from-[#004795] to-cyan-500 bg-clip-text text-transparent"
          >
            Digital Innovation
          </span>
        </h2>

        <p
          class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg"
        >
          TENA-ADAM is a digital healthcare infrastructure platform designed to
          make healthcare more connected, accessible, efficient, and
          patient-centered.
        </p>
      </div>

      <!-- ================================================= -->
      <!-- ABOUT INTRO -->
      <!-- ================================================= -->

      <div class="mt-16 grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
        <!-- LEFT: Visual -->
        <div class="relative">
          <!-- Main Card -->
          <div
            class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#003b7d] via-[#004795] to-[#0077b6] p-8 shadow-2xl shadow-blue-950/20 sm:p-10"
          >
            <!-- Decorative Elements -->
            <div
              class="absolute -right-20 -top-20 h-64 w-64 rounded-full border border-white/10"
            ></div>

            <div
              class="absolute -bottom-32 -left-20 h-80 w-80 rounded-full border border-white/10"
            ></div>

            <div
              class="absolute right-10 top-10 h-20 w-20 rounded-full bg-cyan-400/10 blur-2xl"
            ></div>

            <div class="relative">
              <!-- Logo/Icon -->
              <div
                class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm"
              >
                <HeartPulse class="h-8 w-8 text-cyan-300" :stroke-width="2" />
              </div>

              <p
                class="mt-8 text-xs font-bold uppercase tracking-[0.2em] text-cyan-300"
              >
                TENA-ADAM
              </p>

              <h3
                class="mt-3 text-2xl font-extrabold leading-tight text-white sm:text-3xl"
              >
                A connected digital
                <span class="text-cyan-300"> healthcare ecosystem. </span>
              </h3>

              <p class="mt-5 max-w-lg text-sm leading-7 text-blue-100">
                We bring the essential parts of healthcare management together
                so patients and healthcare providers can focus on what matters
                most: better care.
              </p>

              <!-- Platform Network -->
              <div class="relative mt-10">
                <div class="grid grid-cols-3 gap-3">
                  <div
                    class="rounded-xl border border-white/10 bg-white/10 p-4 text-center backdrop-blur-sm"
                  >
                    <Users class="mx-auto h-5 w-5 text-cyan-300" />

                    <p class="mt-2 text-[10px] font-bold text-white">
                      Patients
                    </p>
                  </div>

                  <div
                    class="rounded-xl border border-white/10 bg-white/10 p-4 text-center backdrop-blur-sm"
                  >
                    <HeartPulse class="mx-auto h-5 w-5 text-cyan-300" />

                    <p class="mt-2 text-[10px] font-bold text-white">
                      Smart Care
                    </p>
                  </div>

                  <div
                    class="rounded-xl border border-white/10 bg-white/10 p-4 text-center backdrop-blur-sm"
                  >
                    <Network class="mx-auto h-5 w-5 text-cyan-300" />

                    <p class="mt-2 text-[10px] font-bold text-white">
                      Providers
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Floating Trust Card -->
          <div
            class="absolute -bottom-6 right-4 rounded-2xl border border-slate-100 bg-white p-4 shadow-xl sm:right-8"
          >
            <div class="flex items-center gap-3">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50"
              >
                <ShieldCheck class="h-5 w-5 text-emerald-500" />
              </div>

              <div>
                <p class="text-xs font-bold text-slate-900">Built with Trust</p>

                <p class="mt-0.5 text-[10px] text-slate-400">
                  Secure healthcare platform
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT: Content -->
        <div>
          <p
            class="text-xs font-bold uppercase tracking-[0.15em] text-[#004795]"
          >
            Our Story
          </p>

          <h3
            class="mt-3 text-2xl font-extrabold leading-tight text-slate-900 sm:text-3xl"
          >
            Making healthcare more connected and accessible
          </h3>

          <p class="mt-5 text-sm leading-7 text-slate-600 sm:text-base">
            Healthcare involves many different people, processes, and services.
            When these systems operate separately, patients may experience
            unnecessary delays while healthcare teams spend more time managing
            administrative tasks.
          </p>

          <p class="mt-4 text-sm leading-7 text-slate-600 sm:text-base">
            TENA-ADAM brings these experiences together through a centralized
            digital platform. It helps healthcare organizations manage their
            operations while giving patients and healthcare providers a more
            convenient way to connect and continue care.
          </p>

          <!-- Points -->
          <div class="mt-7 grid gap-3 sm:grid-cols-2">
            <div
              v-for="point in platformPoints"
              :key="point"
              class="flex items-start gap-3"
            >
              <CheckCircle2 class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" />

              <span class="text-sm font-semibold leading-5 text-slate-700">
                {{ point }}
              </span>
            </div>
          </div>

          <router-link
            to="/register"
            class="group mt-8 inline-flex items-center gap-2 rounded-xl bg-[#004795] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-900/15 transition-all duration-300 hover:-translate-y-1 hover:bg-[#003b7d]"
          >
            Experience TENA-ADAM

            <ArrowRight
              class="h-4 w-4 transition-transform group-hover:translate-x-1"
            />
          </router-link>
        </div>
      </div>

      <!-- ================================================= -->
      <!-- MISSION & VISION -->
      <!-- ================================================= -->

      <div class="mt-24 grid gap-6 md:grid-cols-2">
        <!-- Mission -->
        <div
          class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 p-7 transition-all duration-300 hover:-translate-y-1 hover:border-blue-100 hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 sm:p-8"
        >
          <div
            class="absolute right-0 top-0 h-32 w-32 rounded-full bg-blue-100/50 blur-3xl transition-transform duration-500 group-hover:scale-150"
          ></div>

          <div class="relative">
            <div
              class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-[#004795]"
            >
              <Target class="h-6 w-6" />
            </div>

            <p
              class="mt-6 text-xs font-bold uppercase tracking-[0.15em] text-[#004795]"
            >
              Our Mission
            </p>

            <h3 class="mt-2 text-xl font-extrabold text-slate-900">
              Making better care easier to deliver
            </h3>

            <p class="mt-4 text-sm leading-7 text-slate-500">
              Our mission is to provide healthcare organizations and patients
              with reliable digital tools that simplify healthcare management,
              improve connectivity, and support better healthcare experiences.
            </p>
          </div>
        </div>

        <!-- Vision -->
        <div
          class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 p-7 transition-all duration-300 hover:-translate-y-1 hover:border-cyan-100 hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 sm:p-8"
        >
          <div
            class="absolute right-0 top-0 h-32 w-32 rounded-full bg-cyan-100/50 blur-3xl transition-transform duration-500 group-hover:scale-150"
          ></div>

          <div class="relative">
            <div
              class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600"
            >
              <Eye class="h-6 w-6" />
            </div>

            <p
              class="mt-6 text-xs font-bold uppercase tracking-[0.15em] text-cyan-600"
            >
              Our Vision
            </p>

            <h3 class="mt-2 text-xl font-extrabold text-slate-900">
              A more connected healthcare future
            </h3>

            <p class="mt-4 text-sm leading-7 text-slate-500">
              We envision a healthcare ecosystem where technology connects
              people, services, and organizations to make quality healthcare
              more accessible, efficient, and convenient.
            </p>
          </div>
        </div>
      </div>

      <!-- ================================================= -->
      <!-- VALUES -->
      <!-- ================================================= -->

      <div class="mt-24">
        <div class="mx-auto max-w-2xl text-center">
          <p
            class="text-xs font-bold uppercase tracking-[0.15em] text-[#004795]"
          >
            What We Believe In
          </p>

          <h3 class="mt-3 text-2xl font-extrabold text-slate-900 sm:text-3xl">
            Values that guide our platform
          </h3>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <div
            v-for="value in values"
            :key="value.title"
            class="group rounded-2xl border border-slate-200 bg-white p-6 text-center transition-all duration-300 hover:-translate-y-2 hover:border-blue-100 hover:shadow-xl hover:shadow-blue-950/5"
          >
            <div
              class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-[#004795] transition-all duration-300 group-hover:bg-[#004795] group-hover:text-white"
            >
              <component :is="value.icon" class="h-5 w-5" />
            </div>

            <h4 class="mt-5 text-base font-bold text-slate-900">
              {{ value.title }}
            </h4>

            <p class="mt-2 text-sm leading-6 text-slate-500">
              {{ value.description }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
