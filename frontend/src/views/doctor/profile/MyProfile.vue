```vue
<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-4xl mx-auto">

      <!-- =========================================================
           PAGE HEADER
      ========================================================== -->
      <div class="flex items-center justify-between mb-7">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <div
              class="w-8 h-8 rounded-lg bg-[#004795]/10 flex items-center justify-center"
            >
              <UserCircle class="w-4 h-4 text-[#004795]" />
            </div>

            <span
              class="text-[10px] font-bold uppercase tracking-widest text-[#004795]"
            >
              Account
            </span>
          </div>

          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            My Profile
          </h1>

          <p class="text-xs text-gray-500 font-medium mt-1">
            Manage your professional information and account settings
          </p>
        </div>

        <button
          v-if="!editing"
          @click="startEdit"
          class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-2 transition shadow-sm"
        >
          <Pencil class="w-3.5 h-3.5" />
          Edit Profile
        </button>
      </div>

      <!-- =========================================================
           LOADING SKELETON
      ========================================================== -->
      <div v-if="loading" class="space-y-4">
        <div
          v-for="n in 4"
          :key="n"
          class="h-16 bg-white rounded-xl border border-gray-100 animate-pulse"
        ></div>
      </div>

      <!-- =========================================================
           ERROR
      ========================================================== -->
      <div
        v-else-if="error && !doctor"
        class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" />
        {{ error }}
      </div>

      <!-- =========================================================
           PROFILE CONTENT
      ========================================================== -->
      <div v-else-if="doctor" class="space-y-6">

        <!-- =======================================================
             DOCTOR PROFILE CARD
        ======================================================== -->
        <div
          class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
        >

          <!-- Cover + Avatar -->
          <div
            class="h-28 bg-gradient-to-r from-[#003670] via-[#004795] to-indigo-600 relative"
          >
            <!-- Decorative circles -->
            <div
              class="absolute right-8 top-4 w-20 h-20 rounded-full bg-white/5"
            ></div>

            <div
              class="absolute right-24 -top-8 w-28 h-28 rounded-full bg-white/5"
            ></div>

            <!-- Avatar -->
            <div class="absolute -bottom-10 left-7">
              <div
                class="w-20 h-20 rounded-full border-4 border-white bg-gray-100 overflow-hidden flex items-center justify-center shadow-md"
              >
                <img
                  v-if="picPreview || doctor.profile_picture_url"
                  :src="picPreview || doctor.profile_picture_url"
                  class="w-full h-full object-cover"
                  :alt="fullName"
                />

                <span
                  v-else
                  class="text-2xl font-bold text-[#004795]"
                >
                  {{ initials }}
                </span>
              </div>
            </div>
          </div>

          <div class="pt-14 px-7 pb-7">

            <!-- Name & Department -->
            <div
              class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-7"
            >
              <div>
                <div class="flex items-center gap-2">
                  <h2 class="text-xl font-bold text-gray-900">
                    {{ fullName }}
                  </h2>

                  <span
                    class="inline-flex items-center px-2 py-1 rounded-md bg-blue-50 text-[#004795] text-[9px] font-bold uppercase tracking-wide"
                  >
                    Doctor
                  </span>
                </div>

                <p
                  class="text-sm text-[#004795] font-semibold mt-1"
                >
                  {{ doctor.department?.name ?? "—" }}
                </p>

                <p class="text-xs text-gray-400 mt-1">
                  {{ doctor.hospital?.name ?? "—" }}
                </p>
              </div>

              <!-- Account Status -->
              <div
                class="flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-50 border border-emerald-100 w-fit"
              >
                <span
                  class="w-2 h-2 rounded-full bg-emerald-500"
                ></span>

                <span
                  class="text-[10px] font-bold text-emerald-700"
                >
                  Active Account
                </span>
              </div>
            </div>

            <!-- =================================================
                 VIEW MODE
            ================================================== -->
            <div
              v-if="!editing"
              class="space-y-6"
            >

              <!-- Professional Information -->
              <div>
                <div class="flex items-center gap-2 mb-4">
                  <div
                    class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center"
                  >
                    <Stethoscope
                      class="w-3.5 h-3.5 text-[#004795]"
                    />
                  </div>

                  <h3
                    class="text-sm font-bold text-gray-800"
                  >
                    Professional Information
                  </h3>
                </div>

                <div
                  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3"
                >
                  <div
                    class="p-4 rounded-xl bg-gray-50 border border-gray-100"
                  >
                    <InfoItem
                      label="Email"
                      :value="doctor.user?.email"
                    />
                  </div>

                  <div
                    class="p-4 rounded-xl bg-gray-50 border border-gray-100"
                  >
                    <InfoItem
                      label="Phone"
                      :value="doctor.user?.phone || '—'"
                    />
                  </div>

                  <div
                    class="p-4 rounded-xl bg-gray-50 border border-gray-100"
                  >
                    <InfoItem
                      label="License"
                      :value="doctor.license_number"
                    />
                  </div>

                  <div
                    class="p-4 rounded-xl bg-gray-50 border border-gray-100"
                  >
                    <InfoItem
                      label="Experience"
                      :value="
                        (doctor.years_experience ?? 0) + ' years'
                      "
                    />
                  </div>

                  <div
                    class="p-4 rounded-xl bg-gray-50 border border-gray-100"
                  >
                    <InfoItem
                      label="Fee"
                      :value="
                        'ETB ' +
                        Number(
                          doctor.consultation_fee ?? 0
                        ).toLocaleString()
                      "
                    />
                  </div>

                  <div
                    class="p-4 rounded-xl bg-gray-50 border border-gray-100"
                  >
                    <InfoItem
                      label="Telehealth"
                      :value="
                        doctor.is_telehealth_available
                          ? 'Available'
                          : 'Not available'
                      "
                    />
                  </div>
                </div>
              </div>

              <!-- Bio -->
              <div
                v-if="doctor.bio"
                class="border-t border-gray-100 pt-5"
              >
                <p
                  class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2"
                >
                  Professional Bio
                </p>

                <p
                  class="text-sm text-gray-700 leading-relaxed"
                >
                  {{ doctor.bio }}
                </p>
              </div>
            </div>

            <!-- =================================================
                 EDIT MODE
            ================================================== -->
            <form
              v-else
              @submit.prevent="handleSave"
              class="space-y-5"
            >

              <!-- Save Error -->
              <div
                v-if="saveError"
                class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"
              >
                <AlertCircle
                  class="w-3.5 h-3.5 flex-shrink-0 mt-0.5"
                />

                {{ saveError }}
              </div>

              <!-- Read-only fields -->
              <div>
                <div class="flex items-center gap-2 mb-3">
                  <div
                    class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center"
                  >
                    <ShieldCheck
                      class="w-3.5 h-3.5 text-gray-500"
                    />
                  </div>

                  <h3
                    class="text-sm font-bold text-gray-800"
                  >
                    Professional Details
                  </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                  <div
                    class="p-3.5 rounded-xl bg-gray-50 border border-gray-100"
                  >
                    <InfoItem
                      label="Email"
                      :value="doctor.user?.email"
                    />
                  </div>

                  <div
                    class="p-3.5 rounded-xl bg-gray-50 border border-gray-100"
                  >
                    <InfoItem
                      label="Phone"
                      :value="doctor.user?.phone || '—'"
                    />
                  </div>

                  <div
                    class="p-3.5 rounded-xl bg-gray-50 border border-gray-100"
                  >
                    <InfoItem
                      label="License"
                      :value="doctor.license_number"
                    />
                  </div>
                </div>
              </div>

              <!-- =================================================
                   EXISTING INPUT - UNCHANGED
              ================================================== -->

              <div>
                <label
                  class="block text-xs font-semibold text-gray-700 mb-1.5"
                >
                  Consultation Fee (ETB)
                </label>

                <input
                  v-model.number="form.consultation_fee"
                  type="number"
                  min="0"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
                />
              </div>

              <!-- Existing Bio Input - UNCHANGED -->

              <div>
                <label
                  class="block text-xs font-semibold text-gray-700 mb-1.5"
                >
                  Bio
                </label>

                <textarea
                  v-model="form.bio"
                  rows="4"
                  placeholder="Write a short professional bio..."
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none"
                ></textarea>
              </div>

              <!-- Existing Telemedicine Toggle - UNCHANGED -->

              <div class="flex items-center gap-3">
                <button
                  type="button"
                  @click="
                    form.is_telehealth_available =
                      !form.is_telehealth_available
                  "
                  :class="[
                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-300 focus:outline-none',
                    form.is_telehealth_available
                      ? 'bg-[#004795]'
                      : 'bg-gray-300',
                  ]"
                >
                  <span
                    :class="[
                      'absolute left-0.5 h-5 w-5 rounded-full bg-white shadow-md transform transition-transform duration-300',
                      form.is_telehealth_available
                        ? 'translate-x-5'
                        : 'translate-x-0',
                    ]"
                  ></span>
                </button>

                <span
                  class="text-sm text-gray-700 font-medium"
                >
                  Telemedicine Available
                </span>
              </div>

              <!-- Existing Profile Picture Input - UNCHANGED -->

              <div>
                <label
                  class="block text-xs font-semibold text-gray-700 mb-1.5"
                >
                  Profile Picture
                </label>

                <div class="flex items-center gap-3">
                  <div
                    v-if="picPreview"
                    class="w-14 h-14 rounded-full overflow-hidden border-2 border-[#004795]/30 flex-shrink-0"
                  >
                    <img
                      :src="picPreview"
                      class="w-full h-full object-cover"
                      alt="Preview"
                    />
                  </div>

                  <input
                    type="file"
                    accept="image/*"
                    @change="onPicChange"
                    class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#004795]/10 file:text-[#004795] hover:file:bg-[#004795]/20 transition"
                  />
                </div>
              </div>

              <!-- Existing Buttons -->

              <div
                class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100"
              >
                <button
                  type="button"
                  @click="cancelEdit"
                  class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                >
                  Cancel
                </button>

                <button
                  type="submit"
                  :disabled="saving"
                  class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2"
                >
                  <Loader2
                    v-if="saving"
                    class="w-3.5 h-3.5 animate-spin"
                  />

                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- =======================================================
             ACCOUNT & SECURITY
             This is separate from the existing doctor edit logic.
        ======================================================== -->
        <section
          class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
        >

          <!-- Section Header -->
          <div
            class="px-7 py-5 border-b border-gray-100 flex items-center justify-between"
          >
            <div class="flex items-center gap-3">

              <div
                class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center"
              >
                <ShieldCheck
                  class="w-5 h-5 text-[#004795]"
                />
              </div>

              <div>
                <h2
                  class="text-base font-bold text-gray-900"
                >
                  Account & Security
                </h2>

                <p
                  class="text-xs text-gray-500 mt-0.5"
                >
                  Manage your login and contact information
                </p>
              </div>
            </div>

            <span
              class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold"
            >
              <ShieldCheck class="w-3 h-3" />
              Secure
            </span>
          </div>

          <div class="p-7 space-y-6">

            <!-- =================================================
                 EMAIL
            ================================================== -->
            <div
              class="rounded-xl border border-gray-100 bg-gray-50/70 p-5"
            >
              <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
              >

                <div class="flex items-start gap-3">

                  <div
                    class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center shadow-sm"
                  >
                    <Mail
                      class="w-4.5 h-4.5 text-[#004795]"
                    />
                  </div>

                  <div>
                    <p
                      class="text-sm font-bold text-gray-800"
                    >
                      Email Address
                    </p>

                    <p
                      class="text-xs text-gray-500 mt-1"
                    >
                      Your account login and notification email
                    </p>
                  </div>
                </div>

                <div
                  class="sm:text-right"
                >
                  <p
                    class="text-sm font-semibold text-gray-800 break-all"
                  >
                    {{ doctor.user?.email || "—" }}
                  </p>

                  <span
                    class="inline-flex items-center gap-1 mt-1 text-[10px] font-semibold text-emerald-600"
                  >
                    <CheckCircle2 class="w-3 h-3" />
                    Current email
                  </span>
                </div>
              </div>
            </div>

            <!-- =================================================
                 PHONE
            ================================================== -->
            <div
              class="rounded-xl border border-gray-100 bg-gray-50/70 p-5"
            >
              <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
              >

                <div class="flex items-start gap-3">

                  <div
                    class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center shadow-sm"
                  >
                    <Phone
                      class="w-4.5 h-4.5 text-[#004795]"
                    />
                  </div>

                  <div>
                    <p
                      class="text-sm font-bold text-gray-800"
                    >
                      Phone Number
                    </p>

                    <p
                      class="text-xs text-gray-500 mt-1"
                    >
                      Used for important account communication
                    </p>
                  </div>
                </div>

                <div
                  class="sm:text-right"
                >
                  <p
                    class="text-sm font-semibold text-gray-800"
                  >
                    {{ doctor.user?.phone || "—" }}
                  </p>

                  <span
                    class="text-[10px] font-medium text-gray-400"
                  >
                    Current phone
                  </span>
                </div>
              </div>
            </div>

            <!-- =================================================
                 PASSWORD
            ================================================== -->
            <div
              class="rounded-xl border border-gray-100 bg-gray-50/70 p-5"
            >

              <div
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5"
              >

                <div class="flex items-start gap-3">

                  <div
                    class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center shadow-sm"
                  >
                    <Lock
                      class="w-4.5 h-4.5 text-[#004795]"
                    />
                  </div>

                  <div>
                    <p
                      class="text-sm font-bold text-gray-800"
                    >
                      Password
                    </p>

                    <p
                      class="text-xs text-gray-500 mt-1 leading-5"
                    >
                      Keep your Smart Care account protected with a
                      strong password.
                    </p>
                  </div>
                </div>

                <button
                  type="button"
                  @click="showPasswordForm = !showPasswordForm"
                  class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-white border border-gray-200 text-xs font-bold text-[#004795] hover:bg-blue-50 hover:border-blue-200 transition"
                >
                  <Lock class="w-3.5 h-3.5" />

                  {{
                    showPasswordForm
                      ? "Cancel"
                      : "Change Password"
                  }}
                </button>
              </div>

              <!-- Password Form -->
              <div
                v-if="showPasswordForm"
                class="mt-5 pt-5 border-t border-gray-200"
              >

                <div
                  class="grid grid-cols-1 md:grid-cols-2 gap-4"
                >

                  <!-- Current Password -->
                  <div>
                    <label
                      class="block text-xs font-semibold text-gray-700 mb-1.5"
                    >
                      Current Password
                    </label>

                    <div class="relative">

                      <Lock
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                      />

                      <input
                        v-model="accountForm.current_password"
                        :type="
                          showCurrentPassword
                            ? 'text'
                            : 'password'
                        "
                        autocomplete="current-password"
                        placeholder="Enter current password"
                        class="w-full border border-gray-200 rounded-lg pl-10 pr-10 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/20 focus:border-[#004795] transition"
                      />

                      <button
                        type="button"
                        @click="
                          showCurrentPassword =
                            !showCurrentPassword
                        "
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#004795]"
                      >
                        <Eye
                          v-if="!showCurrentPassword"
                          class="w-4 h-4"
                        />

                        <EyeOff
                          v-else
                          class="w-4 h-4"
                        />
                      </button>

                    </div>
                  </div>

                  <!-- New Password -->
                  <div>
                    <label
                      class="block text-xs font-semibold text-gray-700 mb-1.5"
                    >
                      New Password
                    </label>

                    <div class="relative">

                      <Lock
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                      />

                      <input
                        v-model="accountForm.password"
                        :type="
                          showNewPassword
                            ? 'text'
                            : 'password'
                        "
                        autocomplete="new-password"
                        placeholder="Enter new password"
                        class="w-full border border-gray-200 rounded-lg pl-10 pr-10 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/20 focus:border-[#004795] transition"
                      />

                      <button
                        type="button"
                        @click="
                          showNewPassword =
                            !showNewPassword
                        "
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#004795]"
                      >
                        <Eye
                          v-if="!showNewPassword"
                          class="w-4 h-4"
                        />

                        <EyeOff
                          v-else
                          class="w-4 h-4"
                        />
                      </button>

                    </div>
                  </div>

                  <!-- Confirm Password -->
                  <div class="md:col-span-2">
                    <label
                      class="block text-xs font-semibold text-gray-700 mb-1.5"
                    >
                      Confirm New Password
                    </label>

                    <div class="relative">

                      <Lock
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                      />

                      <input
                        v-model="accountForm.password_confirmation"
                        :type="
                          showConfirmPassword
                            ? 'text'
                            : 'password'
                        "
                        autocomplete="new-password"
                        placeholder="Confirm new password"
                        class="w-full border border-gray-200 rounded-lg pl-10 pr-10 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/20 focus:border-[#004795] transition"
                      />

                      <button
                        type="button"
                        @click="
                          showConfirmPassword =
                            !showConfirmPassword
                        "
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#004795]"
                      >
                        <Eye
                          v-if="!showConfirmPassword"
                          class="w-4 h-4"
                        />

                        <EyeOff
                          v-else
                          class="w-4 h-4"
                        />
                      </button>

                    </div>
                  </div>
                </div>

                <!-- Password Information -->
                <div
                  class="mt-4 rounded-lg border border-blue-100 bg-blue-50/70 px-4 py-3"
                >
                  <div class="flex gap-2.5">

                    <ShieldCheck
                      class="w-4 h-4 text-[#004795] flex-shrink-0 mt-0.5"
                    />

                    <div>
                      <p
                        class="text-xs font-bold text-[#003670]"
                      >
                        Password requirements
                      </p>

                      <p
                        class="text-[11px] text-gray-600 mt-1 leading-5"
                      >
                        Use at least 8 characters with a mixture of
                        uppercase letters, lowercase letters, numbers,
                        and special characters.
                      </p>
                    </div>

                  </div>
                </div>

                <!-- Password Save -->
                <div
                  class="flex justify-end mt-5"
                >
                  <button
                    type="button"
                    @click="handlePasswordChange"
                    :disabled="accountSaving"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#004795] hover:bg-[#003670] text-white text-xs font-bold transition disabled:opacity-60"
                  >

                    <Loader2
                      v-if="accountSaving"
                      class="w-3.5 h-3.5 animate-spin"
                    />

                    <Lock
                      v-else
                      class="w-3.5 h-3.5"
                    />

                    Update Password
                  </button>
                </div>

              </div>
            </div>

            <!-- =================================================
                 ACCOUNT PERMISSION NOTICE
            ================================================== -->
            <div
              class="flex items-start gap-3 rounded-xl border border-amber-100 bg-amber-50/70 p-4"
            >
              <AlertCircle
                class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"
              />

              <div>
                <p
                  class="text-xs font-bold text-amber-800"
                >
                  Account security notice
                </p>

                <p
                  class="text-[11px] text-amber-700 mt-1 leading-5"
                >
                  Your hospital, department, role, and permissions
                  are managed by the appropriate hospital administrator.
                  Changing your contact information or password does
                  not change your system permissions.
                </p>
              </div>
            </div>

          </div>
        </section>

      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

import {
  Pencil,
  AlertCircle,
  Loader2,
  UserCircle,
  Mail,
  Phone,
  Lock,
  Eye,
  EyeOff,
  ShieldCheck,
  CheckCircle2,
  Stethoscope,
} from "lucide-vue-next";

import doctorApi from "../../../api/doctorApi";

/* ============================================================
   INFO ITEM
============================================================ */

const InfoItem = {
  props: ["label", "value"],

  template: `
    <div>
      <p
        class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-0.5"
      >
        {{ label }}
      </p>

      <p class="text-sm font-semibold text-gray-800">
        {{ value || '—' }}
      </p>
    </div>
  `,
};

/* ============================================================
   EXISTING DOCTOR STATE
============================================================ */

const doctor = ref(null);

const loading = ref(false);

const error = ref(null);

const editing = ref(false);

const saving = ref(false);

const saveError = ref(null);

const picFile = ref(null);

const picPreview = ref(null);

/* ============================================================
   EXISTING DOCTOR FORM
   DO NOT CHANGE
============================================================ */

const form = ref({
  consultation_fee: 0,
  bio: "",
  is_telehealth_available: false,
});

/* ============================================================
   ACCOUNT SECURITY STATE
   NEW - DOES NOT MODIFY EXISTING FORM
============================================================ */

const showPasswordForm = ref(false);

const accountSaving = ref(false);

const accountError = ref(null);

const accountSuccess = ref(false);

const showCurrentPassword = ref(false);

const showNewPassword = ref(false);

const showConfirmPassword = ref(false);

const accountForm = ref({
  current_password: "",
  password: "",
  password_confirmation: "",
});

/* ============================================================
   FULL NAME
============================================================ */

const fullName = computed(() => {
  const u = doctor.value?.user;

  return u
    ? `Dr. ${u.first_name} ${u.last_name}`
    : "—";
});

/* ============================================================
   INITIALS
============================================================ */

const initials = computed(() => {
  const u = doctor.value?.user;

  return u
    ? (
        (u.first_name?.[0] ?? "") +
        (u.last_name?.[0] ?? "")
      ).toUpperCase()
    : "?";
});

/* ============================================================
   LOAD PROFILE
   EXISTING LOGIC - UNCHANGED
============================================================ */

async function load() {
  try {
    loading.value = true;

    error.value = null;

    const res = await doctorApi.getMe();

    doctor.value = res.data?.data ?? res.data;
  } catch (err) {
    error.value =
      err.response?.data?.message ||
      "Failed to load profile.";
  } finally {
    loading.value = false;
  }
}

onMounted(load);

/* ============================================================
   START EDIT
   EXISTING LOGIC - UNCHANGED
============================================================ */

function startEdit() {
  form.value = {
    consultation_fee:
      doctor.value.consultation_fee ?? 0,

    bio:
      doctor.value.bio ?? "",

    is_telehealth_available:
      doctor.value.is_telehealth_available ?? false,
  };

  picFile.value = null;

  picPreview.value = null;

  saveError.value = null;

  editing.value = true;
}

/* ============================================================
   CANCEL EDIT
   EXISTING LOGIC - UNCHANGED
============================================================ */

function cancelEdit() {
  editing.value = false;

  saveError.value = null;

  picPreview.value = null;
}

/* ============================================================
   PROFILE PICTURE
   EXISTING LOGIC - UNCHANGED
============================================================ */

function onPicChange(e) {
  const file = e.target.files[0] ?? null;

  picFile.value = file;

  if (file) {
    picPreview.value =
      URL.createObjectURL(file);
  } else {
    picPreview.value = null;
  }
}

/* ============================================================
   SAVE DOCTOR PROFILE
   EXISTING LOGIC - UNCHANGED
============================================================ */

async function handleSave() {
  saveError.value = null;

  try {
    saving.value = true;

    const payload = {
      ...form.value,
    };

    if (picFile.value) {
      payload.profile_picture = picFile.value;
    }

    const res =
      await doctorApi.updateMe(payload);

    doctor.value =
      res.data?.data ?? res.data;

    editing.value = false;
  } catch (err) {
    const errors =
      err.response?.data?.errors;

    saveError = errors
      ? Object.values(errors)
          .flat()
          .join(" ")
      : err.response?.data?.message ||
        "Something went wrong.";
  } finally {
    saving.value = false;
  }
}

/* ============================================================
   PASSWORD UI
============================================================ */

function handlePasswordChange() {
  accountError.value = null;

  accountSuccess.value = false;

  if (
    !accountForm.value.current_password ||
    !accountForm.value.password ||
    !accountForm.value.password_confirmation
  ) {
    accountError.value =
      "Please complete all password fields.";

    return;
  }

  if (
    accountForm.value.password.length < 8
  ) {
    accountError.value =
      "The new password must contain at least 8 characters.";

    return;
  }

  if (
    accountForm.value.password !==
    accountForm.value.password_confirmation
  ) {
    accountError.value =
      "The new passwords do not match.";

    return;
  }
  accountSuccess.value = true;

  accountForm.value = {
    current_password: "",
    password: "",
    password_confirmation: "",
  };
}
</script>

