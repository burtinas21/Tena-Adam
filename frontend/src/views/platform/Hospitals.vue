<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Hospitals</h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Register and manage hospitals on the platform.
        </p>
      </div> 
      
      <button
        @click="openCreate"
        class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-x-2 transition shadow-sm"
      >
        <Plus class="w-3.5 h-3.5" />
        Register Hospital
      </button>
    </div>

    <!-- Error -->
    <div
      v-if="store.error && !showForm"
      class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
    >
      <AlertCircle class="w-4 h-4 flex-shrink-0" />
      {{ store.error }}
    </div>

    <!-- Loading -->
    <div v-if="store.loading && store.hospitals.length === 0" class="space-y-3">
      <div v-for="n in 4" :key="n" class="h-14 bg-white rounded-xl border border-gray-100 animate-pulse" />
    </div>

    <!-- Table -->
    <HospitalTable
      v-else
      :hospitals="store.hospitals"
      @edit="openEdit"
      @delete="confirmDelete"
    />

    <!-- Create / Edit modal -->
    <HospitalForm
      v-if="showForm"
      :hospital="selectedHospital"
      :loading="store.loading"
      :error="formError"
      @close="closeForm"
      @submit="handleFormSubmit"
    />

    <!-- Delete confirmation -->
    <div
      v-if="showDeleteConfirm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <Trash2 class="w-5 h-5 text-red-500" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-800">Delete Hospital</h3>
            <p class="text-xs text-gray-400 mt-0.5">This will remove the hospital and all its data.</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-5">
          Delete <span class="font-semibold text-gray-800">{{ hospitalToDelete?.name }}</span>?
        </p>
        <div class="flex items-center justify-end gap-3">
          <button @click="showDeleteConfirm = false" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Cancel
          </button>
          <button
            @click="handleDelete"
            :disabled="store.loading"
            class="px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition disabled:opacity-60 flex items-center gap-2"
          >
            <Loader2 v-if="store.loading" class="w-3.5 h-3.5 animate-spin" />
            Delete
          </button>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Plus, AlertCircle, Trash2, Loader2 } from "lucide-vue-next";
import { useHospitalStore } from "../../stores/hospitalStore";
import HospitalTable from "../../components/hospital/HospitalTable.vue";
import HospitalForm from "../../components/hospital/HospitalForm.vue";
import { useToast } from "../../composables/useToast";

const { showToast } = useToast();

const store = useHospitalStore();
const showForm = ref(false);
const selectedHospital = ref(null);
const formError = ref(null);
const showDeleteConfirm = ref(false);
const hospitalToDelete = ref(null);

onMounted(() => store.fetchAll());

function openCreate() {
  selectedHospital.value = null;
  formError.value = null;
  showForm.value = true;
}

function openEdit(hospital) {
  selectedHospital.value = hospital;
  formError.value = null;
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
  selectedHospital.value = null;
  formError.value = null;
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
  hospitalToDelete.value = hospital;
  showDeleteConfirm.value = true;
}

async function handleDelete() {
  if (!hospitalToDelete.value) return;
  const name = hospitalToDelete.value.name;
  try {
    await store.destroy(hospitalToDelete.value.id);
    showDeleteConfirm.value = false;
    hospitalToDelete.value = null;
    showToast(`"${name}" deleted successfully`, "success");
  } catch (err) {
    const msg = err.response?.data?.message || "Failed to delete hospital.";
    showToast(msg, "error");
  }
}
</script>
