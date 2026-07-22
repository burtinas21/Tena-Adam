<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Patient Registration</h1>
          <p class="text-xs text-gray-500 mt-0.5">Register new patients or find existing ones.</p>
        </div>
        <button @click="openForm"
          class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-2 transition shadow-sm">
          <UserPlus class="w-3.5 h-3.5" />
          Register New Patient
        </button>
      </div>

      <!-- Search bar -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5 flex items-center gap-3">
        <Search class="w-4 h-4 text-gray-400 flex-shrink-0" />
        <input v-model="searchQuery" @input="onSearch" type="text"
          placeholder="Search patients by name, email or phone..."
          class="flex-1 text-sm text-gray-700 placeholder-gray-400 outline-none bg-transparent" />
        <span v-if="store.searchLoading" class="text-xs text-gray-400">Searching...</span>
      </div>

      <!-- Patient table -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
          <span class="text-sm font-bold text-gray-800">
            {{ searchQuery ? 'Search Results' : 'All Registered Patients' }}
          </span>
          <span class="text-xs text-gray-400">{{ displayedPatients.length }} patients</span>
        </div>

        <!-- Loading skeleton -->
        <div v-if="store.loading && !store.patients.length" class="divide-y divide-gray-50">
          <div v-for="n in 5" :key="n" class="px-5 py-3 flex items-center gap-4">
            <div class="w-9 h-9 rounded-full bg-gray-100 animate-pulse flex-shrink-0" />
            <div class="flex-1 space-y-2">
              <div class="h-3 bg-gray-100 animate-pulse rounded w-1/3" />
              <div class="h-2.5 bg-gray-100 animate-pulse rounded w-1/4" />
            </div>
          </div>
        </div>

        <!-- Empty state -->
        <div v-else-if="!displayedPatients.length"
          class="py-16 text-center text-gray-400">
          <Users class="w-8 h-8 mx-auto mb-2 text-gray-300" />
          <p class="text-sm font-medium">
            {{ searchQuery ? 'No patients match your search' : 'No patients registered yet' }}
          </p>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Patient</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Contact</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Gender</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Date of Birth</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="patient in displayedPatients" :key="patient.id"
                class="hover:bg-gray-50/60 transition-colors">
                <td class="px-5 py-3">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
                      <span class="text-xs font-bold text-[#004795]">
                        {{ initials(patient) }}
                      </span>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-800">
                        {{ patient.first_name }} {{ patient.last_name }}
                      </p>
                      <p class="text-xs text-gray-400">{{ patient.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-3 text-gray-600">{{ patient.phone || '—' }}</td>
                <td class="px-5 py-3 text-gray-600 capitalize">{{ patient.gender || '—' }}</td>
                <td class="px-5 py-3 text-gray-600">{{ formatDate(patient.date_of_birth) }}</td>
                <td class="px-5 py-3">
                  <span :class="statusClass(patient.patient_status)"
                    class="text-[11px] font-semibold px-2 py-0.5 rounded-full border capitalize">
                    {{ patient.patient_status || 'active' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ── REGISTRATION MODAL ────────────────────────────────────────────── -->
    <div v-if="showForm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="closeForm">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0">
          <div>
            <h3 class="text-sm font-bold text-gray-800">Register New Patient</h3>
            <p class="text-xs text-gray-400 mt-0.5">Basic info only — doctor fills medical details later.</p>
          </div>
          <button @click="closeForm" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
            <X class="w-4 h-4" />
          </button>
        </div>

        <!-- Form body -->
        <form @submit.prevent="handleSubmit" class="px-6 py-5 space-y-4 overflow-y-auto flex-1">
          <!-- Error banner -->
          <div v-if="formError"
            class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ formError }}
          </div>

          <!-- Name row -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
              <input v-model="form.first_name" type="text" required placeholder="Abebe"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
              <input v-model="form.last_name" type="text" required placeholder="Girma"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
            <input v-model="form.email" type="email" required placeholder="patient@email.com"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
          </div>

          <!-- Phone + Password -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone</label>
              <input v-model="form.phone" type="text" placeholder="+251911000000"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                Temporary Password <span class="text-red-500">*</span>
              </label>
              <input v-model="form.password" type="password" required minlength="8" placeholder="Min 8 chars"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
          </div>

          <!-- DoB + Gender -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Date of Birth <span class="text-red-500">*</span></label>
              <input v-model="form.date_of_birth" type="date" required
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Gender <span class="text-red-500">*</span></label>
              <select v-model="form.gender" required
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition">
                <option value="" disabled>Select gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>

          <!-- Address -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Address</label>
            <input v-model="form.address" type="text" placeholder="Addis Ababa, Bole"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
          </div>

          <!-- Occupation + National ID -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Occupation</label>
              <input v-model="form.occupation" type="text" placeholder="Teacher"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">National ID</label>
              <input v-model="form.national_id" type="text" placeholder="ETH-123456"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
          </div>

          <!-- Note about medical details -->
          <div class="flex items-start gap-2 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2.5">
            <Info class="w-3.5 h-3.5 text-blue-500 flex-shrink-0 mt-0.5" />
            <p class="text-xs text-blue-700">
              Blood type, allergies and medical history will be filled by the doctor during consultation.
            </p>
          </div>
        </form>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0">
          <button type="button" @click="closeForm"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Cancel
          </button>
          <button @click="handleSubmit" :disabled="store.loading"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="store.loading" class="w-3.5 h-3.5 animate-spin" />
            Register Patient
          </button>
        </div>
      </div>
    </div>

    <!-- ── SUCCESS TOAST ───────────────────────────────────────────────────── -->
    <Transition name="toast">
      <div v-if="successToast"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-emerald-500 text-white text-sm font-semibold px-4 py-3 rounded-xl shadow-lg">
        <CheckCircle2 class="w-4 h-4" />
        Patient registered successfully!
      </div>
    </Transition>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import {
  UserPlus, Search, Users, X, AlertCircle, Loader2, Info, CheckCircle2,
} from "lucide-vue-next";
import { useReceptionistStore } from "../../stores/receptionistStore";

const store = useReceptionistStore();

// ── Search ────────────────────────────────────────────────────────────────────
const searchQuery = ref("");
let searchTimer   = null;

function onSearch() {
  clearTimeout(searchTimer);
  if (searchQuery.value.length < 2) {
    store.searchResults = [];
    return;
  }
  searchTimer = setTimeout(() => {
    store.searchPatients(searchQuery.value);
  }, 350);
}

const displayedPatients = computed(() =>
  searchQuery.value.length >= 2 ? store.searchResults : store.patients
);

// ── Form ──────────────────────────────────────────────────────────────────────
const showForm    = ref(false);
const formError   = ref(null);
const successToast = ref(false);
const form = ref({});

function openForm() {
  formError.value = null;
  form.value = {
    first_name: "", last_name: "", email: "", phone: "",
    password: "", date_of_birth: "", gender: "",
    address: "", occupation: "", national_id: "",
  };
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
  formError.value = null;
}

async function handleSubmit() {
  formError.value = null;
  try {
    await store.registerPatient(form.value);
    closeForm();
    successToast.value = true;
    setTimeout(() => (successToast.value = false), 3000);
  } catch (err) {
    const errors = err.response?.data?.errors;
    formError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function initials(p) {
  return ((p?.first_name?.[0] ?? "") + (p?.last_name?.[0] ?? "")).toUpperCase() || "?";
}

function formatDate(d) {
  if (!d) return "—";
  return new Date(d).toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" });
}

function statusClass(status) {
  return {
    active:   "bg-emerald-50 text-emerald-700 border-emerald-200",
    pending:  "bg-amber-50 text-amber-700 border-amber-200",
    inactive: "bg-gray-50 text-gray-500 border-gray-200",
  }[status] ?? "bg-blue-50 text-blue-600 border-blue-200";
}

onMounted(() => store.fetchPatients());
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
</style>
