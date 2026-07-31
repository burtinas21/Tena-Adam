<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 class="text-base font-bold text-gray-800">
            {{ isEditing ? 'Edit Hospital Admin' : 'Add Hospital Admin' }}
          </h3>
          <p class="text-xs text-gray-400 mt-0.5">
            {{ isEditing ? 'Update admin account details.' : 'Create a new admin account for a hospital.' }}
          </p>
        </div>
        <button @click="$emit('close')" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
          <X class="w-4 h-4" />
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="px-6 py-5 space-y-4">
        <div v-if="error" class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
          <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
          <span>{{ error }}</span>
        </div>

        <!-- Hospital searchable picker (create only) -->
        <div v-if="!isEditing">
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Hospital <span class="text-red-500">*</span>
          </label>

          <!-- Selected hospital chip -->
          <div v-if="selectedHospital"
            class="flex items-center gap-2 w-full border border-[#004795] rounded-lg px-3 py-2.5 bg-blue-50/40">
            <Building2 class="w-4 h-4 text-[#004795] flex-shrink-0" />
            <span class="flex-1 text-sm font-semibold text-gray-800 truncate">{{ selectedHospital.name }}</span>
            <button type="button" @click="clearHospital"
              class="p-0.5 text-gray-400 hover:text-red-500 transition flex-shrink-0">
              <X class="w-3.5 h-3.5" />
            </button>
          </div>

          <!-- Search input + dropdown -->
          <div v-else class="relative">
            <div class="relative">
              <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" />
              <input
                v-model="hospitalSearch"
                type="text"
                placeholder="Search hospital by name..."
                class="w-full border border-gray-200 rounded-lg pl-8 pr-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
              />
              <Loader2 v-if="hospitalSearchLoading"
                class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 animate-spin text-gray-400" />
            </div>

            <!-- Results dropdown -->
            <div v-if="filteredHospitals.length"
              class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden max-h-52 overflow-y-auto">
              <button
                v-for="h in filteredHospitals" :key="h.id"
                type="button"
                @click="selectHospital(h)"
                class="flex items-center gap-3 w-full px-3 py-2.5 hover:bg-blue-50 transition text-left"
              >
                <div class="w-7 h-7 rounded-lg bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
                  <Building2 class="w-3.5 h-3.5 text-[#004795]" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-800 truncate">{{ h.name }}</p>
                  <p v-if="h.city || h.address" class="text-[10px] text-gray-400 truncate">
                    {{ [h.city, h.address].filter(Boolean).join(' · ') }}
                  </p>
                </div>
              </button>
            </div>

            <!-- No results -->
            <div v-else-if="hospitalSearch.length >= 1 && !hospitalSearchLoading && !filteredHospitals.length"
              class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg px-4 py-3 text-xs text-gray-400">
              No hospitals found for "{{ hospitalSearch }}"
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              First Name <span class="text-red-500">*</span>
            </label>
            <input v-model="form.first_name" type="text" placeholder="Abebe" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Last Name <span class="text-red-500">*</span>
            </label>
            <input v-model="form.last_name" type="text" placeholder="Kebede" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Email <span class="text-red-500">*</span>
          </label>
          <input v-model="form.email" type="email" placeholder="admin@hospital.et" required
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone</label>
          <input v-model="form.phone" type="text" placeholder="+251 911 000 000"
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
        </div>

        <!-- Invitation notice (create only) -->
        <div v-if="!isEditing"
          class="flex items-start gap-2.5 bg-blue-50 border border-blue-200 rounded-lg px-3 py-3">
          <svg class="w-4 h-4 text-[#004795] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <p class="text-xs text-blue-700 leading-relaxed">
            An invitation email will be sent to the provided address. The admin will set their own password
            by clicking the activation link in the email.
          </p>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
          <button type="button" @click="$emit('close')"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Cancel
          </button>
          <button type="submit" :disabled="loading || (!isEditing && !form.hospital_id)"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="loading" class="w-3.5 h-3.5 animate-spin" />
            {{ isEditing ? 'Save Changes' : 'Create Admin' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { X, AlertCircle, Loader2, Search, Building2 } from "lucide-vue-next";

const props = defineProps({
  admin:     { type: Object, default: null },
  hospitals: { type: Array,  default: () => [] },
  loading:   { type: Boolean, default: false },
  error:     { type: String,  default: null },
});
const emit = defineEmits(["close", "submit"]);

const isEditing = computed(() => !!props.admin);

const form = ref({
  hospital_id: "", first_name: "", last_name: "",
  email: "", phone: "",
});

// ── Hospital search ───────────────────────────────────────────────────────
const hospitalSearch = ref("");
const selectedHospital = ref(null);
const hospitalSearchLoading = ref(false);

const filteredHospitals = computed(() => {
  const q = hospitalSearch.value.trim().toLowerCase();
  if (!q) return props.hospitals;
  return props.hospitals.filter((h) =>
    h.name?.toLowerCase().includes(q) ||
    h.city?.toLowerCase().includes(q) ||
    h.address?.toLowerCase().includes(q)
  );
});

function selectHospital(h) {
  selectedHospital.value = h;
  form.value.hospital_id = h.id;
  hospitalSearch.value = "";
}

function clearHospital() {
  selectedHospital.value = null;
  form.value.hospital_id = "";
  hospitalSearch.value = "";
}

watch(() => props.admin, (a) => {
  if (a) {
    form.value = {
      hospital_id: "",
      first_name: a.first_name ?? "",
      last_name:  a.last_name  ?? "",
      email:      a.email      ?? "",
      phone:      a.phone      ?? "",
    };
  } else {
    form.value = { hospital_id: "", first_name: "", last_name: "", email: "", phone: "" };
    clearHospital();
  }
}, { immediate: true });

function handleSubmit() {
  if (isEditing.value) {
    emit("submit", {
      first_name: form.value.first_name,
      last_name:  form.value.last_name,
      email:      form.value.email,
      phone:      form.value.phone || null,
    });
  } else {
    emit("submit", {
      hospital_id: form.value.hospital_id,
      first_name:  form.value.first_name,
      last_name:   form.value.last_name,
      email:       form.value.email,
      phone:       form.value.phone || null,
    });
  }
}
</script>
