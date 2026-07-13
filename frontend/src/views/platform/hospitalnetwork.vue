<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Hospital Network Management</h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Manage and monitor all healthcare facilities across the platform.
        </p>
      </div>
      <button @click="openCreate"
        class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-2 transition shadow-sm flex-shrink-0">
        <Plus class="w-3.5 h-3.5" /> Add Hospital
      </button>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <!-- Total -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
          <Building2 class="w-6 h-6 text-blue-500" />
        </div>
        <div>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Hospitals</p>
          <p class="text-3xl font-black text-gray-900 tracking-tight mt-1">{{ totalCount }}</p>
        </div>
      </div>
      <!-- Active -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
          <CheckCircle class="w-6 h-6 text-emerald-500" />
        </div>
        <div>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Active Hospitals</p>
          <p class="text-3xl font-black text-gray-900 tracking-tight mt-1">{{ activeCount }}</p>
        </div>
      </div>
      <!-- Inactive -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
          <XCircle class="w-6 h-6 text-red-400" />
        </div>
        <div>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Suspended</p>
          <p class="text-3xl font-black text-gray-900 tracking-tight mt-1">{{ suspendedCount }}</p>
        </div>
      </div>
    </div>

    <!-- Search + filter bar -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 border-b border-gray-100">
        <!-- Search -->
        <div class="relative flex-1 min-w-0">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
          <input v-model="search" type="text" placeholder="Search hospitals..."
            class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#004795] focus:ring-1 focus:ring-[#004795]/20" />
        </div>
        <!-- Status filter -->
        <select v-model="statusFilter"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 bg-white focus:outline-none focus:border-[#004795] min-w-[130px]">
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
        <!-- Region filter -->
        <select v-model="regionFilter"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 bg-white focus:outline-none focus:border-[#004795] min-w-[130px]">
          <option value="">All Regions</option>
          <option v-for="r in regions" :key="r" :value="r">{{ r }}</option>
        </select>
        <button @click="resetFilters"
          class="flex items-center gap-1.5 px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition flex-shrink-0">
          <SlidersHorizontal class="w-4 h-4" />
          More Filters
        </button>
      </div>

      <!-- Error -->
      <div v-if="store.error"
        class="mx-5 mt-3 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
        <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ store.error }}
      </div>

      <!-- Loading -->
      <div v-if="store.loading && !store.hospitals.length" class="p-5 space-y-3">
        <div v-for="n in 4" :key="n" class="h-14 bg-gray-50 rounded-lg animate-pulse" />
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead>
            <tr class="border-b border-gray-100">
              <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Hospital</th>
              <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Code</th>
              <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Location</th>
              <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Doctors</th>
              <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Departments</th>
              <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
              <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="!paged.length">
              <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                <Building2 class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                <p class="text-sm font-medium">No hospitals found</p>
              </td>
            </tr>
            <tr v-for="h in paged" :key="h.id" class="hover:bg-gray-50/60 transition-colors">
              <!-- Hospital name + icon -->
              <td class="px-5 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <Building2 class="w-4 h-4 text-blue-500" />
                  </div>
                  <div>
                    <p class="font-semibold text-gray-800">{{ h.name }}</p>
                    <p v-if="h.email" class="text-xs text-gray-400 mt-0.5">{{ h.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-5 py-4 text-gray-500 font-mono text-xs">{{ h.code || '—' }}</td>
              <td class="px-5 py-4">
                <p class="text-gray-700 font-medium">{{ h.city || '—' }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ h.region || '' }}</p>
              </td>
              <td class="px-5 py-4 text-center font-semibold text-gray-700">
                {{ h.total_doctors ?? 0 }}
              </td>
              <td class="px-5 py-4 text-center font-semibold text-gray-700">
                {{ h.departments?.length ?? 0 }}
              </td>
              <td class="px-5 py-4">
                <span
                  :class="h.is_active
                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                    : 'bg-red-50 text-red-600 border-red-200'"
                  class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-0.5 rounded-full border"
                >
                  <span :class="h.is_active ? 'bg-emerald-500' : 'bg-red-400'" class="w-1.5 h-1.5 rounded-full" />
                  {{ h.is_active ? 'Active' : 'Suspended' }}
                </span>
              </td>
              <td class="px-5 py-4 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button @click="openEdit(h)" title="Edit"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-[#004795] hover:bg-[#004795]/10 transition">
                    <Pencil class="w-3.5 h-3.5" />
                  </button>
                  <button @click="confirmDelete(h)" title="Delete"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination footer -->
      <div v-if="filtered.length" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-gray-100">
        <p class="text-xs text-gray-500">
          Showing {{ (page - 1) * perPage + 1 }} to {{ Math.min(page * perPage, filtered.length) }} of {{ filtered.length }} entries
        </p>
        <div class="flex items-center gap-1">
          <button @click="page--" :disabled="page === 1"
            class="w-7 h-7 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 disabled:opacity-40 transition">
            <ChevronLeft class="w-3.5 h-3.5" />
          </button>
          <button v-for="p in visiblePages" :key="p" @click="page = p"
            :class="p === page ? 'bg-[#004795] text-white border-[#004795]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
            class="w-7 h-7 rounded-lg border text-xs font-semibold transition">
            {{ p }}
          </button>
          <button @click="page++" :disabled="page === totalPages"
            class="w-7 h-7 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 disabled:opacity-40 transition">
            <ChevronRight class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Hospital Form Modal -->
    <HospitalForm
      v-if="showForm"
      :hospital="selectedHospital"
      :loading="store.loading"
      :error="formError"
      @close="closeForm"
      @submit="handleFormSubmit"
    />

    <!-- Delete Confirm -->
    <div v-if="showDeleteConfirm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <Trash2 class="w-5 h-5 text-red-500" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-800">Delete Hospital</h3>
            <p class="text-xs text-gray-400 mt-0.5">This will remove all associated data.</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-5">
          Delete <span class="font-semibold text-gray-800">{{ hospitalToDelete?.name }}</span>?
        </p>
        <div class="flex items-center justify-end gap-3">
          <button @click="showDeleteConfirm = false"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Cancel
          </button>
          <button @click="handleDelete" :disabled="store.loading"
            class="px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="store.loading" class="w-3.5 h-3.5 animate-spin" />
            Delete
          </button>
        </div>
      </div>
    </div>

  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import {
  Plus, Building2, CheckCircle, XCircle, Search,
  SlidersHorizontal, AlertCircle, Pencil, Trash2,
  Loader2, ChevronLeft, ChevronRight,
} from "lucide-vue-next";
import { useHospitalStore } from "../../stores/hospitalStore";
import HospitalForm from "../../components/hospital/HospitalForm.vue";

const store = useHospitalStore();

// ── Filters ──────────────────────────────────────────────────────────────
const search       = ref("");
const statusFilter = ref("");
const regionFilter = ref("");

const page    = ref(1);
const perPage = 3;
const showForm         = ref(false);
const selectedHospital = ref(null);
const formError        = ref(null);
const showDeleteConfirm = ref(false);
const hospitalToDelete  = ref(null);

onMounted(() => store.fetchAll());


const regions = computed(() =>
  [...new Set(store.hospitals.map((h) => h.region).filter(Boolean))].sort()
);

const totalCount     = computed(() => store.hospitals.length);
const activeCount    = computed(() => store.hospitals.filter((h) => h.is_active).length);
const suspendedCount = computed(() => store.hospitals.filter((h) => !h.is_active).length);

const filtered = computed(() => {
  let list = store.hospitals;
  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter((h) =>
      h.name.toLowerCase().includes(q) ||
      (h.code ?? "").toLowerCase().includes(q) ||
      (h.city ?? "").toLowerCase().includes(q)
    );
  }
  if (statusFilter.value === "active")   list = list.filter((h) => h.is_active);
  if (statusFilter.value === "inactive") list = list.filter((h) => !h.is_active);
  if (regionFilter.value) list = list.filter((h) => h.region === regionFilter.value);
  return list;
});

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)));

const paged = computed(() => {
  const start = (page.value - 1) * perPage;
  return filtered.value.slice(start, start + perPage);
});

const visiblePages = computed(() => {
  const total = totalPages.value;
  if (total <= 5) return Array.from({ length: total }, (_, i) => i + 1);
  const p = page.value;
  if (p <= 3) return [1, 2, 3, "...", total];
  if (p >= total - 2) return [1, "...", total - 2, total - 1, total];
  return [1, "...", p - 1, p, p + 1, "...", total];
});

// Reset page when filters change
import { watch } from "vue";
watch([search, statusFilter, regionFilter], () => { page.value = 1; });

function resetFilters() {
  search.value       = "";
  statusFilter.value = "";
  regionFilter.value = "";
  page.value         = 1;
}

// ── CRUD ──────────────────────────────────────────────────────────────────
function openCreate() {
  selectedHospital.value = null;
  formError.value        = null;
  showForm.value         = true;
}

function openEdit(hospital) {
  selectedHospital.value = hospital;
  formError.value        = null;
  showForm.value         = true;
}

function closeForm() {
  showForm.value         = false;
  selectedHospital.value = null;
  formError.value        = null;
}

async function handleFormSubmit(payload) {
  formError.value = null;
  try {
    if (selectedHospital.value) {
      await store.update(selectedHospital.value.id, payload);
    } else {
      await store.create(payload);
    }
    closeForm();
  } catch (err) {
    const errors = err.response?.data?.errors;
    formError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
  }
}

function confirmDelete(hospital) {
  hospitalToDelete.value  = hospital;
  showDeleteConfirm.value = true;
}

async function handleDelete() {
  try {
    await store.destroy(hospitalToDelete.value.id);
    showDeleteConfirm.value = false;
    hospitalToDelete.value  = null;
  } catch { /* store.error shows it */ }
}
</script>
