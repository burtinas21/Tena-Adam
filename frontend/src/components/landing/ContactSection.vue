<script setup>
import { ref } from "vue";
import {
  Mail,
  Phone,
  MapPin,
  Clock3,
  Send,
  CheckCircle2,
  MessageSquare,
  ArrowRight,
} from "lucide-vue-next";

const form = ref({
  name: "",
  email: "",
  phone: "",
  subject: "",
  message: "",
});

const isSubmitting = ref(false);
const submitted = ref(false);

const handleSubmit = async () => {
  isSubmitting.value = true;

  // Connect this to your Laravel API later.
  await new Promise((resolve) => setTimeout(resolve, 800));

  isSubmitting.value = false;
  submitted.value = true;

  form.value = {
    name: "",
    email: "",
    phone: "",
    subject: "",
    message: "",
  };

  setTimeout(() => {
    submitted.value = false;
  }, 5000);
};

const contactItems = [
  {
    title: "Email Us",
    value: "support@tena-adam.com",
    description: "Send us your questions anytime",
    icon: Mail,
  },
  {
    title: "Call Us",
    value: "+251 900 000 000",
    description: "Available during business hours",
    icon: Phone,
  },
  {
    title: "Our Location",
    value: "Addis Ababa, Ethiopia",
    description: "Serving healthcare organizations across Ethiopia",
    icon: MapPin,
  },
  {
    title: "Working Hours",
    value: "Mon – Fri, 8:00 AM – 5:00 PM",
    description: "We are here to support you",
    icon: Clock3,
  },
];
</script>

<template>
  <section
    id="contact"
    class="relative overflow-hidden bg-white py-24 sm:py-28"
  >
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
          <MessageSquare class="h-4 w-4 text-[#004795]" />

          <span
            class="text-xs font-bold uppercase tracking-[0.15em] text-[#004795]"
          >
            Get In Touch
          </span>
        </div>

        <h2
          class="text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl lg:text-5xl"
        >
          We’re Here to
          <span
            class="bg-gradient-to-r from-[#004795] to-cyan-500 bg-clip-text text-transparent"
          >
            Help
          </span>
        </h2>

        <p
          class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg"
        >
          Have a question about TENA-ADAM or need help with our platform? Send
          us a message and our team will be happy to assist you.
        </p>
      </div>

      <!-- ================================================= -->
      <!-- CONTACT CONTENT -->
      <!-- ================================================= -->

      <div
        class="mt-14 grid overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5 lg:grid-cols-[0.8fr_1.2fr]"
      >
        <!-- ================================================= -->
        <!-- LEFT: CONTACT INFORMATION -->
        <!-- ================================================= -->

        <div
          class="relative overflow-hidden bg-gradient-to-br from-[#003b7d] via-[#004795] to-[#0077b6] p-8 sm:p-10 lg:p-12"
        >
          <!-- Decorative Circles -->
          <div
            class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full border border-white/10"
          ></div>

          <div
            class="pointer-events-none absolute -bottom-32 -left-24 h-80 w-80 rounded-full border border-white/10"
          ></div>

          <div class="relative">
            <p
              class="text-xs font-bold uppercase tracking-[0.15em] text-cyan-300"
            >
              Contact Information
            </p>

            <h3 class="mt-3 text-2xl font-extrabold text-white sm:text-3xl">
              Let’s connect and make healthcare smarter.
            </h3>

            <p class="mt-4 text-sm leading-7 text-blue-100">
              Whether you are a patient, healthcare professional, or healthcare
              organization, we are ready to answer your questions and help you
              get started.
            </p>

            <!-- Contact Items -->
            <div class="mt-9 space-y-6">
              <div
                v-for="item in contactItems"
                :key="item.title"
                class="flex items-start gap-4"
              >
                <div
                  class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/10"
                >
                  <component :is="item.icon" class="h-5 w-5 text-cyan-300" />
                </div>

                <div class="min-w-0">
                  <p
                    class="text-xs font-bold uppercase tracking-wider text-blue-200"
                  >
                    {{ item.title }}
                  </p>

                  <p class="mt-1 break-words text-sm font-bold text-white">
                    {{ item.value }}
                  </p>

                  <p class="mt-1 text-xs leading-5 text-blue-200">
                    {{ item.description }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Support Box -->
            <div
              class="mt-10 rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur-sm"
            >
              <div class="flex items-start gap-3">
                <CheckCircle2
                  class="mt-0.5 h-5 w-5 shrink-0 text-emerald-300"
                />

                <div>
                  <p class="text-sm font-bold text-white">
                    Need technical support?
                  </p>

                  <p class="mt-1 text-xs leading-5 text-blue-100">
                    Our support team can help with account access, platform
                    questions, and technical issues.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ================================================= -->
        <!-- RIGHT: CONTACT FORM -->
        <!-- ================================================= -->

        <div class="p-8 sm:p-10 lg:p-12">
          <!-- Success Message -->
          <div
            v-if="submitted"
            class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4"
          >
            <CheckCircle2 class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" />

            <div>
              <p class="text-sm font-bold text-emerald-800">
                Message sent successfully!
              </p>

              <p class="mt-1 text-xs text-emerald-700">
                Thank you for contacting us. Our team will get back to you soon.
              </p>
            </div>
          </div>

          <!-- Form Header -->
          <div>
            <p
              class="text-xs font-bold uppercase tracking-[0.15em] text-[#004795]"
            >
              Send Us a Message
            </p>

            <h3 class="mt-2 text-2xl font-extrabold text-slate-900">
              How can we help?
            </h3>

            <p class="mt-2 text-sm text-slate-500">
              Fill out the form below and we’ll get back to you.
            </p>
          </div>

          <!-- Form -->
          <form class="mt-8 space-y-5" @submit.prevent="handleSubmit">
            <!-- Name + Email -->
            <div class="grid gap-5 sm:grid-cols-2">
              <!-- Name -->
              <div>
                <label
                  for="contact-name"
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Full Name
                  <span class="text-red-500">*</span>
                </label>

                <input
                  id="contact-name"
                  v-model="form.name"
                  type="text"
                  required
                  autocomplete="name"
                  placeholder="Enter your full name"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                />
              </div>

              <!-- Email -->
              <div>
                <label
                  for="contact-email"
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Email Address
                  <span class="text-red-500">*</span>
                </label>

                <input
                  id="contact-email"
                  v-model="form.email"
                  type="email"
                  required
                  autocomplete="email"
                  placeholder="you@example.com"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                />
              </div>
            </div>

            <!-- Phone + Subject -->
            <div class="grid gap-5 sm:grid-cols-2">
              <!-- Phone -->
              <div>
                <label
                  for="contact-phone"
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Phone Number
                </label>

                <input
                  id="contact-phone"
                  v-model="form.phone"
                  type="tel"
                  autocomplete="tel"
                  placeholder="+251 9XX XXX XXX"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                />
              </div>

              <!-- Subject -->
              <div>
                <label
                  for="contact-subject"
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Subject
                  <span class="text-red-500">*</span>
                </label>

                <input
                  id="contact-subject"
                  v-model="form.subject"
                  type="text"
                  required
                  placeholder="How can we help?"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                />
              </div>
            </div>

            <!-- Message -->
            <div>
              <label
                for="contact-message"
                class="mb-2 block text-sm font-semibold text-slate-700"
              >
                Message
                <span class="text-red-500">*</span>
              </label>

              <textarea
                id="contact-message"
                v-model="form.message"
                rows="5"
                required
                placeholder="Tell us how we can help you..."
                class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
              ></textarea>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              :disabled="isSubmitting"
              class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#004795] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-900/10 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#003b7d] disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
            >
              <span v-if="!isSubmitting"> Send Message </span>

              <span v-else> Sending... </span>

              <Send
                v-if="!isSubmitting"
                class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
              />

              <span
                v-else
                class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"
              ></span>
            </button>
          </form>

          <!-- Privacy Note -->
          <p class="mt-5 text-xs leading-5 text-slate-400">
            By submitting this form, you agree that our team may contact you
            regarding your inquiry.
          </p>
        </div>
      </div>

      <!-- ================================================= -->
      <!-- BOTTOM CTA -->
      <!-- ================================================= -->

      <div
        class="mt-10 flex flex-col items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-6 py-5 sm:flex-row"
      >
        <div>
          <p class="text-sm font-bold text-slate-900">
            Ready to experience Smart Care?
          </p>

          <p class="mt-1 text-xs text-slate-500">
            Create your account and start your digital healthcare journey.
          </p>
        </div>

        <router-link
          to="/register"
          class="group inline-flex shrink-0 items-center gap-2 text-sm font-bold text-[#004795]"
        >
          Get Started

          <ArrowRight
            class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
          />
        </router-link>
      </div>
    </div>
  </section>
</template>
