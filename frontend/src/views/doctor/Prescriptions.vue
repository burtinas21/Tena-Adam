<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-4 sm:p-6 lg:p-8 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-6xl mx-auto">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Prescriptions</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">All prescriptions from your consultations</p>
        </div>
      </div>

      <!-- KPI cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div
          v-for="stat in stats"
          :key="stat.label"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-5"
        >
          <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="stat.bg">
              <component :is="stat.icon" class="w-4.5 h-4.5" :class="stat.color" />
            </div>
          </div>
          <p class="text-2xl font-black text-gray-900">{{ stat.value }}</p>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">{{ stat.label }}</p>
        </div>
      </div>

      <!-- Error -->
      <div
        v-if="store.error"
        class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" />
        {{ store.error }}
      </div>

      <!-- Table card -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <!-- Filter bar -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 border-b border-gray-100">
          <div class="relative flex-1">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
            <input
              v-model="search"
              type="text"
              placeholder="Search medication or patient..."
              class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#004795]"
            />
          </div>
          <select
            v-model="statusFilter"
            class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-[#004795]"
          >
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>

        <!-- Loading -->
        <div v-if="store.loading && !store.prescriptions.length" class="p-5 space-y-3">
          <div v-for="n in 4" :key="n" class="h-14 bg-gray-50 rounded-lg animate-pulse" />
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="border-b border-gray-100">
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Medication</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Patient</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Dosage</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Frequency</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Duration</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="!filtered.length">
                <td colspan="7" class="px-5 py-10 text-center text-gray-400">
                  <Pill class="w-7 h-7 mx-auto mb-2 text-gray-200" />
                  <p class="text-xs font-medium">No prescriptions found</p>
                </td>
              </tr>
              <tr
                v-for="rx in filtered"
                :key="rx.id"
                class="hover:bg-gray-50/60 transition-colors"
              >
                <!-- Medication -->
                <td class="px-5 py-3">
                  <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                      <Pill class="w-3.5 h-3.5 text-violet-500" />
                    </div>
                    <div>
                      <p class="font-bold text-gray-800 text-xs">{{ rx.medication_name }}</p>
                      <p v-if="rx.medication?.generic_name" class="text-[10px] text-gray-400">
                        {{ rx.medication.generic_name }}
                      </p>
                    </div>
                  </div>
                </td>
                <!-- Patient -->
                <td class="px-5 py-3">
                  <p class="font-semibold text-gray-700">{{ rx.patient?.name || "—" }}</p>
                  <p v-if="rx.encounter?.encounter_date" class="text-[10px] text-gray-400">
                    {{ formatDate(rx.encounter.encounter_date) }}
                  </p>
                </td>
                <!-- Dosage -->
                <td class="px-5 py-3">
                  <span class="font-mono text-slate-700">{{ rx.dosage }}</span>
                </td>
                <!-- Frequency -->
                <td class="px-5 py-3">
                  <p class="text-gray-600">{{ rx.frequency }}</p>
                  <p v-if="rx.route" class="text-[10px] text-gray-400 capitalize">{{ rx.route }}</p>
                </td>
                <!-- Duration -->
                <td class="px-5 py-3">
                  <span v-if="rx.duration_days" class="text-gray-600">{{ rx.duration_days }}d</span>
                  <span v-else class="text-gray-300">—</span>
                </td>
                <!-- Status -->
                <td class="px-5 py-3">
                  <span
                    :class="statusClass(rx.status)"
                    class="text-[10px] font-semibold px-2 py-0.5 rounded-full border capitalize"
                  >
                    {{ rx.status }}
                  </span>
                </td>
                <!-- Actions -->
                <td class="px-5 py-3 text-right">
                  <div v-if="rx.status === 'active'" class="flex items-center justify-end gap-1.5">
                    <button
                      @click="handleComplete(rx.id)"
                      :disabled="store.saving"
                      class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded hover:bg-emerald-100 transition"
                    >
                      Complete
                    </button>
                    <button
                      @click="handleCancel(rx.id)"
                      :disabled="store.saving"
                      class="text-[10px] font-bold text-red-500 bg-red-50 border border-red-200 px-2 py-1 rounded hover:bg-red-100 transition"
                    >
                      Cancel
                    </button>
                  </div>
                  <span v-else class="text-gray-300 text-[10px]">—</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { AlertCircle, Search, Pill, ClipboardCheck, CheckCircle, XCircle } from "lucide-vue-next";
import { usePrescriptionStore } from "../../stores/prescriptionStore";

const store       = usePrescriptionStore();
const search      = ref("");
const statusFilter = ref("");

onMounted(() => store.fetchAll());

const stats = computed(() => [
  {
    label: "Total",
    value: store.prescriptions.length,
    icon: Pill,
    bg: "bg-violet-50",
    color: "text-violet-500",
  },
  {
    label: "Active",
    value: store.active.length,
    icon: ClipboardCheck,
    bg: "bg-blue-50",
    color: "text-blue-500",
  },
  {
    label: "Completed",
    value: store.completed.length,
    icon: CheckCircle,
    bg: "bg-emerald-50",
    color: "text-emerald-500",
  },
  {
    label: "Cancelled",
    value: store.cancelled.length,
    icon: XCircle,
    bg: "bg-red-50",
    color: "text-red-400",
  },
]);

const filtered = computed(() => {
  let list = store.prescriptions;
  if (statusFilter.value) list = list.filter((p) => p.status === statusFilter.value);
  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter(
      (p) =>
        (p.medication_name ?? "").toLowerCase().includes(q) ||
        (p.patient?.name ?? "").toLowerCase().includes(q)
    );
  }
  return list;
});

function formatDate(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleDateString("en-ET", { day: "numeric", month: "short", year: "numeric" });
}

function statusClass(status) {
  return {
    active:    "bg-blue-50 text-blue-700 border-blue-100",
    completed: "bg-emerald-50 text-emerald-700 border-emerald-100",
    cancelled: "bg-red-50 text-red-600 border-red-200",
  }[status] ?? "bg-gray-50 text-gray-500 border-gray-200";
}

async function handleComplete(id) {
  try { await store.complete(id); } catch { /* error shown */ }
}
async function handleCancel(id) {
  try { await store.cancel(id); } catch { /* error shown */ }
}
</script>
