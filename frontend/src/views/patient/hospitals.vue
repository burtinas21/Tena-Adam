<template>
  <div class="min-h-screen bg-[#f8fafc] dark:bg-[#0f172a] p-6 lg:p-8 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Hospital Discovery Network</h1>
      <p class="text-sm text-gray-700 mt-1 max-w-2xl">
        Search and connect with specialized medical facilities, filter by departments,
        and verify telemedicine capabilities across our clinical network.
      </p>
    </div>

    <!-- Search + Filter block -->
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm mb-6">
      <!-- Top search row -->
      <div class="flex gap-3 mb-4">
        <div class="relative flex-1">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
          <input
            v-model="search"
            type="text"
            placeholder="Search Hospital Name..."
            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#004bb5] focus:ring-1 focus:ring-[#004bb5]/20"
          />
        </div>
        <button
          @click="resetFilters"
          class="flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition"
        >
          <SlidersHorizontal class="w-4 h-4" />
          Advanced Filters
        </button>
        <button
          class="px-6 py-2 bg-[#004bb5] text-white text-sm font-semibold rounded-lg hover:bg-[#003da1] transition"
        >
          Search
        </button>
      </div>

      <!-- Filter dropdowns row -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 items-end">
        <div>
          <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Region</label>
          <select v-model="regionFilter"
            class="w-full p-2 border border-gray-200 rounded-lg text-sm text-gray-600 bg-white focus:outline-none focus:border-[#004bb5]">
            <option value="">Select Region</option>
            <option v-for="r in regions" :key="r" :value="r">{{ r }}</option>
          </select>
        </div>
        <div>
          <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">City</label>
          <select v-model="cityFilter"
            class="w-full p-2 border border-gray-200 rounded-lg text-sm text-gray-600 bg-white focus:outline-none focus:border-[#004bb5]">
            <option value="">Select City</option>
            <option v-for="c in cities" :key="c" :value="c">{{ c }}</option>
          </select>
        </div>
        <div>
          <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Department</label>
          <select v-model="deptFilter"
            class="w-full p-2 border border-gray-200 rounded-lg text-sm text-gray-600 bg-white focus:outline-none focus:border-[#004bb5]">
            <option value="">All Departments</option>
            <option v-for="d in allDepts" :key="d" :value="d">{{ d }}</option>
          </select>
        </div>
        <div class="flex items-center">
          <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 cursor-pointer w-full justify-between hover:bg-gray-50 transition">
            <div class="flex items-center gap-2">
              <input type="checkbox" v-model="teleOnly"
                class="w-4 h-4 text-[#004bb5] border-gray-300 rounded focus:ring-[#004bb5]" />
              <span class="text-sm font-medium text-gray-700">Telemedicine Available</span>
            </div>
            <Video class="w-4 h-4 text-emerald-500" />
          </label>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="n in 3" :key="n" class="bg-white rounded-xl border border-gray-100 p-5 h-64 animate-pulse" />
    </div>

    <!-- Error -->
    <div v-else-if="error"
      class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3 mb-4">
      <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ error }}
    </div>

    <!-- Results header + sort -->
    <div v-else>
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-semibold text-gray-700">
          Results
          <span class="text-gray-400 font-normal">({{ filtered.length }} facilities)</span>
        </h2>
        <div class="flex items-center gap-2 text-sm text-gray-500">
          <span>Sort by:</span>
          <select v-model="sortBy"
            class="bg-transparent font-semibold text-[#004bb5] focus:outline-none cursor-pointer text-sm">
            <option value="name">Relevance</option>
            <option value="doctors">Total Doctors</option>
          </select>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="!filtered.length" class="text-center py-16 text-gray-400">
        <Building2 class="w-10 h-10 mx-auto mb-3 text-gray-300" />
        <p class="text-sm font-medium">No hospitals found matching your filters.</p>
      </div>

      <!-- Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <HospitalCard
          v-for="hospital in filtered"
          :key="hospital.id"
          :hospital="hospital"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Search, SlidersHorizontal, Video, AlertCircle, Building2 } from "lucide-vue-next";
import hospitalApi from "../../api/hospitalApi";
import HospitalCard from "../../components/patient/hospital/HospitalCard.vue";

// ── State ─────────────────────────────────────────────────────────────────
const rawHospitals  = ref([]);
const loading       = ref(false);
const error         = ref(null);
const search        = ref("");
const cityFilter    = ref("");
const regionFilter  = ref("");
const deptFilter    = ref("");
const teleOnly      = ref(false);
const sortBy        = ref("name");

// ── Load ──────────────────────────────────────────────────────────────────
async function load() {
  try {
    loading.value = true;
    error.value   = null;
    const res     = await hospitalApi.getAll();
    const list    = res.data?.data ?? res.data ?? [];

    rawHospitals.value = list.map((h) => {
      const depts     = (h.departments ?? []).slice(0, 3).map((d) => d.name ?? d);
      const extraCount = Math.max(0, (h.departments?.length ?? 0) - 3);
      return {
        id:              h.id,
        name:            h.name,
        location:        `${h.city ?? ""}${h.region ? ", " + h.region : ""}`,
        phone:           h.phone ?? "—",
        departments:     depts,
        moreDeptsCount:  extraCount,
        totalDoctors:    h.total_doctors ?? 0,
        telemedAvailable: h.has_telehealth ?? false,
        _city:           h.city ?? "",
        _region:         h.region ?? "",
        _depts:          (h.departments ?? []).map((d) => d.name ?? d),
      };
    });
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to load hospitals.";
  } finally {
    loading.value = false;
  }
}

onMounted(load);

// ── Derived ───────────────────────────────────────────────────────────────
const cities   = computed(() => [...new Set(rawHospitals.value.map((h) => h._city).filter(Boolean))].sort());
const regions  = computed(() => [...new Set(rawHospitals.value.map((h) => h._region).filter(Boolean))].sort());
const allDepts = computed(() => {
  const set = new Set();
  rawHospitals.value.forEach((h) => h._depts.forEach((d) => set.add(d)));
  return [...set].sort();
});

const filtered = computed(() => {
  let list = rawHospitals.value;

  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter((h) =>
      h.name.toLowerCase().includes(q) ||
      h.location.toLowerCase().includes(q)
    );
  }
  if (cityFilter.value)   list = list.filter((h) => h._city === cityFilter.value);
  if (regionFilter.value) list = list.filter((h) => h._region === regionFilter.value);
  if (deptFilter.value)   list = list.filter((h) => h._depts.includes(deptFilter.value));
  if (teleOnly.value)     list = list.filter((h) => h.telemedAvailable);

  return [...list].sort((a, b) =>
    sortBy.value === "doctors"
      ? b.totalDoctors - a.totalDoctors
      : a.name.localeCompare(b.name)
  );
});

function resetFilters() {
  search.value       = "";
  cityFilter.value   = "";
  regionFilter.value = "";
  deptFilter.value   = "";
  teleOnly.value     = false;
  sortBy.value       = "name";
}
</script>
