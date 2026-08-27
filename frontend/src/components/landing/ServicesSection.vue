<script setup>
import {
  CalendarCheck,
  Users,
  Stethoscope,
  FileText,
  Video,
  Activity,
  Bell,
  BarChart3,
  ArrowRight,
  CheckCircle2,
  Sparkles,
} from "lucide-vue-next";

const services = [
  {
    title: "Appointment Management",
    description:
      "Book, manage, and track appointments with doctors through a simple and efficient digital workflow.",
    icon: CalendarCheck,
    color: "blue",
    features: ["Online booking", "Doctor schedules", "Appointment tracking"],
  },
  {
    title: "Patient Management",
    description:
      "Maintain organized patient information and provide a seamless experience throughout the healthcare journey.",
    icon: Users,
    color: "cyan",
    features: ["Patient profiles", "Medical history", "Patient tracking"],
  },
  {
    title: "Doctor Management",
    description:
      "Help healthcare providers manage schedules, appointments, patients, and clinical activities efficiently.",
    icon: Stethoscope,
    color: "emerald",
    features: ["Doctor profiles", "Schedules", "Workload management"],
  },
  {
    title: "Electronic Medical Records",
    description:
      "Keep important patient medical information organized, accessible, and securely managed in one place.",
    icon: FileText,
    color: "violet",
    features: ["Medical history", "Clinical records", "Prescriptions"],
  },
  {
    title: "Telemedicine",
    description:
      "Connect patients with healthcare professionals remotely through secure and convenient virtual consultations.",
    icon: Video,
    color: "orange",
    features: ["Virtual consultation", "Remote care", "Follow-up"],
  },
  {
    title: "Smart Health Guidance",
    description:
      "Guide patients toward appropriate healthcare services using intelligent symptom-based recommendations.",
    icon: Activity,
    color: "rose",
    features: ["Symptom guidance", "Department suggestions", "Care navigation"],
  },
];

const additionalServices = [
  {
    title: "Queue Management",
    icon: Users,
  },
  {
    title: "Notifications",
    icon: Bell,
  },
  {
    title: "Reports & Analytics",
    icon: BarChart3,
  },
];

const colorClasses = {
  blue: {
    icon: "bg-blue-50 text-[#004795]",
    hover: "group-hover:bg-[#004795] group-hover:text-white",
    line: "bg-[#004795]",
  },
  cyan: {
    icon: "bg-cyan-50 text-cyan-600",
    hover: "group-hover:bg-cyan-500 group-hover:text-white",
    line: "bg-cyan-500",
  },
  emerald: {
    icon: "bg-emerald-50 text-emerald-600",
    hover: "group-hover:bg-emerald-500 group-hover:text-white",
    line: "bg-emerald-500",
  },
  violet: {
    icon: "bg-violet-50 text-violet-600",
    hover: "group-hover:bg-violet-500 group-hover:text-white",
    line: "bg-violet-500",
  },
  orange: {
    icon: "bg-orange-50 text-orange-600",
    hover: "group-hover:bg-orange-500 group-hover:text-white",
    line: "bg-orange-500",
  },
  rose: {
    icon: "bg-rose-50 text-rose-600",
    hover: "group-hover:bg-rose-500 group-hover:text-white",
    line: "bg-rose-500",
  },
};
</script>

<template>
  <section
    id="services"
    class="relative overflow-hidden bg-slate-50 py-24 sm:py-28"
  >
    <!-- Background Decorations -->
    <div
      class="pointer-events-none absolute -left-40 top-20 h-96 w-96 rounded-full bg-blue-100/40 blur-3xl"
    ></div>

    <div
      class="pointer-events-none absolute -right-40 bottom-20 h-96 w-96 rounded-full bg-cyan-100/40 blur-3xl"
    ></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <!-- ================================================= -->
      <!-- SECTION HEADER -->
      <!-- ================================================= -->

      <div class="mx-auto max-w-3xl text-center">
        <!-- Small Badge -->
        <div
          class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-100 bg-white px-4 py-2 shadow-sm"
        >
          <Sparkles class="h-4 w-4 text-[#004795]" />

          <span
            class="text-xs font-bold uppercase tracking-[0.15em] text-[#004795]"
          >
            Our Healthcare Services
          </span>
        </div>

        <!-- Heading -->
        <h2
          class="text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl lg:text-5xl"
        >
          Everything You Need for
          <span
            class="bg-gradient-to-r from-[#004795] to-cyan-500 bg-clip-text text-transparent"
          >
            Smarter Healthcare
          </span>
        </h2>

        <!-- Description -->
        <p
          class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg"
        >
          TENA-ADAM brings essential healthcare services together in one
          connected platform, helping patients, doctors, and healthcare
          organizations work better together.
        </p>
      </div>

      <!-- ================================================= -->
      <!-- MAIN SERVICE CARDS -->
      <!-- ================================================= -->

      <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <article
          v-for="service in services"
          :key="service.title"
          class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-2 hover:border-transparent hover:shadow-xl hover:shadow-slate-900/10"
        >
          <!-- Hover Gradient -->
          <div
            class="pointer-events-none absolute inset-0 bg-gradient-to-br from-blue-50/0 via-white to-cyan-50/0 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
          ></div>

          <!-- Top Accent -->
          <div
            class="absolute left-0 top-0 h-1 w-0 transition-all duration-500 group-hover:w-full"
            :class="colorClasses[service.color].line"
          ></div>

          <div class="relative">
            <!-- Icon -->
            <div
              class="flex h-14 w-14 items-center justify-center rounded-2xl transition-all duration-300"
              :class="[
                colorClasses[service.color].icon,
                colorClasses[service.color].hover,
              ]"
            >
              <component
                :is="service.icon"
                class="h-6 w-6 transition-transform duration-300 group-hover:scale-110"
                :stroke-width="2"
              />
            </div>

            <!-- Title -->
            <h3
              class="mt-6 text-lg font-bold text-slate-900 transition-colors duration-300 group-hover:text-[#004795]"
            >
              {{ service.title }}
            </h3>

            <!-- Description -->
            <p class="mt-3 text-sm leading-6 text-slate-500">
              {{ service.description }}
            </p>

            <!-- Features -->
            <ul class="mt-5 space-y-2.5">
              <li
                v-for="feature in service.features"
                :key="feature"
                class="flex items-center gap-2 text-xs font-medium text-slate-600"
              >
                <CheckCircle2 class="h-4 w-4 shrink-0 text-emerald-500" />

                <span>{{ feature }}</span>
              </li>
            </ul>

            <!-- Learn More -->
            <a
              href="#contact"
              class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-[#004795] transition-all duration-300"
            >
              Learn more

              <ArrowRight
                class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
              />
            </a>
          </div>
        </article>
      </div>

      <!-- ================================================= -->
      <!-- ADDITIONAL SERVICES -->
      <!-- ================================================= -->

      <div class="mt-8">
        <div
          class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
        >
          <div
            class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between"
          >
            <!-- Text -->
            <div>
              <p
                class="text-xs font-bold uppercase tracking-[0.15em] text-[#004795]"
              >
                More from TENA-ADAM
              </p>

              <h3 class="mt-2 text-xl font-bold text-slate-900">
                A complete healthcare ecosystem
              </h3>

              <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">
                Additional tools help healthcare organizations coordinate daily
                operations and make better decisions.
              </p>
            </div>

            <!-- Additional Services -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
              <div
                v-for="service in additionalServices"
                :key="service.title"
                class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 transition hover:border-blue-100 hover:bg-blue-50"
              >
                <div
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-[#004795] shadow-sm"
                >
                  <component :is="service.icon" class="h-4 w-4" />
                </div>

                <span class="text-xs font-bold text-slate-700">
                  {{ service.title }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ================================================= -->
      <!-- BOTTOM CTA -->
      <!-- ================================================= -->

      <div class="mt-12 text-center">
        <p class="text-sm text-slate-500">
          One platform. Multiple healthcare solutions.
        </p>

        <router-link
          to="/register"
          class="group mt-4 inline-flex items-center gap-2 text-sm font-bold text-[#004795]"
        >
          Explore the complete platform

          <ArrowRight
            class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
          />
        </router-link>
      </div>
    </div>
  </section>
</template>
