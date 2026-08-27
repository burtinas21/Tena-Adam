<script setup>
import { ref } from "vue";
import {
  User,
  Mail,
  Phone,
  Lock,
  ShieldCheck,
  Camera,
  Save,
  Eye,
  EyeOff,
  CheckCircle2,
  AlertCircle,
} from "lucide-vue-next";

const activeTab = ref("profile");

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const saved = ref(false);
const passwordChanged = ref(false);

const profile = ref({
  name: "Hospital Administrator",
  email: "admin@hospital.com",
  phone: "+251 911 000 000",
});

const password = ref({
  current: "",
  new: "",
  confirm: "",
});

const saveProfile = () => {
  saved.value = true;

  setTimeout(() => {
    saved.value = false;
  }, 3000);
};

const changePassword = () => {
  if (
    !password.value.current ||
    !password.value.new ||
    !password.value.confirm
  ) {
    return;
  }

  if (password.value.new !== password.value.confirm) {
    return;
  }

  password.value = {
    current: "",
    new: "",
    confirm: "",
  };

  passwordChanged.value = true;

  setTimeout(() => {
    passwordChanged.value = false;
  }, 3000);
};
</script>

<template>
  <div class="min-h-screen bg-slate-50/80 px-4 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
      <!-- ================================================= -->
      <!-- PAGE HEADER -->
      <!-- ================================================= -->

      <div class="mb-8">
        <div
          class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between"
        >
          <div>
            <p
              class="text-xs font-bold uppercase tracking-[0.16em] text-[#004795]"
            >
              Account Settings
            </p>

            <h1
              class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl"
            >
              My Profile
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
              Manage your personal information, contact details, and account
              security.
            </p>
          </div>

          <div
            class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5"
          >
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>

            <span class="text-xs font-semibold text-emerald-700">
              Account Active
            </span>
          </div>
        </div>
      </div>

      <!-- ================================================= -->
      <!-- PROFILE HEADER CARD -->
      <!-- ================================================= -->

      <div
        class="relative mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
      >
        <!-- Top gradient -->
        <div
          class="h-28 bg-gradient-to-r from-[#003b7d] via-[#004795] to-[#0077b6]"
        ></div>

        <div class="px-6 pb-6 sm:px-8">
          <div
            class="-mt-12 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between"
          >
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
              <!-- Profile Image -->
              <div class="relative">
                <div
                  class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-2xl border-4 border-white bg-blue-100 shadow-lg"
                >
                  <User class="h-11 w-11 text-[#004795]" />
                </div>

                <button
                  type="button"
                  class="absolute -bottom-2 -right-2 flex h-9 w-9 items-center justify-center rounded-xl border-2 border-white bg-[#004795] text-white shadow-md transition hover:bg-[#003b7d]"
                  aria-label="Change profile photo"
                >
                  <Camera class="h-4 w-4" />
                </button>
              </div>

              <!-- User Information -->
              <div class="pb-1">
                <h2 class="text-xl font-bold text-slate-900">
                  {{ profile.name }}
                </h2>

                <div class="mt-1 flex flex-wrap items-center gap-2">
                  <span
                    class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-[#004795]"
                  >
                    Hospital Administrator
                  </span>

                  <span class="text-xs text-slate-400"> • </span>

                  <span class="text-sm text-slate-500"> Smart Care </span>
                </div>
              </div>
            </div>

            <!-- Security Status -->
            <div
              class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"
            >
              <div
                class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100"
              >
                <ShieldCheck class="h-5 w-5 text-emerald-600" />
              </div>

              <div>
                <p class="text-xs font-semibold text-slate-500">
                  Security Status
                </p>

                <p class="text-sm font-bold text-slate-800">Protected</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ================================================= -->
      <!-- MAIN CONTENT -->
      <!-- ================================================= -->

      <div class="grid gap-6 lg:grid-cols-[240px_1fr]">
        <!-- ================================================= -->
        <!-- SIDEBAR -->
        <!-- ================================================= -->

        <aside
          class="h-fit rounded-2xl border border-slate-200 bg-white p-2 shadow-sm"
        >
          <button
            type="button"
            @click="activeTab = 'profile'"
            :class="
              activeTab === 'profile'
                ? 'bg-blue-50 text-[#004795]'
                : 'text-slate-600 hover:bg-slate-50'
            "
            class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-sm font-semibold transition"
          >
            <User class="h-4.5 w-4.5" />

            <span>Personal Information</span>
          </button>

          <button
            type="button"
            @click="activeTab = 'security'"
            :class="
              activeTab === 'security'
                ? 'bg-blue-50 text-[#004795]'
                : 'text-slate-600 hover:bg-slate-50'
            "
            class="mt-1 flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-sm font-semibold transition"
          >
            <Lock class="h-4.5 w-4.5" />

            <span>Password & Security</span>
          </button>
        </aside>

        <!-- ================================================= -->
        <!-- CONTENT -->
        <!-- ================================================= -->

        <div>
          <!-- ================================================= -->
          <!-- PERSONAL INFORMATION -->
          <!-- ================================================= -->

          <div
            v-if="activeTab === 'profile'"
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
          >
            <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
              <h3 class="text-lg font-bold text-slate-900">
                Personal Information
              </h3>

              <p class="mt-1 text-sm text-slate-500">
                Update your name and contact information.
              </p>
            </div>

            <form
              class="space-y-6 px-6 py-6 sm:px-8 sm:py-8"
              @submit.prevent="saveProfile"
            >
              <!-- Full Name -->
              <div>
                <label
                  for="name"
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Full Name
                </label>

                <div class="relative">
                  <User
                    class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                  />

                  <input
                    id="name"
                    v-model="profile.name"
                    type="text"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                  />
                </div>
              </div>

              <!-- Email + Phone -->
              <div class="grid gap-6 md:grid-cols-2">
                <!-- Email -->
                <div>
                  <label
                    for="email"
                    class="mb-2 block text-sm font-semibold text-slate-700"
                  >
                    Email Address
                  </label>

                  <div class="relative">
                    <Mail
                      class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                    />

                    <input
                      id="email"
                      v-model="profile.email"
                      type="email"
                      class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900 outline-none transition focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                    />
                  </div>

                  <p class="mt-2 text-xs text-slate-400">
                    You may need to verify a new email address.
                  </p>
                </div>

                <!-- Phone -->
                <div>
                  <label
                    for="phone"
                    class="mb-2 block text-sm font-semibold text-slate-700"
                  >
                    Phone Number
                  </label>

                  <div class="relative">
                    <Phone
                      class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                    />

                    <input
                      id="phone"
                      v-model="profile.phone"
                      type="tel"
                      class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900 outline-none transition focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                    />
                  </div>

                  <p class="mt-2 text-xs text-slate-400">
                    Use an active phone number.
                  </p>
                </div>
              </div>

              <!-- Success Message -->
              <div
                v-if="saved"
                class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3"
              >
                <CheckCircle2 class="h-5 w-5 text-emerald-600" />

                <p class="text-sm font-medium text-emerald-700">
                  Your profile has been updated successfully.
                </p>
              </div>

              <!-- Save Button -->
              <div class="flex justify-end border-t border-slate-100 pt-6">
                <button
                  type="submit"
                  class="inline-flex items-center gap-2 rounded-xl bg-[#004795] px-6 py-3 text-sm font-bold text-white shadow-md shadow-blue-900/10 transition hover:-translate-y-0.5 hover:bg-[#003b7d] focus:outline-none focus:ring-4 focus:ring-blue-100"
                >
                  <Save class="h-4 w-4" />
                  Save Changes
                </button>
              </div>
            </form>
          </div>

          <!-- ================================================= -->
          <!-- PASSWORD & SECURITY -->
          <!-- ================================================= -->

          <div
            v-else
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
          >
            <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
              <div class="flex items-start gap-4">
                <div
                  class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50"
                >
                  <Lock class="h-5 w-5 text-[#004795]" />
                </div>

                <div>
                  <h3 class="text-lg font-bold text-slate-900">
                    Password & Security
                  </h3>

                  <p class="mt-1 text-sm text-slate-500">
                    Keep your account secure with a strong password.
                  </p>
                </div>
              </div>
            </div>

            <form
              class="space-y-6 px-6 py-6 sm:px-8 sm:py-8"
              @submit.prevent="changePassword"
            >
              <!-- Current Password -->
              <div>
                <label
                  for="current-password"
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Current Password
                </label>

                <div class="relative">
                  <Lock
                    class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                  />

                  <input
                    id="current-password"
                    v-model="password.current"
                    :type="showCurrentPassword ? 'text' : 'password'"
                    autocomplete="current-password"
                    placeholder="Enter your current password"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                  />

                  <button
                    type="button"
                    @click="showCurrentPassword = !showCurrentPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                  >
                    <EyeOff v-if="showCurrentPassword" class="h-4 w-4" />

                    <Eye v-else class="h-4 w-4" />
                  </button>
                </div>
              </div>

              <!-- New Password -->
              <div>
                <label
                  for="new-password"
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  New Password
                </label>

                <div class="relative">
                  <Lock
                    class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                  />

                  <input
                    id="new-password"
                    v-model="password.new"
                    :type="showNewPassword ? 'text' : 'password'"
                    autocomplete="new-password"
                    placeholder="Enter your new password"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                  />

                  <button
                    type="button"
                    @click="showNewPassword = !showNewPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                  >
                    <EyeOff v-if="showNewPassword" class="h-4 w-4" />

                    <Eye v-else class="h-4 w-4" />
                  </button>
                </div>
              </div>

              <!-- Confirm Password -->
              <div>
                <label
                  for="confirm-password"
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Confirm New Password
                </label>

                <div class="relative">
                  <Lock
                    class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                  />

                  <input
                    id="confirm-password"
                    v-model="password.confirm"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    autocomplete="new-password"
                    placeholder="Confirm your new password"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                  />

                  <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                  >
                    <EyeOff v-if="showConfirmPassword" class="h-4 w-4" />

                    <Eye v-else class="h-4 w-4" />
                  </button>
                </div>
              </div>

              <!-- Password Requirements -->
              <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-4">
                <div class="flex gap-3">
                  <ShieldCheck class="mt-0.5 h-5 w-5 shrink-0 text-[#004795]" />

                  <div>
                    <p class="text-sm font-bold text-slate-800">
                      Password security
                    </p>

                    <ul class="mt-2 space-y-1 text-xs leading-5 text-slate-500">
                      <li>• Use at least 8 characters.</li>
                      <li>• Include uppercase and lowercase letters.</li>
                      <li>• Include at least one number.</li>
                      <li>• Avoid using easily guessed information.</li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Success -->
              <div
                v-if="passwordChanged"
                class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3"
              >
                <CheckCircle2 class="h-5 w-5 text-emerald-600" />

                <p class="text-sm font-medium text-emerald-700">
                  Your password has been changed successfully.
                </p>
              </div>

              <!-- Save -->
              <div class="flex justify-end border-t border-slate-100 pt-6">
                <button
                  type="submit"
                  class="inline-flex items-center gap-2 rounded-xl bg-[#004795] px-6 py-3 text-sm font-bold text-white shadow-md shadow-blue-900/10 transition hover:-translate-y-0.5 hover:bg-[#003b7d] focus:outline-none focus:ring-4 focus:ring-blue-100"
                >
                  <Lock class="h-4 w-4" />
                  Change Password
                </button>
              </div>
            </form>
          </div>

          <!-- ================================================= -->
          <!-- SECURITY INFORMATION -->
          <!-- ================================================= -->

          <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <div class="flex gap-3">
              <AlertCircle class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />

              <div>
                <p class="text-sm font-bold text-amber-800">
                  Keep your account secure
                </p>

                <p class="mt-1 text-xs leading-5 text-amber-700">
                  Never share your password with anyone. Smart Care support will
                  never ask you to provide your password.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
