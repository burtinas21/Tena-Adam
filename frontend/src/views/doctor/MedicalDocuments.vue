<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-4 sm:p-6 lg:p-8 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Medical Documents</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">
            Lab reports, imaging, and clinical files attached to consultations
          </p>
        </div>
        <button
          @click="showUploadModal = true"
          class="flex items-center gap-2 px-4 py-2 bg-[#004795] text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition shadow-sm"
        >
          <Upload class="w-4 h-4" />
          Upload Document
        </button>
      </div>

      <!-- KPI strip -->
      <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
        <div
          v-for="cat in categoryKpis"
          :key="cat.type"
          class="bg-white border rounded-xl p-3 text-center shadow-sm"
          :class="cat.count > 0 ? 'border-slate-200' : 'border-slate-100'"
        >
          <p class="text-lg font-black text-slate-900">{{ cat.count }}</p>
          <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ cat.label }}</p>
        </div>
      </div>

      <!-- Error -->
      <div
        v-if="store.error"
        class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" />
        {{ store.error }}
      </div>

      <!-- Filter bar -->
      <div class="flex flex-col sm:flex-row items-center gap-3">
        <div class="relative flex-1">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
          <input
            v-model="search"
            type="text"
            placeholder="Search file name or patient..."
            class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#004795]"
          />
        </div>
        <select
          v-model="typeFilter"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-[#004795]"
        >
          <option value="">All Types</option>
          <option v-for="t in docTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
        </select>
      </div>

      <!-- Loading -->
      <div v-if="store.loading" class="space-y-3">
        <div v-for="n in 4" :key="n" class="h-16 bg-white rounded-xl animate-pulse border border-slate-100" />
      </div>

      <!-- Empty state -->
      <div
        v-else-if="!filtered.length"
        class="bg-white border border-slate-200 rounded-xl p-12 text-center"
      >
        <FileArchive class="w-10 h-10 mx-auto mb-3 text-slate-200" />
        <p class="text-sm font-semibold text-slate-400">No documents found</p>
        <p class="text-xs text-slate-300 mt-1">Upload the first document using the button above</p>
      </div>

      <!-- Document list -->
      <div v-else class="bg-white border border-slate-200 rounded-xl shadow-sm divide-y divide-slate-100">
        <div
          v-for="doc in filtered"
          :key="doc.id"
          class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50/50 transition"
        >
          <!-- Icon -->
          <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" :class="typeIconClass(doc.document_type)">
            <component :is="typeIcon(doc.document_type)" class="w-5 h-5" />
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <p class="text-sm font-bold text-slate-800 truncate">{{ doc.file_name }}</p>
              <span :class="typeBadgeClass(doc.document_type)" class="text-[9px] font-bold px-1.5 py-0.5 rounded border uppercase tracking-wider">
                {{ docTypeLabel(doc.document_type) }}
              </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">
              <span v-if="doc.patient">
                {{ doc.patient.user?.first_name }} {{ doc.patient.user?.last_name }}
              </span>
              <span v-if="doc.encounter?.encounter_date">
                · {{ formatDate(doc.encounter.encounter_date) }}
              </span>
              <span v-if="doc.file_size">
                · {{ formatSize(doc.file_size) }}
              </span>
            </p>
            <p v-if="doc.description" class="text-[11px] text-slate-500 italic mt-0.5">{{ doc.description }}</p>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2 flex-shrink-0">
            <a
              :href="storageUrl(doc.file_url)"
              target="_blank"
              rel="noopener"
              class="flex items-center gap-1 text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-1.5 rounded-lg hover:bg-blue-100 transition"
            >
              <Eye class="w-3.5 h-3.5" />
              View
            </a>
            <a
              :href="store.downloadUrl(doc.id)"
              class="flex items-center gap-1 text-[10px] font-bold text-slate-600 bg-white border border-slate-200 px-2.5 py-1.5 rounded-lg hover:bg-slate-50 transition"
            >
              <Download class="w-3.5 h-3.5" />
              Download
            </a>
            <button
              @click="confirmDelete(doc)"
              class="flex items-center gap-1 text-[10px] font-bold text-red-500 bg-red-50 border border-red-100 px-2.5 py-1.5 rounded-lg hover:bg-red-100 transition"
            >
              <Trash2 class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Upload Modal ──────────────────────────────────────────────────── -->
    <Teleport to="body">
      <div
        v-if="showUploadModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        @click.self="closeUpload"
      >
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
          <!-- Modal header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <Upload class="w-4 h-4 text-[#004795]" />
              <h3 class="text-sm font-bold text-slate-900">Upload Medical Document</h3>
            </div>
            <button @click="closeUpload" class="text-slate-400 hover:text-slate-600">
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Form -->
          <div class="px-6 py-5 space-y-4">
            <!-- Document type -->
            <div class="space-y-1">
              <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                Document Type <span class="text-rose-400">*</span>
              </label>
              <select
                v-model="uploadForm.document_type"
                class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-[#004795]"
              >
                <option value="">Select type</option>
                <option v-for="t in docTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
              </select>
            </div>

            <!-- Patient ID (required) -->
            <div class="space-y-1">
              <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                Patient ID <span class="text-rose-400">*</span>
              </label>
              <input
                v-model="uploadForm.patient_id"
                type="text"
                placeholder="UUID of the patient"
                class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#004795] font-mono"
              />
            </div>

            <!-- Encounter ID (optional) -->
            <div class="space-y-1">
              <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                Encounter ID <span class="text-[10px] font-normal text-slate-300">(optional)</span>
              </label>
              <input
                v-model="uploadForm.encounter_id"
                type="text"
                placeholder="Link to a consultation"
                class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#004795] font-mono"
              />
            </div>

            <!-- Description -->
            <div class="space-y-1">
              <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Description</label>
              <textarea
                v-model="uploadForm.description"
                rows="2"
                placeholder="Brief note about this document..."
                class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#004795]"
              ></textarea>
            </div>

            <!-- File picker -->
            <div class="space-y-1">
              <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                File <span class="text-rose-400">*</span>
              </label>
              <div
                class="border-2 border-dashed border-slate-200 rounded-xl p-5 text-center hover:border-[#004795] transition cursor-pointer"
                @click="$refs.fileInput.click()"
                @dragover.prevent
                @drop.prevent="onDrop"
              >
                <Paperclip class="w-6 h-6 mx-auto mb-2 text-slate-300" />
                <p v-if="!uploadForm.file" class="text-xs text-slate-400">
                  Click to select or drag & drop (max 10 MB)
                </p>
                <p v-else class="text-xs font-semibold text-[#004795]">
                  {{ uploadForm.file.name }} ({{ formatSize(uploadForm.file.size) }})
                </p>
                <input ref="fileInput" type="file" class="hidden" @change="onFileChange" />
              </div>
            </div>

            <!-- Error -->
            <div v-if="store.error" class="flex items-center gap-2 text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
              <AlertCircle class="w-4 h-4 flex-shrink-0" />
              {{ store.error }}
            </div>
          </div>

          <!-- Footer -->
          <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button @click="closeUpload" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
              Cancel
            </button>
            <button
              @click="handleUpload"
              :disabled="store.uploading"
              class="flex items-center gap-2 px-5 py-2 bg-[#004795] text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
            >
              <Upload class="w-3.5 h-3.5" />
              {{ store.uploading ? "Uploading…" : "Upload" }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ── Delete Confirm Modal ──────────────────────────────────────────── -->
    <Teleport to="body">
      <div
        v-if="deleteTarget"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        @click.self="deleteTarget = null"
      >
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
              <Trash2 class="w-5 h-5 text-red-500" />
            </div>
            <div>
              <p class="text-sm font-bold text-slate-800">Delete Document?</p>
              <p class="text-xs text-slate-400 mt-0.5">{{ deleteTarget.file_name }}</p>
            </div>
          </div>
          <p class="text-xs text-slate-500 mb-5">This will permanently remove the file and cannot be undone.</p>
          <div class="flex gap-3 justify-end">
            <button @click="deleteTarget = null" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
              Cancel
            </button>
            <button @click="handleDelete" class="px-4 py-2 text-xs font-bold text-white bg-red-500 rounded-lg hover:bg-red-600 transition">
              Delete
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import {
  Upload, Search, AlertCircle, Eye, Download, Trash2,
  FileArchive, X, Paperclip,
  FileImage, FlaskConical, ScanLine, FileText, Pill, File,
} from "lucide-vue-next";
import { useMedicalDocumentStore } from "../../stores/medicalDocumentStore";

const store  = useMedicalDocumentStore();
const search = ref("");
const typeFilter = ref("");
const showUploadModal = ref(false);
const deleteTarget    = ref(null);
const fileInput       = ref(null);

const uploadForm = ref({
  patient_id:    "",
  encounter_id:  "",
  document_type: "",
  description:   "",
  file:          null,
});

const docTypes = [
  { value: "lab_report",   label: "Lab Report" },
  { value: "xray",         label: "X-Ray" },
  { value: "mri",          label: "MRI" },
  { value: "ct_scan",      label: "CT Scan" },
  { value: "prescription", label: "Prescription" },
  { value: "other",        label: "Other" },
];

// ── Lifecycle ─────────────────────────────────────────────────────────────────

onMounted(() => store.fetchAll());

// ── Computed ──────────────────────────────────────────────────────────────────

const categoryKpis = computed(() =>
  docTypes.map((t) => ({
    ...t,
    count: store.documents.filter((d) => d.document_type === t.value).length,
  }))
);

const filtered = computed(() => {
  let list = store.documents;
  if (typeFilter.value) list = list.filter((d) => d.document_type === typeFilter.value);
  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter(
      (d) =>
        (d.file_name ?? "").toLowerCase().includes(q) ||
        (`${d.patient?.user?.first_name ?? ""} ${d.patient?.user?.last_name ?? ""}`).toLowerCase().includes(q)
    );
  }
  return list;
});

// ── Actions ───────────────────────────────────────────────────────────────────

function closeUpload() {
  showUploadModal.value = false;
  uploadForm.value = { patient_id: "", encounter_id: "", document_type: "", description: "", file: null };
  store.clearError();
}

function onFileChange(e) {
  uploadForm.value.file = e.target.files[0] ?? null;
}
function onDrop(e) {
  uploadForm.value.file = e.dataTransfer.files[0] ?? null;
}

async function handleUpload() {
  if (!uploadForm.value.patient_id || !uploadForm.value.document_type || !uploadForm.value.file) {
    store.error = "Patient ID, document type and file are required.";
    return;
  }
  store.clearError();
  try {
    await store.upload({
      patient_id:    uploadForm.value.patient_id,
      encounter_id:  uploadForm.value.encounter_id || undefined,
      document_type: uploadForm.value.document_type,
      description:   uploadForm.value.description || undefined,
      file:          uploadForm.value.file,
    });
    closeUpload();
  } catch {
    // error shown via store.error
  }
}

function confirmDelete(doc) {
  deleteTarget.value = doc;
}

async function handleDelete() {
  if (!deleteTarget.value) return;
  try {
    await store.destroy(deleteTarget.value.id);
    deleteTarget.value = null;
  } catch {
    deleteTarget.value = null;
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────

const BACKEND_URL = "http://127.0.0.1:8000/storage";

function storageUrl(filePath) {
  if (!filePath) return "#";
  if (filePath.startsWith("http")) return filePath;
  return `${BACKEND_URL}/${filePath}`;
}

function formatDate(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleDateString("en-ET", { day: "numeric", month: "short", year: "numeric" });
}

function formatSize(bytes) {
  if (!bytes) return "";
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / 1048576).toFixed(1)} MB`;
}

function docTypeLabel(type) {
  return docTypes.find((t) => t.value === type)?.label ?? type;
}

function typeIcon(type) {
  return {
    lab_report:   FlaskConical,
    xray:         ScanLine,
    mri:          ScanLine,
    ct_scan:      ScanLine,
    prescription: Pill,
    other:        File,
  }[type] ?? File;
}

function typeIconClass(type) {
  return {
    lab_report:   "bg-emerald-50 text-emerald-600",
    xray:         "bg-blue-50 text-blue-600",
    mri:          "bg-indigo-50 text-indigo-600",
    ct_scan:      "bg-violet-50 text-violet-600",
    prescription: "bg-amber-50 text-amber-600",
    other:        "bg-slate-100 text-slate-500",
  }[type] ?? "bg-slate-100 text-slate-500";
}

function typeBadgeClass(type) {
  return {
    lab_report:   "bg-emerald-50 text-emerald-700 border-emerald-100",
    xray:         "bg-blue-50 text-blue-700 border-blue-100",
    mri:          "bg-indigo-50 text-indigo-700 border-indigo-100",
    ct_scan:      "bg-violet-50 text-violet-700 border-violet-100",
    prescription: "bg-amber-50 text-amber-700 border-amber-100",
    other:        "bg-slate-50 text-slate-500 border-slate-200",
  }[type] ?? "bg-slate-50 text-slate-500 border-slate-200";
}
</script>
