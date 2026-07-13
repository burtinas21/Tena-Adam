<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-7xl mx-auto">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-xl font-bold text-gray-900 tracking-tight">My Schedule</h1>
          <p class="text-xs text-gray-400 font-medium mt-0.5">
            Manage your weekly availability and working hours
          </p>
        </div>
      </div>

      <!-- Error banner -->
      <div v-if="error"
        class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
        <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ error }}
      </div>

      <!-- KPIs -->
      <ScheduleKPIs
        :schedules="schedules"
        :telehealth-enabled="telehealthEnabled"
      />

      <!-- Main grid -->
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
        <!-- Calendar (3/4) -->
        <div class="lg:col-span-3 space-y-6">
          <div v-if="loading" class="space-y-2">
            <div v-for="n in 4" :key="n" class="h-14 bg-white rounded-xl border border-gray-100 animate-pulse" />
          </div>
          <CalendarGrid
            v-else
            :schedules="schedules"
            @add="openAdd"
            @edit="openEdit"
            @delete="confirmDelete"
          />

          <!-- ── Leave Requests section ──────────────────────────────── -->
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h3 class="text-sm font-bold text-gray-800">My Leave Requests</h3>
                <p class="text-xs text-gray-400 mt-0.5">Request and track your time off</p>
              </div>
              <button @click="openLeaveForm"
                class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2 px-3 rounded-lg flex items-center gap-1.5 transition">
                <Plus class="w-3.5 h-3.5" /> Request Leave
              </button>
            </div>

            <!-- Leave loading -->
            <div v-if="leaveLoading" class="space-y-2">
              <div v-for="n in 2" :key="n" class="h-12 bg-gray-100 rounded-xl animate-pulse" />
            </div>

            <!-- Empty -->
            <div v-else-if="!myLeaves.length" class="py-8 text-center text-gray-400">
              <CalendarOff class="w-7 h-7 mx-auto mb-2 text-gray-300" />
              <p class="text-sm font-medium">No leave requests yet</p>
            </div>

            <!-- Leave list -->
            <div v-else class="divide-y divide-gray-50">
              <div v-for="leave in myLeaves" :key="leave.id"
                class="py-3 flex items-center justify-between gap-4">
                <div>
                  <p class="text-sm font-semibold text-gray-800">{{ leave.leave_date }}</p>
                  <p class="text-xs text-gray-400 mt-0.5 capitalize">
                    {{ leave.leave_type }}{{ leave.reason ? ' · ' + leave.reason : '' }}
                  </p>
                </div>
                <div class="flex items-center gap-2">
                  <span :class="leaveStatusClass(leave.status)"
                    class="text-xs font-semibold px-2.5 py-0.5 rounded-full border capitalize">
                    {{ leave.status }}
                  </span>
                  <button v-if="leave.status === 'pending'" @click="handleDeleteLeave(leave.id)"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Cancel">
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Settings sidebar (1/4) -->
        <div class="lg:col-span-1">
          <ScheduleSettings
            :schedules="schedules"
            :loading="loading"
            :error="null"
            @add="openAdd"
          />
        </div>
      </div>
    </div>

    <!-- ── Leave Request modal ────────────────────────────────────────── -->
    <div v-if="showLeaveForm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="closeLeaveForm">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
          <div>
            <h3 class="text-sm font-bold text-gray-800">Request Leave</h3>
            <p class="text-xs text-gray-400 mt-0.5">Submit a leave request for approval</p>
          </div>
          <button @click="closeLeaveForm" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
            <X class="w-4 h-4" />
          </button>
        </div>
        <form @submit.prevent="handleLeaveSubmit" class="px-6 py-4 space-y-4">
          <div v-if="leaveFormError"
            class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ leaveFormError }}
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Leave Date <span class="text-red-500">*</span>
            </label>
            <input v-model="leaveForm.leave_date" type="date" required
              :min="today"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Leave Type <span class="text-red-500">*</span>
            </label>
            <select v-model="leaveForm.leave_type" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition">
              <option value="" disabled>Select type</option>
              <option value="vacation">Vacation</option>
              <option value="sick">Sick Leave</option>
              <option value="training">Training</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Reason</label>
            <textarea v-model="leaveForm.reason" rows="3" placeholder="Optional reason..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none" />
          </div>
          <div class="flex items-center justify-end gap-3 pt-1">
            <button type="button" @click="closeLeaveForm"
              class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
              Cancel
            </button>
            <button type="submit" :disabled="leaveSaving"
              class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2">
              <Loader2 v-if="leaveSaving" class="w-3.5 h-3.5 animate-spin" />
              Submit Request
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ── Schedule form modal ─────────────────────────────────────────── -->
    <ScheduleFormModal
      v-if="showForm"
      :schedule="editingSchedule"
      :used-days="usedDays"
      :loading="saving"
      :error="formError"
      @close="closeForm"
      @submit="handleSubmit"
    />

    <!-- ── Delete confirm ──────────────────────────────────────────────── -->
    <div v-if="showDeleteConfirm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <Trash2 class="w-5 h-5 text-red-500" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-800">Delete Schedule</h3>
            <p class="text-xs text-gray-400 mt-0.5">This day will be removed from your schedule.</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-5">
          Remove <span class="font-semibold">{{ DAY_LABELS[scheduleToDelete?.day_of_week] }}</span>?
        </p>
        <div class="flex items-center justify-end gap-3">
          <button @click="showDeleteConfirm = false"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Cancel
          </button>
          <button @click="handleDelete" :disabled="saving"
            class="px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="saving" class="w-3.5 h-3.5 animate-spin" />
            Delete
          </button>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { AlertCircle, Trash2, Loader2, Plus, X, CalendarOff } from "lucide-vue-next";
import scheduleApi from "../../api/scheduleApi";
import leaveApi    from "../../api/leaveApi";
import doctorApi   from "../../api/doctorApi";
import { useAuthStore } from "../../stores/authStore";
import ScheduleKPIs     from "../../components/doctor/schedule/ScheduleKPIs.vue";
import CalendarGrid     from "../../components/doctor/schedule/CalendarGrid.vue";
import ScheduleSettings from "../../components/doctor/schedule/ScheduleSettings.vue";
import ScheduleFormModal from "../../components/hospital-admin/doctor/ScheduleControls.vue";

const DAY_LABELS = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

const authStore = useAuthStore();
const doctorId  = computed(() => authStore.user?.id ?? "");

const schedules          = ref([]);
const telehealthEnabled  = ref(false);
const loading            = ref(false);
const saving             = ref(false);
const error              = ref(null);

// Form
const showForm       = ref(false);
const editingSchedule = ref(null);
const formError      = ref(null);

// Delete
const showDeleteConfirm = ref(false);
const scheduleToDelete  = ref(null);

const usedDays = computed(() => schedules.value.map((s) => s.day_of_week));

// ── Load ─────────────────────────────────────────────────────────────────
async function load() {
  try {
    loading.value = true;
    error.value   = null;
    const [schedRes, profileRes] = await Promise.all([
      doctorApi.getMySchedules(),
      doctorApi.getMe(),
    ]);
    schedules.value         = schedRes.data?.data ?? schedRes.data ?? [];
    telehealthEnabled.value = profileRes.data?.is_telehealth_available ?? false;
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to load schedule.";
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  await load();
  await loadLeaves();
});

// ── Form ──────────────────────────────────────────────────────────────────
function openAdd() {
  editingSchedule.value = null;
  formError.value       = null;
  showForm.value        = true;
}

function openEdit(schedule) {
  editingSchedule.value = schedule;
  formError.value       = null;
  showForm.value        = true;
}

function closeForm() {
  showForm.value        = false;
  editingSchedule.value = null;
  formError.value       = null;
}

async function handleSubmit(payload) {
  formError.value = null;
  try {
    saving.value = true;
    if (editingSchedule.value) {
      const res = await scheduleApi.update(editingSchedule.value.id, payload);
      const updated = res.data?.data ?? res.data;
      const idx = schedules.value.findIndex((s) => s.id === updated.id);
      if (idx !== -1) schedules.value[idx] = updated;
    } else {
      const res = await scheduleApi.create(payload);
      const created = res.data?.data ?? res.data;
      schedules.value.push(created);
    }
    closeForm();
  } catch (err) {
    const errors = err.response?.data?.errors;
    formError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
  } finally {
    saving.value = false;
  }
}

// ── Delete ────────────────────────────────────────────────────────────────
function confirmDelete(schedule) {
  scheduleToDelete.value  = schedule;
  showDeleteConfirm.value = true;
}

async function handleDelete() {
  try {
    saving.value = true;
    await scheduleApi.destroy(scheduleToDelete.value.id);
    schedules.value     = schedules.value.filter((s) => s.id !== scheduleToDelete.value.id);
    showDeleteConfirm.value = false;
    scheduleToDelete.value  = null;
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to delete schedule.";
  } finally {
    saving.value = false;
  }
}

// ── Leaves ────────────────────────────────────────────────────────────────

const myLeaves       = ref([]);
const leaveLoading   = ref(false);
const showLeaveForm  = ref(false);
const leaveSaving    = ref(false);
const leaveFormError = ref(null);
const today          = new Date().toISOString().split("T")[0];

const leaveForm = ref({ leave_date: "", leave_type: "", reason: "" });

function leaveStatusClass(status) {
  return {
    pending:  "bg-amber-50 text-amber-700 border-amber-200",
    approved: "bg-emerald-50 text-emerald-700 border-emerald-200",
    rejected: "bg-red-50 text-red-600 border-red-200",
  }[status] ?? "bg-gray-50 text-gray-500 border-gray-200";
}

async function loadLeaves() {
  try {
    leaveLoading.value = true;
    const res = await leaveApi.getAll();
    const all = res.data?.data ?? res.data ?? [];
    // Filter to own leaves (backend already scopes by doctor role)
    myLeaves.value = all.filter((l) => l.doctor_id === doctorId.value || all.length > 0
      ? all  // backend already scopes
      : []);
    myLeaves.value = all;
  } catch { /* silent */ }
  finally { leaveLoading.value = false; }
}

function openLeaveForm() {
  leaveForm.value   = { leave_date: "", leave_type: "", reason: "" };
  leaveFormError.value = null;
  showLeaveForm.value  = true;
}

function closeLeaveForm() {
  showLeaveForm.value  = false;
  leaveFormError.value = null;
}

async function handleLeaveSubmit() {
  leaveFormError.value = null;
  try {
    leaveSaving.value = true;
    const res = await leaveApi.create({
      leave_date: leaveForm.value.leave_date,
      leave_type: leaveForm.value.leave_type,
      reason:     leaveForm.value.reason || null,
    });
    const created = res.data?.data ?? res.data;
    myLeaves.value.unshift(created);
    closeLeaveForm();
  } catch (err) {
    const errors = err.response?.data?.errors;
    leaveFormError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
  } finally {
    leaveSaving.value = false;
  }
}

async function handleDeleteLeave(id) {
  try {
    await leaveApi.destroy(id);
    myLeaves.value = myLeaves.value.filter((l) => l.id !== id);
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to cancel leave.";
  }
}
</script>
