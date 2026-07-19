<template>
  <main class="flex-1 bg-[#F8FAFC] p-4 sm:p-6 lg:p-8 overflow-y-auto font-sans">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">My Medical Documents</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">
            Lab reports, imaging, and clinical files from your consultations
          </p>
        </div>
      </div>

      <!-- KPI strip -->
      <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
        <div
          v-for="cat in categoryKpis"
          :key="cat.type"
          class="bg-white border rounded-xl p-3 text-center shadow-sm"
          :class="cat.count > 0 ? 'border-slate-200' : 'border-slate-100'"
        >
          <p class="text-lg font-black" :class="cat.count > 0 ? 'text-slate-900' : 'text-slate-300'">
            {{ cat.count }}
          </p>
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
            placeholder="Search file name or description..."
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
        <div v-for="n in 5" :key="n" class="h-16 bg-white rounded-xl animate-pulse border border-slate-100" />
      </div>

      <!-- Empty state -->
      <div
        v-else-if="!store.loading && !filtered.length"
        class="bg-white border border-slate-200 rounded-xl p-12 text-center"
      >
        <FolderOpen class="w-10 h-10 mx-auto mb-3 text-slate-200" />
        <p class="text-sm font-semibold text-slate-400">No documents found</p>
        <p class="text-xs text-slate-300 mt-1">Documents uploaded by your doctor will appear here</p>
      </div>

      <!-- Document list -->
      <div v-else class="bg-white border border-slate-200 rounded-xl shadow-sm divide-y divide-slate-100">
        <div
          v-for="doc in filtered"
          :key="doc.id"
          class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50/50 transition"
        >
          <!-- Type icon -->
          <div
            class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
            :class="typeIconBg(doc.document_type)"
          >
            <component :is="typeIcon(doc.document_type)" class="w-5 h-5" />
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <p class="text-sm font-bold text-slate-800 truncate">{{ doc.file_name }}</p>
              <span
                :class="typeBadge(doc.document_type)"
                class="text-[9px] font-bold px-1.5 py-0.5 rounded border uppercase tracking-wider"
              >
                {{ docTypeLabel(doc.document_type) }}
              </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">
              <span v-if="doc.encounter?.encounter_date">
                {{ formatDate(doc.encounter.encounter_date) }}
              </span>
              <span v-if="doc.uploader">
                · Uploaded by {{ uploaderName(doc.uploader) }}
              </span>
              <span v-if="doc.file_size"> · {{ formatSize(doc.file_size) }}</span>
            </p>
            <p v-if="doc.description" class="text-[11px] text-slate-500 italic mt-0.5">
              {{ doc.description }}
            </p>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2 flex-shrink-0">
            <a
              :href="storageUrl(doc.file_url)"
              target="_blank"
              rel="noopener"
              class="flex items-center gap-1.5 text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition"
            >
              <Eye class="w-3.5 h-3.5" />
              View
            </a>
            <a
              :href="store.downloadUrl(doc.id)"
              class="flex items-center gap-1.5 text-[10px] font-bold text-slate-600 bg-white border border-slate-200 px-3 py-1.5 rounded-lg hover:bg-slate-50 transition"
            >
              <Download class="w-3.5 h-3.5" />
              Download
            </a>
          </div>
        </div>
      </div>

    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import {
  AlertCircle, Search, Eye, Download, FolderOpen,
  FlaskConical, ScanLine, Pill, File, FileText,
} from "lucide-vue-next";
import { useMedicalDocumentStore } from "../../stores/medicalDocumentStore";

const store      = useMedicalDocumentStore();
const search     = ref("");
const typeFilter = ref("");

const docTypes = [
  { value: "lab_report",   label: "Lab Report"   },
  { value: "xray",         label: "X-Ray"        },
  { value: "mri",          label: "MRI"          },
  { value: "ct_scan",      label: "CT Scan"      },
  { value: "prescription", label: "Prescription" },
  { value: "other",        label: "Other"        },
];

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
        (d.description ?? "").toLowerCase().includes(q)
    );
  }
  return list;
});

// ── Helpers ───────────────────────────────────────────────────────────────────

const BACKEND_URL = "http://127.0.0.1:8000/storage";
function storageUrl(filePath) {
  if (!filePath) return "#";
  if (filePath.startsWith("http")) return filePath;
  return `${BACKEND_URL}/${filePath}`;
}

function formatDate(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleDateString("en-ET", {
    day: "numeric", month: "short", year: "numeric",
  });
}

function formatSize(bytes) {
  if (!bytes) return "";
  if (bytes < 1024)    return `${bytes} B`;
  if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / 1048576).toFixed(1)} MB`;
}

function uploaderName(user) {
  if (!user) return "Doctor";
  const name = `${user.first_name ?? ""} ${user.last_name ?? ""}`.trim();
  return name || "Doctor";
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

function typeIconBg(type) {
  return {
    lab_report:   "bg-emerald-50 text-emerald-600",
    xray:         "bg-blue-50 text-blue-600",
    mri:          "bg-indigo-50 text-indigo-600",
    ct_scan:      "bg-violet-50 text-violet-600",
    prescription: "bg-amber-50 text-amber-600",
    other:        "bg-slate-100 text-slate-500",
  }[type] ?? "bg-slate-100 text-slate-500";
}

function typeBadge(type) {
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
