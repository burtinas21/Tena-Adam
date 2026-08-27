<script setup>
import { ref } from "vue";
import {
  Mail,
  Phone,
  Lock,
  ShieldCheck,
  Save,
  Eye,
  EyeOff,
  CheckCircle2,
  AlertCircle,
  Settings,
} from "lucide-vue-next";

const activeTab = ref("contact");

const profile = ref({
  name: "Platform Administrator",
  email: "admin@smartcare.com",
  phone: "+251 911 000 000",
});

const password = ref({
  current: "",
  new: "",
  confirm: "",
});

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const saved = ref(false);
const passwordChanged = ref(false);
const passwordError = ref("");

const saveContactInformation = () => {
  saved.value = true;

  setTimeout(() => {
    saved.value = false;
  }, 3000);
};

const changePassword = () => {
  passwordError.value = "";

  if (
    !password.value.current ||
    !password.value.new ||
    !password.value.confirm
  ) {
    passwordError.value = "Please complete all password fields.";
    return;
  }

  if (password.value.new.length < 8) {
    passwordError.value =
      "The new password must contain at least 8 characters.";
    return;
  }

  if (password.value.new !== password.value.confirm) {
    passwordError.value = "The new passwords do not match.";
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
      <!-- ===================================================== -->
      <!-- PAGE HEADER -->
      <!-- ===================================================== -->

      <div class="mb-8">
        <div
          class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
        >
          <div>
            <div class="flex items-center gap-2">
              <div
                class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"
              >
                <Settings class="h-5 w-5 text-[#004795]" />
              </div>

              <span
                class="text-xs font-bold uppercase tracking-[0.16em] text-[#004795]"
              >
                Account Settings
              </span>
            </div>

            <h1
              class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl"
            >
              My Profile
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
              Manage your platform administrator contact information and account
              security.
            </p>
          </div>

          <!-- Active Status -->
          <div
            class="flex w-fit items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3.5 py-2"
          >
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>

            <span class="text-xs font-bold text-emerald-700">
              Account Active
            </span>
          </div>
        </div>
      </div>

      <!-- ===================================================== -->
      <!-- PROFILE SUMMARY -->
      <!-- ===================================================== -->

      <div
        class="relative mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
      >
        <!-- Gradient Header -->
        <div
          class="h-28 bg-gradient-to-r from-[#002f63] via-[#004795] to-[#0068a8]"
        ></div>

        <div class="px-6 pb-6 sm:px-8">
          <div
            class="-mt-11 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between"
          >
            <!-- Avatar + User -->
            <div class="flex items-end gap-4">
              <div
                class="flex h-24 w-24 items-center justify-center rounded-2xl border-4 border-white bg-blue-100 shadow-lg"
              >
                <ShieldCheck class="h-11 w-11 text-[#004795]" />
              </div>

              <div class="pb-1">
                <h2 class="text-xl font-bold text-slate-900">
                  {{ profile.name }}
                </h2>

                <div class="mt-1 flex flex-wrap items-center gap-2">
                  <span
                    class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#004795]"
                  >
                    Platform Administrator
                  </span>

                  <span class="text-xs text-slate-400"> • </span>

                  <span class="text-sm text-slate-500">
                    Smart Care Platform
                  </span>
                </div>
              </div>
            </div>

            <!-- Security -->
            <div
              class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"
            >
              <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100"
              >
                <ShieldCheck class="h-5 w-5 text-emerald-600" />
              </div>

              <div>
                <p class="text-xs font-semibold text-slate-500">Security</p>

                <p class="text-sm font-bold text-slate-800">Protected</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ===================================================== -->
      <!-- MAIN CONTENT -->
      <!-- ===================================================== -->

      <div class="grid gap-6 lg:grid-cols-[240px_1fr]">
        <!-- ================================================= -->
        <!-- SETTINGS NAVIGATION -->
        <!-- ================================================= -->

        <aside
          class="h-fit rounded-2xl border border-slate-200 bg-white p-2 shadow-sm"
        >
          <!-- Contact -->
          <button
            type="button"
            @click="activeTab = 'contact'"
            :class="
              activeTab === 'contact'
                ? 'bg-blue-50 text-[#004795]'
                : 'text-slate-600 hover:bg-slate-50'
            "
            class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-sm font-semibold transition"
          >
            <div
              class="flex h-8 w-8 items-center justify-center rounded-lg"
              :class="activeTab === 'contact' ? 'bg-white' : 'bg-slate-100'"
            >
              <Mail class="h-4 w-4" />
            </div>

            <span>Contact Information</span>
          </button>

          <!-- Security -->
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
            <div
              class="flex h-8 w-8 items-center justify-center rounded-lg"
              :class="activeTab === 'security' ? 'bg-white' : 'bg-slate-100'"
            >
              <Lock class="h-4 w-4" />
            </div>

            <span>Password & Security</span>
          </button>
        </aside>

        <!-- ================================================= -->
        <!-- CONTENT -->
        <!-- ================================================= -->

        <main>
          <!-- ================================================= -->
          <!-- CONTACT INFORMATION -->
          <!-- ================================================= -->

          <section
            v-if="activeTab === 'contact'"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
          >
            <!-- Section Header -->
            <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
              <h3 class="text-lg font-bold text-slate-900">
                Contact Information
              </h3>

              <p class="mt-1 text-sm text-slate-500">
                Update the email address and phone number associated with your
                platform account.
              </p>
            </div>

            <form
              class="space-y-7 px-6 py-6 sm:px-8 sm:py-8"
              @submit.prevent="saveContactInformation"
            >
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
                    autocomplete="email"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                  />
                </div>

                <p class="mt-2 text-xs leading-5 text-slate-400">
                  A verification link may be sent when changing your email
                  address.
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
                    autocomplete="tel"
                    placeholder="+251 9XX XXX XXX"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                  />
                </div>

                <p class="mt-2 text-xs leading-5 text-slate-400">
                  Use a phone number that you can access.
                </p>
              </div>

              <!-- Notice -->
              <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-4">
                <div class="flex gap-3">
                  <ShieldCheck class="mt-0.5 h-5 w-5 shrink-0 text-[#004795]" />

                  <div>
                    <p class="text-sm font-bold text-slate-800">
                      Account protection
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                      Your administrator role and platform permissions cannot be
                      changed from this profile page.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Success -->
              <div
                v-if="saved"
                class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3"
              >
                <CheckCircle2 class="h-5 w-5 text-emerald-600" />

                <p class="text-sm font-medium text-emerald-700">
                  Contact information updated successfully.
                </p>
              </div>

              <!-- Footer -->
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
          </section>

          <!-- ================================================= -->
          <!-- PASSWORD & SECURITY -->
          <!-- ================================================= -->

          <section
            v-else
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
          >
            <!-- Header -->
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
                    Change your password to keep your platform account secure.
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
                  for="currentPassword"
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Current Password
                </label>

                <div class="relative">
                  <Lock
                    class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                  />

                  <input
                    id="currentPassword"
                    v-model="password.current"
                    :type="showCurrentPassword ? 'text' : 'password'"
                    autocomplete="current-password"
                    placeholder="Enter current password"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                  />

                  <button
                    type="button"
                    @click="showCurrentPassword = !showCurrentPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                    aria-label="Toggle current password visibility"
                  >
                    <EyeOff v-if="showCurrentPassword" class="h-4 w-4" />

                    <Eye v-else class="h-4 w-4" />
                  </button>
                </div>
              </div>

              <!-- New Password -->
              <div>
                <label
                  for="newPassword"
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  New Password
                </label>

                <div class="relative">
                  <Lock
                    class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                  />

                  <input
                    id="newPassword"
                    v-model="password.new"
                    :type="showNewPassword ? 'text' : 'password'"
                    autocomplete="new-password"
                    placeholder="Enter new password"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                  />

                  <button
                    type="button"
                    @click="showNewPassword = !showNewPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                    aria-label="Toggle new password visibility"
                  >
                    <EyeOff v-if="showNewPassword" class="h-4 w-4" />

                    <Eye v-else class="h-4 w-4" />
                  </button>
                </div>
              </div>

              <!-- Confirm Password -->
              <div>
                <label
                  for="confirmPassword"
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Confirm New Password
                </label>

                <div class="relative">
                  <Lock
                    class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                  />

                  <input
                    id="confirmPassword"
                    v-model="password.confirm"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    autocomplete="new-password"
                    placeholder="Confirm new password"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#004795] focus:bg-white focus:ring-4 focus:ring-blue-100"
                  />

                  <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                    aria-label="Toggle confirm password visibility"
                  >
                    <EyeOff v-if="showConfirmPassword" class="h-4 w-4" />

                    <Eye v-else class="h-4 w-4" />
                  </button>
                </div>
              </div>

              <!-- Error -->
              <div
                v-if="passwordError"
                class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3"
              >
                <AlertCircle class="h-5 w-5 shrink-0 text-red-600" />

                <p class="text-sm font-medium text-red-700">
                  {{ passwordError }}
                </p>
              </div>

              <!-- Success -->
              <div
                v-if="passwordChanged"
                class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3"
              >
                <CheckCircle2 class="h-5 w-5 text-emerald-600" />

                <p class="text-sm font-medium text-emerald-700">
                  Password changed successfully.
                </p>
              </div>

              <!-- Password Requirements -->
              <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                <div class="flex gap-3">
                  <ShieldCheck class="mt-0.5 h-5 w-5 shrink-0 text-[#004795]" />

                  <div>
                    <h4 class="text-sm font-bold text-slate-800">
                      Password requirements
                    </h4>

                    <ul class="mt-2 space-y-1 text-xs leading-5 text-slate-500">
                      <li>• Minimum 8 characters</li>

                      <li>• At least one uppercase letter</li>

                      <li>• At least one lowercase letter</li>

                      <li>• At least one number</li>

                      <li>• Avoid easily guessed information</li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Footer -->
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
          </section>

          <!-- ================================================= -->
          <!-- SECURITY NOTICE -->
          <!-- ================================================= -->

          <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <div class="flex gap-3">
              <AlertCircle class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />

              <div>
                <p class="text-sm font-bold text-amber-800">
                  Protect your administrator account
                </p>

                <p class="mt-1 text-xs leading-5 text-amber-700">
                  Never share your password with another person. Smart Care
                  administrators and support staff will never ask you for your
                  password.
                </p>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>
