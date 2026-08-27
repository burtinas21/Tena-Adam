<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] px-2 py-3 sm:p-6 overflow-y-auto font-sans dark:text-slate-200">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-3 sm:mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Hospital Network Management</h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Manage and monitor all healthcare facilities across the platform.
        </p>
      </div>
      <div>
        <p></p>
      </div>
      <button @click="openCreate"
        class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-2 transition shadow-sm flex-shrink-0">
        <Plus class="w-3.5 h-3.5" /> Add Hospital
      </button>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-4 mb-3 sm:mb-6">
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
      <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-2 py-3 sm:px-5 sm:py-4 border-b border-gray-100">
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

      <div v-else class="w-full">
        <table class="w-full table-fixed">
          <colgroup>
            <col style="width:26%" />
            <col style="width:10%" />
            <col style="width:18%" />
            <col style="width:10%" />
            <col style="width:12%" />
            <col style="width:14%" />
            <col style="width:10%" />
          </colgroup>
          <thead>
            <tr class="border-b border-gray-100">
              <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-wider text-left">Hospital</th>
              <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-wider text-left">Code</th>
              <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-wider text-left">Location</th>
              <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Doctors</th>
              <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Depts</th>
              <th class="px-2 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-wider text-left">Status</th>
              <th class="px-1 sm:px-4 py-2.5 text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="!paged.length">
              <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                <Building2 class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                <p class="text-sm font-medium">No hospitals found</p>
              </td>
            </tr>
            <tr v-for="h in paged" :key="h.id" class="hover:bg-gray-50/60 transition-colors align-middle">
              <!-- Hospital name -->
              <td class="px-2 sm:px-4 py-2 sm:py-3">
                <div class="flex items-center gap-1.5">
                  <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <Building2 class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-blue-500" />
                  </div>
                  <div class="min-w-0">
                    <p class="font-semibold text-[10px] sm:text-xs text-gray-800 truncate">{{ h.name }}</p>
                    <p v-if="h.email" class="text-[9px] sm:text-[10px] text-gray-400 truncate">{{ h.email }}</p>
                  </div>
                </div>
              </td>
              <!-- Code -->
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-[9px] sm:text-[10px] text-gray-500 font-mono truncate">{{ h.code || '—' }}</td>
              <!-- Location -->
              <td class="px-2 sm:px-4 py-2 sm:py-3">
                <p class="text-[10px] sm:text-xs text-gray-700 font-medium truncate">{{ h.city || '—' }}</p>
                <p class="text-[9px] sm:text-[10px] text-gray-400 truncate">{{ h.region || '' }}</p>
              </td>
              <!-- Doctors -->
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-[10px] sm:text-xs font-semibold text-gray-700">
                {{ h.total_doctors ?? 0 }}
              </td>
              <!-- Departments -->
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-[10px] sm:text-xs font-semibold text-gray-700">
                {{ h.departments?.length ?? 0 }}
              </td>
              <!-- Status -->
              <td class="px-2 sm:px-4 py-2 sm:py-3">
                <span
                  :class="h.is_active
                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                    : 'bg-red-50 text-red-600 border-red-200'"
                  class="inline-flex items-center gap-1 text-[9px] sm:text-[10px] font-semibold px-1.5 sm:px-2 py-0.5 rounded-full border whitespace-nowrap"
                >
                  <span :class="h.is_active ? 'bg-emerald-500' : 'bg-red-400'" class="w-1.5 h-1.5 rounded-full flex-shrink-0" />
                  {{ h.is_active ? 'Active' : 'Suspended' }}
                </span>
              </td>
              <!-- Actions — three-dot dropdown -->
              <td class="px-2 sm:px-4 py-2 sm:py-3 text-right">
                <div class="relative inline-block" @click.stop>
                  <button
                    @click="toggleMenu(h.id)"
                    class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                  >
                    <MoreVertical class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                  </button>
                  <div
                    v-if="openMenuId === h.id"
                    class="absolute right-0 mt-1 w-32 bg-white border border-gray-100 rounded-xl shadow-lg z-30 py-1"
                  >
                    <button
                      @click="openEdit(h); closeMenu()"
                      class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition"
                    >
                      <Pencil class="w-3.5 h-3.5 text-[#004795]" /> Edit
                    </button>
                    <button
                      @click="confirmDelete(h); closeMenu()"
                      class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition"
                    >
                      <Trash2 class="w-3.5 h-3.5" /> Delete
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination footer -->
      <div v-if="filtered.length" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-2 py-3 sm:px-5 sm:py-4 border-t border-gray-100">
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
  Loader2, ChevronLeft, ChevronRight, MoreVertical,
} from "lucide-vue-next";
import { useHospitalStore } from "../../stores/hospitalStore";
import HospitalForm from "../../components/hospital/HospitalForm.vue";
import { useToast } from "../../composables/useToast";

const { showToast } = useToast();

const store = useHospitalStore();
const openMenuId = ref(null);
function toggleMenu(id) { openMenuId.value = openMenuId.value === id ? null : id; }
function closeMenu() { openMenuId.value = null; }
onMounted(() => {
  store.fetchAll();
  document.addEventListener("click", closeMenu);
});
import { onUnmounted } from "vue";
onUnmounted(() => document.removeEventListener("click", closeMenu));
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
  const isEdit = !!selectedHospital.value;
  try {
    if (isEdit) {
      await store.update(selectedHospital.value.id, payload);
      showToast("Hospital updated successfully", "success");
    } else {
      await store.create(payload);
      showToast("Hospital registered successfully", "success");
    }
    closeForm();
  } catch (err) {
    const errors = err.response?.data?.errors;
    const msg = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
    formError.value = msg;
    showToast(msg, "error");
  }
}

function confirmDelete(hospital) {
  hospitalToDelete.value  = hospital;
  showDeleteConfirm.value = true;
}

async function handleDelete() {
  const name = hospitalToDelete.value?.name ?? "Hospital";
  try {
    await store.destroy(hospitalToDelete.value.id);
    showDeleteConfirm.value = false;
    hospitalToDelete.value  = null;
    showToast(`"${name}" deleted successfully`, "success");
  } catch (err) {
    const msg = err.response?.data?.message || "Failed to delete hospital.";
    showToast(msg, "error");
  }
}
</script>
