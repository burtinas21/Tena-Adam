<script setup>
import { ref, computed, onMounted, onUnmounted, provide } from "vue";
import { RefreshCw, UserPlus, Calendar } from "lucide-vue-next";
import { useAuthStore } from "../../stores/authStore";
import doctorApi from "../../api/doctorApi";
import queueApi from "../../api/queueApi";
import hospitalApi from "../../api/hospitalApi";
import { useToast } from "../../composables/useToast";

import QueueMetrics from "../../components/hospital-admin/queue/QueueMetrics.vue";
import LiveRegistry from "../../components/hospital-admin/queue/LiveRegistry.vue";
import DepartmentLoad from "../../components/hospital-admin/queue/DepartmentLoad.vue";
import ActiveConsultations from "../../components/hospital-admin/queue/ActiveConsultations.vue";

const { showToast } = useToast();

const authStore = useAuthStore();

// ── State ──────────────────────────────────────────────────────────────
const allEntries  = ref([]);   // flat list of all queue entries across all doctors
const doctors     = ref([]);
const loading     = ref(false);
const error       = ref(null);
const today       = new Date().toISOString().split("T")[0];
const selectedDate = ref(today);

// Walk-in modal
const showWalkIn  = ref(false);
const walkInForm  = ref({ name: "", phone: "", doctor_id: "", hospital_id: "" });
const walkInError = ref(null);
const walkInSaving = ref(false);

// ── Provide queue data to child components ─────────────────────────────
provide("queueEntries", allEntries);

// ── Computed: derive hospital_id from the logged-in admin's hospitals ──
const hospitalId = computed(() => {
  const u = authStore.user;
  // hospital_admin's hospitals relation
  return u?.hospitals?.[0]?.id ?? null;
});

// ── Load all doctors + their queues for today ─────────────────────────
async function load() {
  loading.value = true;
  error.value   = null;
  allEntries.value = [];

  try {
    // Load doctors (scoped to this hospital by the backend)
    const res  = await doctorApi.getAll();
    const list = res.data?.data ?? res.data ?? [];
    doctors.value = list;

    if (!list.length) return;

    // Fetch queues for all doctors in parallel
    const results = await Promise.allSettled(
      list.map((doc) =>
        queueApi.getDoctorQueue(doc.id, selectedDate.value).then((r) => {
          const entries = r.data?.data ?? r.data ?? [];
          return entries.map((e) => ({
            ...e,
            _doctorName: `${doc.user?.first_name ?? ""} ${doc.user?.last_name ?? ""}`.trim(),
            _department: doc.department?.name ?? "—",
          }));
        })
      )
    );

    const flat = [];
    results.forEach((r) => {
      if (r.status === "fulfilled") flat.push(...r.value);
    });

    // Sort by queue_number across all doctors
    allEntries.value = flat.sort((a, b) => a.queue_number - b.queue_number);
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to load queue data.";
  } finally {
    loading.value = false;
  }
}

// ── Add walk-in ───────────────────────────────────────────────────────
async function handleWalkIn() {
  walkInError.value = null;
  if (!walkInForm.value.doctor_id) {
    walkInError.value = "Please select a doctor.";
    return;
  }
  if (!walkInForm.value.name.trim()) {
    walkInError.value = "Patient name is required.";
    return;
  }

  const hId = walkInForm.value.hospital_id
    || hospitalId.value
    || doctors.value.find((d) => d.id === walkInForm.value.doctor_id)?.hospital?.id;

  if (!hId) {
    walkInError.value = "Hospital information is unavailable.";
    return;
  }

  walkInSaving.value = true;
  try {
    await queueApi.generate({
      doctor_id:            walkInForm.value.doctor_id,
      hospital_id:          hId,
      queue_date:           selectedDate.value,
      walk_in_patient_name: walkInForm.value.name,
      walk_in_phone:        walkInForm.value.phone || null,
    });
    const patientName = walkInForm.value.name;
    walkInForm.value = { name: "", phone: "", doctor_id: "", hospital_id: "" };
    showWalkIn.value = false;
    await load();
    showToast(`Walk-in patient "${patientName}" added to queue successfully`, "success");
  } catch (err) {
    const msg = err.response?.data?.message || "Failed to add walk-in.";
    walkInError.value = msg;
    showToast(msg, "error");
  } finally {
    walkInSaving.value = false;
  }
}

// ── Polling ───────────────────────────────────────────────────────────
let pollTimer = null;
onMounted(async () => {
  await load();
  pollTimer = setInterval(load, 30_000);
});
onUnmounted(() => clearInterval(pollTimer));
</script>

<template>
  <main class="min-h-screen bg-slate-50/50 p-6 md:p-8 font-sans antialiased">
    <div class="max-w-7xl mx-auto">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <div class="flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-[9px] font-bold text-emerald-600 tracking-wider uppercase">Live Monitoring</span>
          </div>
          <h1 class="text-xl font-bold text-gray-900 tracking-tight mt-0.5">Queue Management Operations</h1>
        </div>

        <div class="flex items-center gap-2 self-end sm:self-auto flex-wrap">
          <!-- Date picker -->
          <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-semibold text-gray-600 shadow-sm">
            <Calendar class="w-3.5 h-3.5 text-gray-400" />
            <input v-model="selectedDate" type="date" @change="load"
              class="bg-transparent focus:outline-none text-xs text-gray-700" />
          </div>

          <!-- Refresh -->
          <button @click="load" :disabled="loading"
            class="flex items-center gap-1.5 bg-white border border-gray-200/90 text-gray-600 px-3 py-2 rounded-xl text-xs font-semibold shadow-sm hover:bg-gray-50 transition-colors">
            <RefreshCw class="w-3.5 h-3.5" :class="{ 'animate-spin': loading }" />
            Refresh
          </button>

          <!-- New Walk-in -->
          <button @click="showWalkIn = true"
            class="flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition-colors">
            <UserPlus class="w-3.5 h-3.5" />
            New Walk-in
          </button>
        </div>
      </div>

      <!-- Error -->
      <div v-if="error"
        class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
        {{ error }}
      </div>

      <!-- KPI metrics -->
      <QueueMetrics :entries="allEntries" :loading="loading" />

      <!-- Main grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2">
          <LiveRegistry :entries="allEntries" :loading="loading" />
        </div>
        <div class="lg:col-span-1 space-y-6">
          <DepartmentLoad :entries="allEntries" />
          <ActiveConsultations :entries="allEntries" />
        </div>
      </div>
    </div>
  </main>

  <!-- Walk-in modal -->
  <div v-if="showWalkIn"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    @click.self="showWalkIn = false">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
      <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-800">Add Walk-in Patient</h3>
        <button @click="showWalkIn = false" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition text-lg leading-none">&times;</button>
      </div>
      <form @submit.prevent="handleWalkIn" class="px-6 py-4 space-y-4">
        <div v-if="walkInError"
          class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-3 py-2.5">
          {{ walkInError }}
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Select Doctor <span class="text-red-500">*</span>
          </label>
          <select v-model="walkInForm.doctor_id" required
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
            <option value="" disabled>Choose a doctor</option>
            <option v-for="doc in doctors" :key="doc.id" :value="doc.id">
              Dr. {{ doc.user?.first_name }} {{ doc.user?.last_name }} — {{ doc.department?.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Patient Name <span class="text-red-500">*</span>
          </label>
          <input v-model="walkInForm.name" type="text" required placeholder="Full name"
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone</label>
          <input v-model="walkInForm.phone" type="tel" placeholder="+251 911 000 000"
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" />
        </div>

        <div class="flex items-center justify-end gap-3 pt-1">
          <button type="button" @click="showWalkIn = false"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Cancel
          </button>
          <button type="submit" :disabled="walkInSaving"
            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition disabled:opacity-60">
            Add to Queue
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
