<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-3 sm:p-5 overflow-y-auto font-sans dark:text-slate-200">
    <!-- Page header -->
    <div
      class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-4 mb-6"
    >
      <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
          Facilities
        </h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Manage hospital rooms, beds, labs, and other facilities.
        </p>
      </div>
      <button
        @click="openCreate"
        class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-x-2 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <Plus class="w-3.5 h-3.5" />
        New Facility
      </button>
    </div>

    <!-- Error banner -->
    <div
      v-if="(store.error && !showForm) || hospitalError"
      class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
    >
      <AlertCircle class="w-4 h-4 flex-shrink-0" />
      {{ hospitalError || store.error }}
    </div>

    <!-- Loading skeleton -->
    <div
      v-if="store.loading && store.facilities.length === 0"
      class="space-y-3"
    >
      <div
        v-for="n in 4"
        :key="n"
        class="h-14 bg-white rounded-xl border border-gray-100 animate-pulse"
      />
    </div>

    <!-- Table -->
    <FacilityTable
      v-else
      :facilities="store.facilities"
      @edit="openEdit"
      @delete="confirmDelete"
    />

    <!-- Create / Edit modal -->
    <FacilityForm
      v-if="showForm"
      :facility="selectedFacility"
      :hospital-id="hospitalId"
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
          <div
            class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0"
          >
            <Trash2 class="w-5 h-5 text-red-500" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-800">Delete Facility</h3>
            <p class="text-xs text-gray-400 mt-0.5">
              This action cannot be undone.
            </p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-5">
          Delete
          <span class="font-semibold text-gray-800">{{
            facilityToDelete?.name
          }}</span
          >?
        </p>
        <div class="flex items-center justify-end gap-3">
          <button
            @click="showDeleteConfirm = false"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
          >
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
import { useFacilityStore } from "../../stores/facilityStore";
import hospitalApi from "../../api/hospitalApi";
import FacilityTable from "../../components/hospital/FacilityTable.vue";
import FacilityForm from "../../components/hospital/FacilityForm.vue";

const store = useFacilityStore();
const hospitalId = ref("");
const hospitalError = ref(null);
const showForm = ref(false);
const selectedFacility = ref(null);
const formError = ref(null);
const showDeleteConfirm = ref(false);
const facilityToDelete = ref(null);

onMounted(async () => {
  try {
    const [, hospitalRes] = await Promise.all([
      store.fetchAll(),
      hospitalApi.getAll(),
    ]);
    const hospitals = hospitalRes.data?.data ?? hospitalRes.data ?? [];
    if (hospitals.length > 0) {
      hospitalId.value = hospitals[0].id;
    } else {
      hospitalError.value = "No hospital found for your account.";
    }
  } catch {
    hospitalError.value = "Failed to load hospital information.";
  }
});

function openCreate() {
  selectedFacility.value = null;
  formError.value = null;
  showForm.value = true;
}

function openEdit(facility) {
  selectedFacility.value = facility;
  formError.value = null;
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
  selectedFacility.value = null;
  formError.value = null;
}

async function handleFormSubmit(payload) {
  formError.value = null;
  try {
    if (selectedFacility.value) {
      await store.update(selectedFacility.value.id, payload);
    } else {
      await store.create(payload);
    }
    closeForm();
  } catch (err) {
    const errors = err.response?.data?.errors;
    if (errors) {
      formError.value = Object.values(errors).flat().join(" ");
    } else {
      formError.value = err.response?.data?.message || "Something went wrong.";
    }
  }
}

function confirmDelete(facility) {
  facilityToDelete.value = facility;
  showDeleteConfirm.value = true;
}

async function handleDelete() {
  if (!facilityToDelete.value) return;
  try {
    await store.destroy(facilityToDelete.value.id);
    showDeleteConfirm.value = false;
    facilityToDelete.value = null;
  } catch {
    // store.error shows it
  }
}
</script>
