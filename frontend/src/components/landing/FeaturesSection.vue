<script setup>
import {
  ShieldCheck,
  Zap,
  UsersRound,
  Building2,
  Smartphone,
  BarChart3,
  Languages,
  Clock3,
  CheckCircle2,
  ArrowRight,
  Sparkles,
} from "lucide-vue-next";

const features = [
  {
    title: "Secure Healthcare",
    description:
      "Protect sensitive healthcare information with secure authentication, role-based access, and controlled data management.",
    icon: ShieldCheck,
    color: "blue",
  },
  {
    title: "Faster Healthcare Operations",
    description:
      "Automate appointments, queues, notifications, and daily workflows to reduce unnecessary administrative work.",
    icon: Zap,
    color: "amber",
  },
  {
    title: "Patient-Centered Experience",
    description:
      "Give patients convenient access to appointments, healthcare services, medical information, and follow-up care.",
    icon: UsersRound,
    color: "emerald",
  },
  {
    title: "Multi-Hospital Platform",
    description:
      "Support multiple healthcare organizations and departments from one scalable digital healthcare infrastructure.",
    icon: Building2,
    color: "violet",
  },
  {
    title: "Accessible Anywhere",
    description:
      "Connect patients and healthcare providers through a responsive platform designed for modern digital healthcare.",
    icon: Smartphone,
    color: "cyan",
  },
  {
    title: "Data-Driven Decisions",
    description:
      "Turn healthcare data into meaningful reports and analytics that help organizations monitor performance and improve care.",
    icon: BarChart3,
    color: "rose",
  },
];

const highlights = [
  "Role-based access control",
  "Centralized healthcare management",
  "Automated appointment and queue workflows",
  "Real-time notifications",
  "Healthcare reports and analytics",
  "Multi-language support",
];

const colorClasses = {
  blue: {
    icon: "bg-blue-50 text-[#004795]",
    hover: "group-hover:bg-[#004795] group-hover:text-white",
  },
  amber: {
    icon: "bg-amber-50 text-amber-600",
    hover: "group-hover:bg-amber-500 group-hover:text-white",
  },
  emerald: {
    icon: "bg-emerald-50 text-emerald-600",
    hover: "group-hover:bg-emerald-500 group-hover:text-white",
  },
  violet: {
    icon: "bg-violet-50 text-violet-600",
    hover: "group-hover:bg-violet-500 group-hover:text-white",
  },
  cyan: {
    icon: "bg-cyan-50 text-cyan-600",
    hover: "group-hover:bg-cyan-500 group-hover:text-white",
  },
  rose: {
    icon: "bg-rose-50 text-rose-600",
    hover: "group-hover:bg-rose-500 group-hover:text-white",
  },
};
</script>

<template>
  <section
    id="features"
    class="relative overflow-hidden bg-white py-24 sm:py-28"
  >
    <!-- Background Decorations -->
    <div
      class="pointer-events-none absolute right-0 top-0 h-96 w-96 rounded-full bg-blue-50/70 blur-3xl"
    ></div>

    <div
      class="pointer-events-none absolute bottom-0 left-0 h-80 w-80 rounded-full bg-cyan-50/60 blur-3xl"
    ></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <!-- ================================================= -->
      <!-- HEADER -->
      <!-- ================================================= -->

      <div class="mx-auto max-w-3xl text-center">
        <div
          class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-4 py-2"
        >
          <Sparkles class="h-4 w-4 text-[#004795]" />

          <span
            class="text-xs font-bold uppercase tracking-[0.15em] text-[#004795]"
          >
            Why Choose TENA-ADAM
          </span>
        </div>

        <h2
          class="text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl lg:text-5xl"
        >
          Built for
          <span
            class="bg-gradient-to-r from-[#004795] to-cyan-500 bg-clip-text text-transparent"
          >
            Better Healthcare
          </span>
        </h2>

        <p
          class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg"
        >
          TENA-ADAM combines healthcare technology, automation, security, and
          accessibility to create a connected experience for patients and
          healthcare providers.
        </p>
      </div>

      <!-- ================================================= -->
      <!-- MAIN FEATURES -->
      <!-- ================================================= -->

      <div class="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <article
          v-for="feature in features"
          :key="feature.title"
          class="group relative rounded-2xl border border-slate-200 bg-white p-6 transition-all duration-300 hover:-translate-y-2 hover:border-blue-100 hover:shadow-xl hover:shadow-blue-950/10"
        >
          <!-- Hover Background -->
          <div
            class="pointer-events-none absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-50/0 to-cyan-50/0 opacity-0 transition duration-300 group-hover:from-blue-50/40 group-hover:to-cyan-50/20 group-hover:opacity-100"
          ></div>

          <div class="relative">
            <!-- Icon -->
            <div
              class="flex h-14 w-14 items-center justify-center rounded-2xl transition-all duration-300"
              :class="[
                colorClasses[feature.color].icon,
                colorClasses[feature.color].hover,
              ]"
            >
              <component
                :is="feature.icon"
                class="h-6 w-6 transition-transform duration-300 group-hover:scale-110"
                :stroke-width="2"
              />
            </div>

            <!-- Title -->
            <h3
              class="mt-6 text-lg font-bold text-slate-900 transition-colors group-hover:text-[#004795]"
            >
              {{ feature.title }}
            </h3>

            <!-- Description -->
            <p class="mt-3 text-sm leading-6 text-slate-500">
              {{ feature.description }}
            </p>

            <!-- Learn More -->
            <a
              href="#contact"
              class="group/link mt-5 inline-flex items-center gap-2 text-xs font-bold text-[#004795]"
            >
              Learn more

              <ArrowRight
                class="h-3.5 w-3.5 transition-transform group-hover/link:translate-x-1"
              />
            </a>
          </div>
        </article>
      </div>

      <!-- ================================================= -->
      <!-- FEATURE HIGHLIGHT -->
      <!-- ================================================= -->

      <div
        class="relative mt-16 overflow-hidden rounded-3xl bg-gradient-to-br from-[#003b7d] via-[#004795] to-[#0077b6] shadow-2xl shadow-blue-950/20"
      >
        <!-- Background Decoration -->
        <div
          class="pointer-events-none absolute -right-20 -top-32 h-96 w-96 rounded-full bg-cyan-400/20 blur-3xl"
        ></div>

        <div
          class="pointer-events-none absolute -bottom-32 -left-20 h-80 w-80 rounded-full bg-blue-300/10 blur-3xl"
        ></div>

        <div
          class="relative grid gap-10 p-8 sm:p-10 lg:grid-cols-2 lg:items-center lg:p-14"
        >
          <!-- Left -->
          <div>
            <div
              class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5"
            >
              <ShieldCheck class="h-4 w-4 text-cyan-300" />

              <span
                class="text-xs font-bold uppercase tracking-wider text-white"
              >
                Healthcare You Can Trust
              </span>
            </div>

            <h3
              class="mt-5 text-2xl font-extrabold leading-tight text-white sm:text-3xl"
            >
              One connected platform for the complete healthcare journey.
            </h3>

            <p
              class="mt-4 max-w-xl text-sm leading-7 text-blue-100 sm:text-base"
            >
              From appointment booking to consultation, medical records,
              prescriptions, telemedicine, and follow-up care, TENA-ADAM helps
              connect every important part of the healthcare experience.
            </p>

            <router-link
              to="/register"
              class="group mt-7 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-[#004795] shadow-lg transition-all duration-300 hover:-translate-y-1 hover:bg-blue-50"
            >
              Start Your Healthcare Journey

              <ArrowRight
                class="h-4 w-4 transition-transform group-hover:translate-x-1"
              />
            </router-link>
          </div>

          <!-- Right: Highlights -->
          <div
            class="rounded-2xl border border-white/10 bg-white/[0.08] p-6 backdrop-blur-sm"
          >
            <div class="grid gap-4 sm:grid-cols-2">
              <div
                v-for="highlight in highlights"
                :key="highlight"
                class="flex items-start gap-3"
              >
                <div
                  class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-400/20"
                >
                  <CheckCircle2 class="h-3.5 w-3.5 text-emerald-300" />
                </div>

                <span class="text-sm font-medium leading-5 text-blue-50">
                  {{ highlight }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ================================================= -->
      <!-- MINI TRUST FEATURES -->
      <!-- ================================================= -->

      <div class="mt-12 grid gap-4 sm:grid-cols-3">
        <div
          class="flex items-center gap-4 rounded-xl border border-slate-100 bg-slate-50 p-4"
        >
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-[#004795] shadow-sm"
          >
            <ShieldCheck class="h-5 w-5" />
          </div>

          <div>
            <p class="text-sm font-bold text-slate-900">Secure by Design</p>

            <p class="mt-0.5 text-xs text-slate-500">
              Healthcare data protection
            </p>
          </div>
        </div>

        <div
          class="flex items-center gap-4 rounded-xl border border-slate-100 bg-slate-50 p-4"
        >
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-[#004795] shadow-sm"
          >
            <Clock3 class="h-5 w-5" />
          </div>

          <div>
            <p class="text-sm font-bold text-slate-900">Save Time</p>

            <p class="mt-0.5 text-xs text-slate-500">Streamlined workflows</p>
          </div>
        </div>

        <div
          class="flex items-center gap-4 rounded-xl border border-slate-100 bg-slate-50 p-4"
        >
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-[#004795] shadow-sm"
          >
            <Languages class="h-5 w-5" />
          </div>

          <div>
            <p class="text-sm font-bold text-slate-900">Accessible</p>

            <p class="mt-0.5 text-xs text-slate-500">Multi-language ready</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
