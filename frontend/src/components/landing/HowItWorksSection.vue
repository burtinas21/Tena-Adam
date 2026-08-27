<script setup>
import {
  UserPlus,
  Search,
  CalendarCheck,
  Stethoscope,
  FileText,
  ArrowRight,
  CheckCircle2,
  HeartPulse,
  Sparkles,
} from "lucide-vue-next";

const steps = [
  {
    number: "01",
    title: "Create Your Account",
    description:
      "Register securely and create your profile to access the Smart Care platform and its healthcare services.",
    icon: UserPlus,
    color: "blue",
  },
  {
    number: "02",
    title: "Find the Right Care",
    description:
      "Explore healthcare providers, departments, and available services based on your healthcare needs.",
    icon: Search,
    color: "cyan",
  },
  {
    number: "03",
    title: "Book an Appointment",
    description:
      "Choose a doctor, select an available time, and conveniently schedule your appointment online.",
    icon: CalendarCheck,
    color: "emerald",
  },
  {
    number: "04",
    title: "Connect With Your Doctor",
    description:
      "Visit your appointment in person or connect remotely through supported telemedicine services.",
    icon: Stethoscope,
    color: "violet",
  },
  {
    number: "05",
    title: "Continue Your Care",
    description:
      "Access medical records, prescriptions, follow-ups, notifications, and other healthcare services.",
    icon: FileText,
    color: "orange",
  },
];

const benefits = [
  "Simple digital healthcare experience",
  "Connected patients and providers",
  "Secure healthcare information",
  "Convenient appointment management",
];

const colorClasses = {
  blue: {
    icon: "bg-blue-50 text-[#004795]",
    number: "bg-[#004795]",
  },
  cyan: {
    icon: "bg-cyan-50 text-cyan-600",
    number: "bg-cyan-600",
  },
  emerald: {
    icon: "bg-emerald-50 text-emerald-600",
    number: "bg-emerald-600",
  },
  violet: {
    icon: "bg-violet-50 text-violet-600",
    number: "bg-violet-600",
  },
  orange: {
    icon: "bg-orange-50 text-orange-600",
    number: "bg-orange-600",
  },
};
</script>

<template>
  <section
    id="how-it-works"
    class="relative overflow-hidden bg-slate-50 py-24 sm:py-28"
  >
    <!-- Background Decorations -->
    <div
      class="pointer-events-none absolute left-0 top-0 h-96 w-96 rounded-full bg-blue-100/40 blur-3xl"
    ></div>

    <div
      class="pointer-events-none absolute bottom-0 right-0 h-96 w-96 rounded-full bg-cyan-100/40 blur-3xl"
    ></div>

    <div
      class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"
    >
      <!-- ================================================= -->
      <!-- SECTION HEADER -->
      <!-- ================================================= -->

      <div class="mx-auto max-w-3xl text-center">
        <div
          class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-100 bg-white px-4 py-2 shadow-sm"
        >
          <Sparkles class="h-4 w-4 text-[#004795]" />

          <span
            class="text-xs font-bold uppercase tracking-[0.15em] text-[#004795]"
          >
            Simple Healthcare Journey
          </span>
        </div>

        <h2
          class="text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl lg:text-5xl"
        >
          Healthcare Made
          <span
            class="bg-gradient-to-r from-[#004795] to-cyan-500 bg-clip-text text-transparent"
          >
            Simple
          </span>
        </h2>

        <p
          class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg"
        >
          From registration to consultation and follow-up care, TENA-ADAM
          connects every important step through one easy-to-use digital
          healthcare platform.
        </p>
      </div>

      <!-- ================================================= -->
      <!-- PROCESS FLOW -->
      <!-- ================================================= -->

      <div class="relative mt-16">
        <!-- Desktop Connecting Line -->
        <div
          class="absolute left-[10%] right-[10%] top-9 hidden h-px bg-gradient-to-r from-[#004795]/20 via-cyan-400/50 to-[#004795]/20 lg:block"
        ></div>

        <div
          class="grid gap-8 sm:grid-cols-2 lg:grid-cols-5 lg:gap-4"
        >
          <div
            v-for="(step, index) in steps"
            :key="step.number"
            class="group relative"
          >
            <!-- Mobile/Tablet Connector -->
            <div
              v-if="index < steps.length - 1"
              class="absolute left-6 top-16 hidden h-[calc(100%+2rem)] w-px bg-gradient-to-b from-blue-200 to-transparent sm:block lg:hidden"
            ></div>

            <!-- Step Number -->
            <div class="relative flex justify-center">
              <div
                class="relative z-10 flex h-[72px] w-[72px] items-center justify-center rounded-full border-8 border-slate-50 text-sm font-extrabold text-white shadow-lg transition-all duration-300 group-hover:scale-110 group-hover:shadow-xl"
                :class="colorClasses[step.color].number"
              >
                {{ step.number }}

                <!-- Icon Overlay -->
                <div
                  class="absolute -bottom-1 -right-1 flex h-7 w-7 items-center justify-center rounded-full border-2 border-white bg-white shadow-sm"
                >
                  <component
                    :is="step.icon"
                    class="h-3.5 w-3.5 text-[#004795]"
                  />
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="mt-6 text-center">
              <h3
                class="text-base font-bold text-slate-900 transition-colors duration-300 group-hover:text-[#004795]"
              >
                {{ step.title }}
              </h3>

              <p
                class="mx-auto mt-3 max-w-[220px] text-sm leading-6 text-slate-500"
              >
                {{ step.description }}
              </p>
            </div>

            <!-- Arrow -->
            <div
              v-if="index < steps.length - 1"
              class="absolute -right-3 top-7 z-20 hidden lg:block"
            >
              <ArrowRight
                class="h-5 w-5 text-slate-300 transition-all duration-300 group-hover:translate-x-1 group-hover:text-[#004795]"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- ================================================= -->
      <!-- BOTTOM INFORMATION -->
      <!-- ================================================= -->

      <div
        class="mt-20 grid overflow-hidden rounded-3xl bg-white shadow-xl shadow-slate-900/5 lg:grid-cols-2"
      >
        <!-- Left Visual -->
        <div
          class="relative overflow-hidden bg-gradient-to-br from-[#003b7d] via-[#004795] to-[#0077b6] p-8 sm:p-10 lg:p-12"
        >
          <!-- Decorative Circles -->
          <div
            class="absolute -right-24 -top-24 h-72 w-72 rounded-full border border-white/10"
          ></div>

          <div
            class="absolute -bottom-32 -left-20 h-80 w-80 rounded-full border border-white/10"
          ></div>

          <div class="relative">
            <!-- Icon -->
            <div
              class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm"
            >
              <HeartPulse
                class="h-7 w-7 text-cyan-300"
                :stroke-width="2"
              />
            </div>

            <h3
              class="mt-6 max-w-md text-2xl font-extrabold leading-tight text-white sm:text-3xl"
            >
              One journey.
              <span class="text-cyan-300">
                Connected care.
              </span>
            </h3>

            <p
              class="mt-4 max-w-lg text-sm leading-7 text-blue-100 sm:text-base"
            >
              TENA-ADAM brings patients, doctors, hospitals, and healthcare
              services together so that every step of the healthcare journey
              works as one connected experience.
            </p>

            <!-- Mini Flow -->
            <div class="mt-8 flex flex-wrap items-center gap-2">
              <div
                class="rounded-lg border border-white/10 bg-white/10 px-3 py-2 text-xs font-semibold text-white"
              >
                Patient
              </div>

              <ArrowRight class="h-4 w-4 text-cyan-300" />

              <div
                class="rounded-lg border border-white/10 bg-white/10 px-3 py-2 text-xs font-semibold text-white"
              >
                Appointment
              </div>

              <ArrowRight class="h-4 w-4 text-cyan-300" />

              <div
                class="rounded-lg border border-white/10 bg-white/10 px-3 py-2 text-xs font-semibold text-white"
              >
                Doctor
              </div>

              <ArrowRight class="h-4 w-4 text-cyan-300" />

              <div
                class="rounded-lg border border-white/10 bg-white/10 px-3 py-2 text-xs font-semibold text-white"
              >
                Care
              </div>
            </div>
          </div>
        </div>

        <!-- Right Benefits -->
        <div class="p-8 sm:p-10 lg:p-12">
          <p
            class="text-xs font-bold uppercase tracking-[0.15em] text-[#004795]"
          >
            Designed Around You
          </p>

          <h3
            class="mt-3 text-2xl font-extrabold text-slate-900"
          >
            Everything connected in one place
          </h3>

          <p
            class="mt-4 text-sm leading-7 text-slate-500"
          >
            Whether you are a patient looking for care or a healthcare provider
            managing daily operations, TENA-ADAM keeps your essential tools
            connected and easy to access.
          </p>

          <!-- Benefits -->
          <div class="mt-7 space-y-4">
            <div
              v-for="benefit in benefits"
              :key="benefit"
              class="flex items-center gap-3"
            >
              <div
                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-50"
              >
                <CheckCircle2
                  class="h-4 w-4 text-emerald-500"
                />
              </div>

              <span
                class="text-sm font-semibold text-slate-700"
              >
                {{ benefit }}
              </span>
            </div>
          </div>

          <!-- CTA -->
          <router-link
            to="/register"
            class="group mt-8 inline-flex items-center gap-2 text-sm font-bold text-[#004795]"
          >
            Start using Smart Care

            <ArrowRight
              class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
            />
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>