<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-3xl mx-auto">

      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">My Profile</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">View and update your professional information</p>
        </div>
        <button v-if="!editing" @click="startEdit"
          class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-2 transition shadow-sm">
          <Pencil class="w-3.5 h-3.5" /> Edit Profile
        </button>
      </div>

      <!-- Loading skeleton -->
      <div v-if="loading" class="space-y-4">
        <div v-for="n in 4" :key="n" class="h-16 bg-white rounded-xl border border-gray-100 animate-pulse" />
      </div>

      <!-- Error -->
      <div v-else-if="error && !doctor"
        class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
        <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ error }}
      </div>

      <!-- Profile card -->
      <div v-else-if="doctor" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Cover + avatar -->
        <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-600 relative">
          <div class="absolute -bottom-10 left-6">
            <div class="w-20 h-20 rounded-full border-4 border-white bg-gray-100 overflow-hidden flex items-center justify-center shadow-sm">
              <img v-if="doctor.profile_picture_url" :src="doctor.profile_picture_url"
                class="w-full h-full object-cover" :alt="fullName" />
              <span v-else class="text-2xl font-bold text-[#004795]">{{ initials }}</span>
            </div>
          </div>
        </div>

        <div class="pt-12 px-6 pb-6">
          <!-- Name & dept -->
          <div class="mb-5">
            <h2 class="text-lg font-bold text-gray-900">{{ fullName }}</h2>
            <p class="text-sm text-[#004795] font-semibold mt-0.5">{{ doctor.department?.name ?? '—' }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ doctor.hospital?.name ?? '—' }}</p>
          </div>

          <!-- View mode -->
          <div v-if="!editing" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <InfoItem label="Email"       :value="doctor.user?.email" />
              <InfoItem label="Phone"       :value="doctor.user?.phone || '—'" />
              <InfoItem label="License"     :value="doctor.license_number" />
              <InfoItem label="Experience"  :value="(doctor.years_experience ?? 0) + ' years'" />
              <InfoItem label="Fee"         :value="'ETB ' + Number(doctor.consultation_fee ?? 0).toLocaleString()" />
              <InfoItem label="Telehealth"  :value="doctor.is_telehealth_available ? 'Available' : 'Not available'" />
            </div>
            <div v-if="doctor.bio">
              <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Bio</p>
              <p class="text-sm text-gray-700 leading-relaxed">{{ doctor.bio }}</p>
            </div>
          </div>

          <!-- Edit mode -->
          <form v-else @submit.prevent="handleSave" class="space-y-4">
            <div v-if="saveError"
              class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
              <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ saveError }}
            </div>

            <!-- Read-only fields -->
            <div class="grid grid-cols-2 gap-4">
              <InfoItem label="Email"    :value="doctor.user?.email" />
              <InfoItem label="Phone"    :value="doctor.user?.phone || '—'" />
              <InfoItem label="License"  :value="doctor.license_number" />
            </div>

            <!-- Editable fields -->
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Consultation Fee (ETB)</label>
              <input v-model.number="form.consultation_fee" type="number" min="0"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bio</label>
              <textarea v-model="form.bio" rows="4" placeholder="Write a short professional bio..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none" />
            </div>
            <div class="flex items-center gap-3">
              <button type="button"
                @click="form.is_telehealth_available = !form.is_telehealth_available"
                :class="form.is_telehealth_available ? 'bg-[#004795]' : 'bg-gray-200'"
                class="relative w-10 h-5 rounded-full transition-colors duration-200">
                <span
                  :class="form.is_telehealth_available ? 'translate-x-5' : 'translate-x-0.5'"
                  class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200" />
              </button>
              <span class="text-sm text-gray-700 font-medium">Telemedicine Available</span>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Profile Picture</label>
              <input type="file" accept="image/*" @change="onPicChange"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600
                  file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0
                  file:text-xs file:font-semibold file:bg-[#004795]/10 file:text-[#004795]
                  hover:file:bg-[#004795]/20 transition" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
              <button type="button" @click="cancelEdit"
                class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                Cancel
              </button>
              <button type="submit" :disabled="saving"
                class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2">
                <Loader2 v-if="saving" class="w-3.5 h-3.5 animate-spin" />
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Pencil, AlertCircle, Loader2 } from "lucide-vue-next";
import doctorApi from "../../../api/doctorApi";
const InfoItem = {
  props: ["label", "value"],
  template: `
    <div>
      <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-0.5">{{ label }}</p>
      <p class="text-sm font-semibold text-gray-800">{{ value || '—' }}</p>
    </div>
  `,
};
const doctor    = ref(null);
const loading   = ref(false);
const error     = ref(null);
const editing   = ref(false);
const saving    = ref(false);
const saveError = ref(null);
const picFile   = ref(null);
const form      = ref({ consultation_fee: 0, bio: "", is_telehealth_available: false });

const fullName = computed(() => {
  const u = doctor.value?.user;
  return u ? `Dr. ${u.first_name} ${u.last_name}` : "—";
});

const initials = computed(() => {
  const u = doctor.value?.user;
  return u ? ((u.first_name?.[0] ?? "") + (u.last_name?.[0] ?? "")).toUpperCase() : "?";
});

async function load() {
  try {
    loading.value = true;
    error.value   = null;
    const res     = await doctorApi.getMe();
    doctor.value  = res.data?.data ?? res.data;
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to load profile.";
  } finally {
    loading.value = false;
  }
}

onMounted(load);
function startEdit() {
  form.value = {
    consultation_fee:         doctor.value.consultation_fee ?? 0,
    bio:                      doctor.value.bio ?? "",
    is_telehealth_available:  doctor.value.is_telehealth_available ?? false,
  };
  picFile.value  = null;
  saveError.value = null;
  editing.value  = true;
}

function cancelEdit() {
  editing.value   = false;
  saveError.value = null;
}

function onPicChange(e) {
  picFile.value = e.target.files[0] ?? null;
}

async function handleSave() {
  saveError.value = null;
  try {
    saving.value = true;
    const payload = { ...form.value };
    if (picFile.value) payload.profile_picture = picFile.value;
    const res    = await doctorApi.updateMe(payload);
    doctor.value = res.data?.data ?? res.data;
    editing.value = false;
  } catch (err) {
    const errors = err.response?.data?.errors;
    saveError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
  } finally {
    saving.value = false;
  }
}
</script>
