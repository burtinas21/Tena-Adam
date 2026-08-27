<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-5xl mx-auto">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Symptoms</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">Manage symptoms and department mappings</p>
        </div>
        <button
          @click="openCreate"
          class="bg-[#004795] hover:bg-[#003670] text-white text-xs font-bold py-2.5 px-4 rounded-lg flex items-center gap-2 transition shadow-sm"
        >
          <Plus class="w-3.5 h-3.5" /> Add Symptom
        </button>
      </div>

      <!-- Tabs -->
      <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit mb-6">
        <button
          v-for="tab in tabs" :key="tab.key"
          @click="activeTab = tab.key"
          :class="activeTab === tab.key ? 'bg-white text-[#004795] shadow-sm' : 'text-gray-500 hover:text-gray-700'"
          class="px-4 py-2 text-xs font-bold rounded-lg transition"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Error banner -->
      <div
        v-if="store.error"
        class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" /> {{ store.error }}
      </div>

      <!-- TAB: Symptoms list -->
      <div v-if="activeTab === 'symptoms'">
        <!-- Search -->
        <div class="relative mb-4">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search symptoms..."
            class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          />
        </div>

        <!-- Loading -->
        <div v-if="store.loading" class="space-y-2">
          <div v-for="n in 5" :key="n" class="h-14 bg-white rounded-xl border border-gray-100 animate-pulse" />
        </div>

        <!-- Table -->
        <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <table class="w-full text-xs">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-5 py-3 font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-500 uppercase tracking-wide">Category</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Description</th>
                <th class="text-right px-5 py-3 font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="symptom in filteredSymptoms" :key="symptom.id"
                class="border-b border-gray-50 hover:bg-gray-50/50 transition"
              >
                <td class="px-5 py-3 font-semibold text-gray-800">{{ symptom.name }}</td>
                <td class="px-5 py-3">
                  <span v-if="symptom.category" class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-full font-semibold capitalize">
                    {{ symptom.category }}
                  </span>
                  <span v-else class="text-gray-400">—</span>
                </td>
                <td class="px-5 py-3 text-gray-500 hidden sm:table-cell max-w-xs truncate">
                  {{ symptom.description || '—' }}
                </td>
                <td class="px-5 py-3 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      @click="openMappings(symptom)"
                      class="text-xs font-semibold text-[#004795] hover:underline transition"
                    >
                      Mappings
                    </button>
                    <button
                      @click="openEdit(symptom)"
                      class="text-xs font-semibold text-amber-600 hover:underline transition"
                    >
                      Edit
                    </button>
                    <button
                      @click="confirmDelete(symptom)"
                      class="text-xs font-semibold text-red-500 hover:underline transition"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!filteredSymptoms.length">
                <td colspan="4" class="text-center py-12 text-gray-400">No symptoms found</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB: Analytics -->
      <div v-if="activeTab === 'analytics'">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-sm font-bold text-gray-700">Top Selected Symptoms</h2>
          <button
            @click="store.fetchTopSymptoms()"
            class="text-xs font-semibold text-[#004795] hover:underline flex items-center gap-1"
          >
            <Loader2 v-if="store.analyticsLoading" class="w-3 h-3 animate-spin" />
            Refresh
          </button>
        </div>
        <div v-if="store.analyticsLoading" class="space-y-2">
          <div v-for="n in 5" :key="n" class="h-12 bg-white rounded-xl border border-gray-100 animate-pulse" />
        </div>
        <div v-else-if="store.topSymptoms.length" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <table class="w-full text-xs">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-5 py-3 font-semibold text-gray-500 uppercase tracking-wide">#</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-500 uppercase tracking-wide">Symptom</th>
                <th class="text-right px-5 py-3 font-semibold text-gray-500 uppercase tracking-wide">Selections</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(item, idx) in store.topSymptoms" :key="item.symptom_id"
                class="border-b border-gray-50 hover:bg-gray-50/50 transition"
              >
                <td class="px-5 py-3 font-bold text-gray-400">{{ idx + 1 }}</td>
                <td class="px-5 py-3 font-semibold text-gray-800">{{ item.symptom?.name ?? item.symptom_id }}</td>
                <td class="px-5 py-3 text-right">
                  <span class="bg-[#004795]/10 text-[#004795] font-bold px-3 py-1 rounded-full">{{ item.total }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm py-12 text-center text-gray-400 text-sm">
          No analytics data yet.
        </div>
      </div>

    </div>

    <!-- ── Create / Edit Symptom Modal ─────────────────────────────── -->
    <div
      v-if="showForm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="showForm = false"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
          <h3 class="text-sm font-bold text-gray-800">{{ editing ? 'Edit Symptom' : 'Add Symptom' }}</h3>
          <button @click="showForm = false" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
            <X class="w-4 h-4" />
          </button>
        </div>
        <div class="px-6 py-4 space-y-4">
          <div
            v-if="formError"
            class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"
          >
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ formError }}
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
            <input
              v-model="form.name"
              type="text"
              placeholder="e.g. Fever"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Category</label>
            <input
              v-model="form.category"
              type="text"
              placeholder="e.g. General, Respiratory"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Brief description of this symptom..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none"
            />
          </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
          <button @click="showForm = false" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
          <button
            @click="handleSave"
            :disabled="!form.name.trim() || formSaving"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-50 flex items-center gap-2"
          >
            <Loader2 v-if="formSaving" class="w-3.5 h-3.5 animate-spin" />
            {{ editing ? 'Save Changes' : 'Create Symptom' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ── Department Mappings Modal ───────────────────────────────── -->
    <div
      v-if="showMappings"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="showMappings = false"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
          <div>
            <h3 class="text-sm font-bold text-gray-800">Department Mappings</h3>
            <p class="text-xs text-gray-400 mt-0.5">Symptom: <span class="text-[#004795] font-semibold">{{ mappingSymptom?.name }}</span></p>
          </div>
          <button @click="showMappings = false" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
            <X class="w-4 h-4" />
          </button>
        </div>

        <div class="px-6 py-4 space-y-4 overflow-y-auto flex-1">
          <!-- Existing mappings -->
          <div v-if="store.mappingsLoading" class="space-y-2">
            <div v-for="n in 3" :key="n" class="h-12 bg-gray-100 rounded-lg animate-pulse" />
          </div>
          <div v-else-if="store.mappings.length" class="space-y-2">
            <div
              v-for="m in store.mappings" :key="m.id"
              class="flex items-center justify-between gap-3 bg-gray-50 border border-gray-100 rounded-xl px-4 py-3"
            >
              <div>
                <p class="text-sm font-semibold text-gray-800">{{ m.department?.name }}</p>
                <div class="flex items-center gap-2 mt-0.5">
                  <span :class="evidenceClass(m.evidence_level)" class="text-xs font-semibold px-2 py-0.5 rounded-full border">
                    {{ m.evidence_level }}
                  </span>
                  <span class="text-xs text-gray-400">{{ m.relevance_score }}% relevance</span>
                  <span v-if="m.is_primary" class="text-xs font-semibold text-emerald-600">• Primary</span>
                </div>
              </div>
              <button
                @click="deleteMapping(m.id)"
                class="text-xs font-semibold text-red-500 hover:underline"
              >Delete</button>
            </div>
          </div>
          <div v-else class="text-xs text-gray-400 text-center py-4">No mappings yet for this symptom.</div>

          <!-- Add new mapping form -->
          <div class="border-t border-gray-100 pt-4">
            <p class="text-xs font-bold text-gray-700 mb-3">Add Mapping</p>
            <div
              v-if="mappingError"
              class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5 mb-3"
            >
              <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ mappingError }}
            </div>
            <div class="space-y-3">
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Department <span class="text-red-500">*</span></label>
                <select
                  v-model="mappingForm.department_id"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
                >
                  <option value="" disabled>Choose department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1">Relevance Score (0–100)</label>
                  <input
                    v-model.number="mappingForm.relevance_score"
                    type="number" min="0" max="100"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1">Evidence Level</label>
                  <select
                    v-model="mappingForm.evidence_level"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
                  >
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                  </select>
                </div>
              </div>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="mappingForm.is_primary" type="checkbox" class="rounded" />
                <span class="text-xs font-semibold text-gray-700">Set as primary department</span>
              </label>
              <button
                @click="handleAddMapping"
                :disabled="!mappingForm.department_id || mappingSaving"
                class="w-full py-2.5 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-50 flex items-center justify-center gap-2"
              >
                <Loader2 v-if="mappingSaving" class="w-3.5 h-3.5 animate-spin" />
                Add Mapping
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Delete Confirm Modal ────────────────────────────────────── -->
    <div
      v-if="showDeleteConfirm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="showDeleteConfirm = false"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <h3 class="text-sm font-bold text-gray-800 mb-2">Delete Symptom</h3>
        <p class="text-xs text-gray-500 mb-6">
          Are you sure you want to delete <span class="font-semibold text-gray-800">{{ deleteTarget?.name }}</span>?
          This will also remove all its department mappings.
        </p>
        <div class="flex items-center justify-end gap-3">
          <button @click="showDeleteConfirm = false" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
          <button
            @click="handleDelete"
            :disabled="deleteSaving"
            class="px-5 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition flex items-center gap-2"
          >
            <Loader2 v-if="deleteSaving" class="w-3.5 h-3.5 animate-spin" />
            Delete
          </button>
        </div>
      </div>
    </div>

  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Plus, Search, AlertCircle, X, Loader2 } from "lucide-vue-next";
import { useSymptomStore } from "../../stores/symptomStore";
import departmentApi from "../../api/departmentApi";
import { useToast } from "../../composables/useToast";

const { showToast } = useToast();

const store = useSymptomStore();

const tabs = [
  { key: "symptoms", label: "Symptoms" },
  { key: "analytics", label: "Analytics" },
];
const activeTab = ref("symptoms");

const searchQuery = ref("");
const filteredSymptoms = computed(() => {
  if (!searchQuery.value.trim()) return store.symptoms;
  const q = searchQuery.value.toLowerCase();
  return store.symptoms.filter(
    (s) =>
      s.name.toLowerCase().includes(q) ||
      (s.category ?? "").toLowerCase().includes(q)
  );
});

// ── Symptom form ──────────────────────────────────────────────────────────
const showForm = ref(false);
const editing = ref(null);
const formSaving = ref(false);
const formError = ref(null);
const form = ref({ name: "", category: "", description: "" });

function openCreate() {
  editing.value = null;
  form.value = { name: "", category: "", description: "" };
  formError.value = null;
  showForm.value = true;
}

function openEdit(symptom) {
  editing.value = symptom;
  form.value = {
    name: symptom.name,
    category: symptom.category ?? "",
    description: symptom.description ?? "",
  };
  formError.value = null;
  showForm.value = true;
}

async function handleSave() {
  formError.value = null;
  formSaving.value = true;
  const isEdit = !!editing.value;
  try {
    if (isEdit) {
      await store.update(editing.value.id, form.value);
      showToast("Symptom updated successfully", "success");
    } else {
      await store.create(form.value);
      showToast("Symptom created successfully", "success");
    }
    showForm.value = false;
  } catch (err) {
    const errors = err.response?.data?.errors;
    const msg = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Failed to save symptom.";
    formError.value = msg;
    showToast(msg, "error");
  } finally {
    formSaving.value = false;
  }
}

// ── Delete ────────────────────────────────────────────────────────────────
const showDeleteConfirm = ref(false);
const deleteTarget = ref(null);
const deleteSaving = ref(false);

function confirmDelete(symptom) {
  deleteTarget.value = symptom;
  showDeleteConfirm.value = true;
}

async function handleDelete() {
  deleteSaving.value = true;
  const name = deleteTarget.value?.name;
  try {
    await store.destroy(deleteTarget.value.id);
    showDeleteConfirm.value = false;
    showToast(`Symptom "${name}" deleted successfully`, "success");
  } catch (err) {
    const msg = err.response?.data?.message || "Failed to delete symptom.";
    showToast(msg, "error");
  } finally {
    deleteSaving.value = false;
  }
}

// ── Mappings ──────────────────────────────────────────────────────────────
const showMappings = ref(false);
const mappingSymptom = ref(null);
const departments = ref([]);
const mappingSaving = ref(false);
const mappingError = ref(null);
const mappingForm = ref({
  department_id: "",
  relevance_score: 70,
  is_primary: false,
  evidence_level: "medium",
});

async function openMappings(symptom) {
  mappingSymptom.value = symptom;
  mappingError.value = null;
  mappingForm.value = { department_id: "", relevance_score: 70, is_primary: false, evidence_level: "medium" };
  showMappings.value = true;
  await store.fetchMappingsBySymptom(symptom.id);
}

async function handleAddMapping() {
  mappingError.value = null;
  mappingSaving.value = true;
  try {
    await store.createMapping({
      symptom_id: mappingSymptom.value.id,
      ...mappingForm.value,
    });
    mappingForm.value = { department_id: "", relevance_score: 70, is_primary: false, evidence_level: "medium" };
    await store.fetchMappingsBySymptom(mappingSymptom.value.id);
    showToast("Department mapping added successfully", "success");
  } catch (err) {
    const errors = err.response?.data?.errors;
    const msg = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Failed to add mapping.";
    mappingError.value = msg;
    showToast(msg, "error");
  } finally {
    mappingSaving.value = false;
  }
}

async function deleteMapping(id) {
  try {
    await store.destroyMapping(id);
    await store.fetchMappingsBySymptom(mappingSymptom.value.id);
    showToast("Mapping deleted successfully", "success");
  } catch (err) {
    const msg = err.response?.data?.message || "Failed to delete mapping.";
    showToast(msg, "error");
  }
}

function evidenceClass(level) {
  return (
    {
      high: "bg-emerald-50 text-emerald-700 border-emerald-200",
      medium: "bg-amber-50 text-amber-700 border-amber-200",
      low: "bg-gray-50 text-gray-500 border-gray-200",
    }[level] ?? "bg-gray-50 text-gray-500 border-gray-200"
  );
}

onMounted(async () => {
  await store.fetchAll();
  if (activeTab.value === "analytics") await store.fetchTopSymptoms();
  try {
    const res = await departmentApi.getAll();
    departments.value = res.data?.data ?? res.data ?? [];
  } catch { /* silent */ }
});
</script>
