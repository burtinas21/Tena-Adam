<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Queue Management</h1>
          <p class="text-xs text-gray-500 mt-0.5">Manage walk-in patients and today's queue.</p>
        </div>
        <div class="flex items-center gap-2">
          <input v-model="selectedDate" type="date"
            class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
        </div>
      </div>

      <!-- Doctor selector -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
        <label class="block text-xs font-semibold text-gray-700 mb-2">Select Doctor</label>
        <div v-if="store.loading && !store.doctors.length"
          class="h-10 bg-gray-100 animate-pulse rounded-lg" />
        <select v-else v-model="selectedDoctorId" @change="loadQueue"
          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition">
          <option value="" disabled>Choose a doctor</option>
          <option v-for="doc in store.doctors" :key="doc.id" :value="doc.id">
            Dr. {{ doc.user?.first_name }} {{ doc.user?.last_name }}
            {{ doc.department?.name ? `— ${doc.department.name}` : '' }}
          </option>
        </select>
      </div>

      <!-- No doctor selected -->
      <div v-if="!selectedDoctorId"
        class="bg-white rounded-xl border border-gray-100 shadow-sm py-20 flex flex-col items-center justify-center text-gray-400">
        <Stethoscope class="w-10 h-10 mb-3 text-gray-300" />
        <p class="text-sm font-medium">Select a doctor to view and manage the queue</p>
      </div>

      <template v-else>
        <!-- KPI row -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
          <div v-for="kpi in kpiCards" :key="kpi.label"
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold" :class="kpi.color">{{ kpi.value }}</p>
            <p class="text-xs text-gray-500 font-medium mt-0.5">{{ kpi.label }}</p>
          </div>
        </div>

        <!-- Add walk-in button -->
        <div class="flex justify-end mb-3">
          <button @click="openWalkIn"
            class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-2 transition shadow-sm">
            <UserPlus class="w-3.5 h-3.5" />
            Add Walk-in Patient
          </button>
        </div>

        <!-- Queue table -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <span class="text-sm font-bold text-gray-800">Today's Queue</span>
            <button @click="loadQueue" :disabled="queueLoading"
              class="text-xs font-semibold text-[#004795] hover:underline disabled:opacity-50">
              Refresh
            </button>
          </div>

          <!-- Loading -->
          <div v-if="queueLoading" class="divide-y divide-gray-50">
            <div v-for="n in 4" :key="n" class="px-5 py-3 flex items-center gap-4">
              <div class="w-7 h-7 rounded-full bg-gray-100 animate-pulse flex-shrink-0" />
              <div class="flex-1 space-y-2">
                <div class="h-3 bg-gray-100 animate-pulse rounded w-1/3" />
                <div class="h-2.5 bg-gray-100 animate-pulse rounded w-1/4" />
              </div>
            </div>
          </div>

          <!-- Empty -->
          <div v-else-if="!queueEntries.length"
            class="py-16 text-center text-gray-400">
            <ListOrdered class="w-8 h-8 mx-auto mb-2 text-gray-300" />
            <p class="text-sm font-medium">No queue entries for this doctor today</p>
          </div>

          <!-- Entries -->
          <div v-else class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                  <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">#</th>
                  <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Patient</th>
                  <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Type</th>
                  <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Status</th>
                  <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Called At</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="entry in queueEntries" :key="entry.id"
                  class="hover:bg-gray-50/60 transition-colors">
                  <td class="px-5 py-3">
                    <span class="w-8 h-8 rounded-full bg-[#004795]/10 text-[#004795] font-bold text-sm flex items-center justify-center">
                      {{ entry.queue_number }}
                    </span>
                  </td>
                  <td class="px-5 py-3">
                    <p class="font-semibold text-gray-800">
                      {{ patientName(entry) }}
                    </p>
                    <p v-if="entry.walk_in_phone" class="text-xs text-gray-400">
                      {{ entry.walk_in_phone }}
                    </p>
                  </td>
                  <td class="px-5 py-3">
                    <span :class="entry.appointment_id
                      ? 'bg-blue-50 text-blue-600 border-blue-200'
                      : 'bg-purple-50 text-purple-600 border-purple-200'"
                      class="text-[11px] font-semibold px-2 py-0.5 rounded-full border">
                      {{ entry.appointment_id ? 'Appointment' : 'Walk-in' }}
                    </span>
                  </td>
                  <td class="px-5 py-3">
                    <span :class="statusClass(entry.status)"
                      class="text-[11px] font-semibold px-2 py-0.5 rounded-full border capitalize">
                      {{ entry.status.replace('_', ' ') }}
                    </span>
                  </td>
                  <td class="px-5 py-3 text-xs text-gray-500">
                    {{ entry.called_at ? formatTime(entry.called_at) : '—' }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </template>
    </div>

    <!-- ── WALK-IN MODAL ───────────────────────────────────────────────────── -->
    <div v-if="showWalkIn"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="closeWalkIn">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md flex flex-col">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
          <div>
            <h3 class="text-sm font-bold text-gray-800">Add Walk-in Patient</h3>
            <p class="text-xs text-gray-400 mt-0.5">Add an unscheduled patient to today's queue.</p>
          </div>
          <button @click="closeWalkIn" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
            <X class="w-4 h-4" />
          </button>
        </div>

        <div class="px-6 py-5 space-y-4">
          <!-- Walk-in type toggle -->
          <div class="flex gap-2">
            <button @click="walkInType = 'new'"
              :class="walkInType === 'new' ? 'bg-[#004795] text-white' : 'bg-gray-100 text-gray-600'"
              class="flex-1 text-xs font-semibold py-2 rounded-lg transition">
              New / Unregistered
            </button>
            <button @click="walkInType = 'registered'"
              :class="walkInType === 'registered' ? 'bg-[#004795] text-white' : 'bg-gray-100 text-gray-600'"
              class="flex-1 text-xs font-semibold py-2 rounded-lg transition">
              Registered Patient
            </button>
          </div>

          <div v-if="walkInError"
            class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ walkInError }}
          </div>

          <!-- New walk-in: name + phone -->
          <template v-if="walkInType === 'new'">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                Patient Name <span class="text-red-500">*</span>
              </label>
              <input v-model="walkInForm.walk_in_patient_name" type="text" required
                placeholder="Full name"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone</label>
              <input v-model="walkInForm.walk_in_phone" type="text" placeholder="+251..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
          </template>

          <!-- Registered patient: search -->
          <template v-else>
            <div class="relative">
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                Search Patient <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" />
                <input v-model="patientSearch" @input="onPatientSearch" type="text"
                  placeholder="Name, email or phone"
                  class="w-full border border-gray-200 rounded-lg pl-8 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
              </div>
              <!-- Results dropdown -->
              <div v-if="store.searchResults.length"
                class="absolute z-10 w-full bg-white border border-gray-200 rounded-xl shadow-lg mt-1 max-h-44 overflow-y-auto">
                <div v-for="p in store.searchResults" :key="p.id"
                  @click="selectPatient(p)"
                  class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer flex items-center gap-3 transition">
                  <div class="w-7 h-7 rounded-full bg-[#004795]/10 flex items-center justify-center text-xs font-bold text-[#004795] flex-shrink-0">
                    {{ (p.first_name?.[0] ?? '') + (p.last_name?.[0] ?? '') }}
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-gray-800">{{ p.first_name }} {{ p.last_name }}</p>
                    <p class="text-xs text-gray-400">{{ p.phone || p.email }}</p>
                  </div>
                </div>
              </div>
            </div>
            <!-- Selected patient badge -->
            <div v-if="selectedPatientForQueue"
              class="flex items-center gap-3 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2.5">
              <div class="w-7 h-7 rounded-full bg-[#004795] flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                {{ (selectedPatientForQueue.first_name?.[0] ?? '') + (selectedPatientForQueue.last_name?.[0] ?? '') }}
              </div>
              <div class="flex-1">
                <p class="text-sm font-semibold text-gray-800">
                  {{ selectedPatientForQueue.first_name }} {{ selectedPatientForQueue.last_name }}
                </p>
                <p class="text-xs text-gray-400">{{ selectedPatientForQueue.phone || selectedPatientForQueue.email }}</p>
              </div>
              <button @click="selectedPatientForQueue = null" class="text-gray-400 hover:text-gray-600">
                <X class="w-3.5 h-3.5" />
              </button>
            </div>
          </template>
        </div>

        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
          <button @click="closeWalkIn"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Cancel
          </button>
          <button @click="handleAddWalkIn" :disabled="addingWalkIn"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="addingWalkIn" class="w-3.5 h-3.5 animate-spin" />
            Add to Queue
          </button>
        </div>
      </div>
    </div>

    <!-- Success toast -->
    <Transition name="toast">
      <div v-if="successToast"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-emerald-500 text-white text-sm font-semibold px-4 py-3 rounded-xl shadow-lg">
        <CheckCircle2 class="w-4 h-4" />
        Patient added to queue!
      </div>
    </Transition>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import {
  Stethoscope, UserPlus, ListOrdered, X, AlertCircle,
  Loader2, Search, CheckCircle2,
} from "lucide-vue-next";
import { useReceptionistStore } from "../../../stores/receptionistStore";
import queueApi from "../../../api/queueApi";
import { echo } from "../../../plugins/echo";

const store = useReceptionistStore();

const selectedDoctorId = ref("");
const selectedDate     = ref(new Date().toISOString().slice(0, 10));
const queueEntries     = ref([]);
const queueLoading     = ref(false);
const queueError       = ref(null);

// KPI cards
const kpiCards = computed(() => [
  { label: "Waiting",         value: queueEntries.value.filter((e) => e.status === "waiting").length,         color: "text-amber-500"   },
  { label: "In Consultation", value: queueEntries.value.filter((e) => e.status === "in_consultation").length, color: "text-blue-600"    },
  { label: "Completed",       value: queueEntries.value.filter((e) => e.status === "completed").length,       color: "text-emerald-600" },
  { label: "Total",           value: queueEntries.value.length,                                                color: "text-gray-700"    },
]);

async function loadQueue() {
  if (!selectedDoctorId.value) return;
  queueLoading.value = true;
  queueError.value   = null;
  try {
    // Init queue from appointments first (idempotent)
    await queueApi.init(selectedDoctorId.value, selectedDate.value);
    const res     = await queueApi.getDoctorQueue(selectedDoctorId.value, selectedDate.value);
    queueEntries.value = res.data?.data ?? res.data ?? [];
  } catch (err) {
    queueError.value = err.response?.data?.message || "Failed to load queue";
  } finally {
    queueLoading.value = false;
  }
}

// ── Walk-in ───────────────────────────────────────────────────────────────────
const showWalkIn   = ref(false);
const walkInType   = ref("new");
const walkInForm   = ref({ walk_in_patient_name: "", walk_in_phone: "" });
const walkInError  = ref(null);
const addingWalkIn = ref(false);
const successToast = ref(false);

// Patient search for registered walk-in
const patientSearch             = ref("");
const selectedPatientForQueue   = ref(null);
let searchTimer = null;

function onPatientSearch() {
  clearTimeout(searchTimer);
  selectedPatientForQueue.value = null;
  if (patientSearch.value.length < 2) { store.searchResults = []; return; }
  searchTimer = setTimeout(() => store.searchPatients(patientSearch.value), 350);
}

function selectPatient(p) {
  selectedPatientForQueue.value = p;
  store.searchResults = [];
  patientSearch.value = "";
}

function openWalkIn() {
  walkInForm.value  = { walk_in_patient_name: "", walk_in_phone: "" };
  walkInError.value = null;
  walkInType.value  = "new";
  patientSearch.value = "";
  selectedPatientForQueue.value = null;
  store.searchResults = [];
  showWalkIn.value  = true;
}

function closeWalkIn() {
  showWalkIn.value = false;
}

// Get hospital_id from the currently selected doctor (always available and correct)
function getHospitalId() {
  const doc = store.doctors.find((d) => d.id === selectedDoctorId.value);
  return doc?.hospital_id ?? doc?.hospital?.id ?? null;
}

async function handleAddWalkIn() {
  walkInError.value = null;
  const hospitalId = getHospitalId();

  if (!hospitalId) {
    walkInError.value = "Could not determine hospital. Please select a doctor first.";
    return;
  }

  const payload = {
    doctor_id:   selectedDoctorId.value,
    hospital_id: hospitalId,
    queue_date:  selectedDate.value,
  };

  if (walkInType.value === "new") {
    if (!walkInForm.value.walk_in_patient_name?.trim()) {
      walkInError.value = "Patient name is required.";
      return;
    }
    payload.walk_in_patient_name = walkInForm.value.walk_in_patient_name;
    payload.walk_in_phone        = walkInForm.value.walk_in_phone || null;
  } else {
    if (!selectedPatientForQueue.value) {
      walkInError.value = "Please select a registered patient.";
      return;
    }
    // For registered patients we still use walk_in fields with their name
    payload.walk_in_patient_name = `${selectedPatientForQueue.value.first_name} ${selectedPatientForQueue.value.last_name}`;
    payload.walk_in_phone        = selectedPatientForQueue.value.phone || null;
  }

  try {
    addingWalkIn.value = true;
    await queueApi.generate(payload);
    closeWalkIn();
    await loadQueue();
    successToast.value = true;
    setTimeout(() => (successToast.value = false), 3000);
  } catch (err) {
    const errors = err.response?.data?.errors;
    walkInError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Failed to add to queue.";
  } finally {
    addingWalkIn.value = false;
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function patientName(entry) {
  if (entry.walk_in_patient_name) return entry.walk_in_patient_name;
  const u = entry.appointment?.patient?.user;
  return u ? `${u.first_name} ${u.last_name}` : "—";
}

function formatTime(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" });
}

function statusClass(status) {
  return {
    waiting:         "bg-amber-50 text-amber-700 border-amber-200",
    in_consultation: "bg-blue-50 text-blue-600 border-blue-200",
    completed:       "bg-emerald-50 text-emerald-700 border-emerald-200",
    skipped:         "bg-orange-50 text-orange-600 border-orange-200",
    no_show:         "bg-gray-50 text-gray-500 border-gray-200",
  }[status] ?? "bg-gray-50 text-gray-500 border-gray-200";
}

onMounted(async () => {
  await store.fetchDoctors();

  // ── WebSocket: live queue updates for receptionist ─────────────────────
  try {
    echo.private("reception.queue")
      .listen(".queue.updated", () => {
        // Refresh the current doctor's queue when any queue entry changes
        if (selectedDoctorId.value) loadQueue();
      });
  } catch {
    /* WebSocket unavailable */
  }
});

onUnmounted(() => {
  try { echo.leave("reception.queue"); } catch { /* noop */ }
});
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
</style>
