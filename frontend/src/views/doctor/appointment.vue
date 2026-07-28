<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-6xl mx-auto">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Appointment Management</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">View, confirm, refer and manage patient appointments.</p>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div v-for="stat in stats" :key="stat.label" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
          <div class="flex items-center gap-3 mb-2"><div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="stat.bg"><component :is="stat.icon" class="w-4.5 h-4.5" :class="stat.color" /></div></div>
          <p class="text-2xl font-black text-gray-900">{{ stat.value }}</p>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">{{ stat.label }}</p>
        </div>
      </div>

      <!-- Tabs -->
      <div class="flex gap-1 bg-gray-100 rounded-xl p-1 mb-5 w-fit">
        <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-white text-[#004795] shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-1.5 text-xs font-bold rounded-lg transition">All Appointments</button>
        <button @click="activeTab = 'referrals'; loadIncoming()" :class="activeTab === 'referrals' ? 'bg-white text-[#004795] shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-1.5 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
          Incoming Referrals
          <span v-if="store.incomingReferrals.length" class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ store.incomingReferrals.length }}</span>
        </button>
      </div>

      <!-- Error -->
      <div v-if="store.error" class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"><AlertCircle class="w-4 h-4 flex-shrink-0" />{{ store.error }}</div>

      <!-- ── All Appointments tab ─────────────────────────────────────── -->
      <template v-if="activeTab === 'all'">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
          <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 border-b border-gray-100">
            <div class="relative flex-1"><Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" /><input v-model="search" type="text" placeholder="Search patient name..." class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#004795]" /></div>
            <select v-model="statusFilter" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-[#004795]">
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>

          <div v-if="store.loading && !store.appointments.length" class="p-5 space-y-3"><div v-for="n in 3" :key="n" class="h-14 bg-gray-50 rounded-lg animate-pulse" /></div>

          <div v-else class="overflow-x-auto" @click="openApptMenuId = null">
            <table class="w-full min-w-[600px] text-left text-xs">
              <thead>
                <tr class="border-b border-gray-100">
                  <th class="px-5 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Patient</th>
                  <th class="px-5 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Date & Time</th>
                  <th class="px-5 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Reason</th>
                  <th class="px-5 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Type</th>
                  <th class="px-5 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Files</th>
                  <th class="px-5 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                  <th class="px-5 py-2.5 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-if="!filtered.length"><td colspan="7" class="px-2 py-10 text-center text-gray-400"><CalendarDays class="w-7 h-7 mx-auto mb-2 text-gray-300" /><p class="text-xs font-medium">No appointments found</p></td></tr>
                <tr v-for="appt in filtered" :key="appt.id" class="hover:bg-gray-50/60 transition-colors">
                  <!-- Patient -->
                  <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                      <div class="w-8 h-8 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0"><span class="text-[10px] font-bold text-[#004795]">{{ patientInitials(appt) }}</span></div>
                      <div class="min-w-0"><p class="font-semibold text-gray-800 text-xs">{{ patientName(appt) }}</p><p class="text-[10px] text-gray-400 truncate">{{ appt.patient?.email ?? '—' }}</p></div>
                    </div>
                  </td>
                  <!-- Date -->
                  <td class="px-5 py-3"><p class="text-xs font-medium text-gray-700">{{ formatDate(appt.scheduled_time) }}</p><p class="text-[10px] text-gray-400">{{ formatTime(appt.scheduled_time) }}</p></td>
                  <!-- Reason -->
                  <td class="px-5 py-3 max-w-[150px]"><p class="text-xs text-gray-600 break-words">{{ appt.reason ?? '—' }}</p></td>
                  <!-- Type -->
                  <td class="px-5 py-3">
                    <span v-if="appt.is_telehealth" class="inline-flex items-center gap-1 text-[10px] font-medium text-blue-600 bg-blue-50 border border-blue-100 px-1.5 py-0.5 rounded"><Monitor class="w-2.5 h-2.5" /> Telemedicine</span>
                    <span v-else class="text-[10px] text-gray-500">In-Person</span>
                  </td>
                  <!-- Files -->
                  <td class="px-5 py-3">
                    <button v-if="appt.documents?.length" @click="openFiles(appt)" class="inline-flex items-center gap-1 text-[10px] font-semibold text-[#004795] bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full hover:bg-blue-100 transition">
                      <Paperclip class="w-2.5 h-2.5" /> {{ appt.documents.length }} file{{ appt.documents.length > 1 ? 's' : '' }}
                    </button>
                    <span v-else class="text-[10px] text-gray-300">—</span>
                  </td>
                  <!-- Status -->
                  <td class="px-5 py-3">
                    <span :class="statusClass(appt.status)" class="text-[10px] font-semibold px-2 py-0.5 rounded-full border capitalize">{{ appt.status }}</span>
                    <span v-if="appt.referrals?.length" class="ml-1 text-[9px] font-bold text-amber-600 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded-full">Referred</span>
                  </td>
                  <!-- Actions -->
                  <td class="px-5 py-3 text-right">
                    <div class="relative inline-block" @click.stop>
                      <button @click="toggleApptMenu(appt.id)" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"><MoreVertical class="w-4 h-4" /></button>
                      <Transition name="appt-dropdown">
                        <div v-if="openApptMenuId === appt.id" class="absolute right-0 top-full mt-1 w-44 bg-white rounded-xl shadow-lg border border-gray-100 z-30 py-1">
                          <button v-if="appt.status === 'pending'" @click="doConfirm(appt.id)" :disabled="store.loading" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-blue-600 hover:bg-blue-50 transition"><CheckCircle class="w-3.5 h-3.5" /> Confirm</button>
                          <button v-if="appt.status === 'confirmed'" @click="doComplete(appt.id)" :disabled="store.loading" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-emerald-600 hover:bg-emerald-50 transition"><CheckCircle class="w-3.5 h-3.5" /> Complete</button>
                          <button v-if="['pending','confirmed'].includes(appt.status)" @click="openReferModal(appt)" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-purple-600 hover:bg-purple-50 transition"><ArrowRightLeft class="w-3.5 h-3.5" /> Refer Patient</button>
                          <button v-if="['pending','confirmed'].includes(appt.status)" @click="doCancel(appt.id)" :disabled="store.loading" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 hover:bg-red-50 transition"><XCircle class="w-3.5 h-3.5" /> Cancel</button>
                        </div>
                      </Transition>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </template>

      <!-- ── Incoming Referrals tab ───────────────────────────────────── -->
      <template v-if="activeTab === 'referrals'">
        <div v-if="referralLoading" class="space-y-3"><div v-for="n in 3" :key="n" class="h-20 bg-white rounded-xl border border-gray-100 animate-pulse" /></div>
        <div v-else-if="!store.incomingReferrals.length" class="bg-white rounded-xl border border-gray-100 py-16 flex flex-col items-center text-gray-400">
          <ArrowRightLeft class="w-10 h-10 mb-3 text-gray-300" />
          <p class="text-sm font-medium">No incoming referrals</p>
        </div>
        <div v-else class="space-y-4">
          <div v-for="ref in store.incomingReferrals" :key="ref.id" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
              <div>
                <p class="text-sm font-bold text-gray-800">{{ patientNameFromAppt(ref.appointment) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ formatDate(ref.appointment?.scheduled_time) }} · {{ formatTime(ref.appointment?.scheduled_time) }}</p>
                <p class="text-xs text-gray-600 mt-1">Referred by Dr. {{ ref.referredBy?.user?.first_name }} {{ ref.referredBy?.user?.last_name }}</p>
                <p class="text-xs text-gray-500 mt-1 italic">"{{ ref.reason }}"</p>
                <!-- Files on the appointment -->
                <div v-if="ref.appointment?.documents?.length" class="flex flex-wrap gap-1.5 mt-2">
                  <button v-for="doc in ref.appointment.documents" :key="doc.id"
                    @click="downloadDoc(doc)"
                    :disabled="downloading[doc.id]"
                    class="inline-flex items-center gap-1 text-[10px] font-semibold text-[#004795] bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full hover:bg-blue-100 transition disabled:opacity-50">
                    <Loader2 v-if="downloading[doc.id]" class="w-2.5 h-2.5 animate-spin" />
                    <Paperclip v-else class="w-2.5 h-2.5" />{{ doc.file_name }}
                  </button>
                </div>
              </div>
              <div class="flex items-center gap-2 flex-shrink-0">
                <button @click="openReferralResponse(ref, 'accept')" :disabled="store.loading" class="px-4 py-2 text-xs font-bold text-white bg-emerald-500 hover:bg-emerald-600 rounded-lg transition disabled:opacity-50 flex items-center gap-1.5"><CheckCircle class="w-3.5 h-3.5" /> Accept</button>
                <button @click="openReferralResponse(ref, 'reject')" :disabled="store.loading" class="px-4 py-2 text-xs font-bold text-white bg-red-500 hover:bg-red-600 rounded-lg transition disabled:opacity-50 flex items-center gap-1.5"><XCircle class="w-3.5 h-3.5" /> Reject</button>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- Click-outside overlay -->
    <div v-if="openApptMenuId" class="fixed inset-0 z-20" @click="openApptMenuId = null" />

    <!-- ── Files Viewer Modal ──────────────────────────────────────────── -->
    <div v-if="showFiles" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" @click.self="showFiles = false">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0">
          <div><h3 class="text-sm font-bold text-gray-800">Patient Documents</h3><p class="text-xs text-gray-400 mt-0.5">Uploaded at booking by {{ patientName(viewingAppt) }}</p></div>
          <button @click="showFiles = false" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"><X class="w-4 h-4" /></button>
        </div>
        <div class="px-6 py-4 space-y-2 overflow-y-auto flex-1">
          <div v-for="doc in viewingAppt?.documents" :key="doc.id" class="flex items-center justify-between bg-gray-50 border border-gray-100 rounded-xl px-3 py-2.5">
            <div class="flex items-center gap-2 min-w-0">
              <FileText v-if="/pdf/i.test(doc.file_type)" class="w-4 h-4 text-red-500 flex-shrink-0" />
              <ImageIcon v-else class="w-4 h-4 text-blue-500 flex-shrink-0" />
              <div class="min-w-0"><p class="text-xs font-semibold text-gray-800 truncate">{{ doc.file_name }}</p><p class="text-[10px] text-gray-400">{{ formatFileSize(doc.file_size) }} · {{ doc.document_type?.replace(/_/g,' ') }}</p></div>
            </div>
            <button @click="downloadDoc(doc)" :disabled="downloading[doc.id]" class="ml-3 flex-shrink-0 px-3 py-1.5 text-xs font-semibold text-[#004795] border border-[#004795]/30 rounded-lg hover:bg-[#004795]/5 transition flex items-center gap-1 disabled:opacity-50">
              <Loader2 v-if="downloading[doc.id]" class="w-3 h-3 animate-spin" />
              <Download v-else class="w-3 h-3" /> Download
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Refer Patient Modal ─────────────────────────────────────────── -->
    <div v-if="showReferModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" @click.self="closeReferModal">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md flex flex-col">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0">
          <div><h3 class="text-sm font-bold text-gray-800">Refer Patient</h3><p class="text-xs text-gray-400 mt-0.5">Transfer this appointment to another doctor or department</p></div>
          <button @click="closeReferModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"><X class="w-4 h-4" /></button>
        </div>
        <div class="px-6 py-4 space-y-4">
          <div v-if="referError" class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"><AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ referError }}</div>
          <!-- Doctor selector -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Refer to Doctor <span class="text-gray-400 font-normal">(optional)</span></label>
            <select v-model="referForm.referred_to_doctor_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition">
              <option value="">— Select doctor —</option>
              <option v-for="doc in allDoctors" :key="doc.id" :value="doc.id">Dr. {{ doc.user?.first_name }} {{ doc.user?.last_name }} ({{ doc.department?.name }})</option>
            </select>
          </div>
          <!-- Department selector -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Or Refer to Department <span class="text-gray-400 font-normal">(optional)</span></label>
            <select v-model="referForm.referred_to_department_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition">
              <option value="">— Select department —</option>
              <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
            </select>
          </div>
          <!-- Reason -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Reason for Referral <span class="text-red-500">*</span></label>
            <textarea v-model="referForm.reason" rows="3" placeholder="Explain why you are referring this patient..." class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none" />
          </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0">
          <button @click="closeReferModal" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
          <button @click="submitRefer" :disabled="!referForm.reason.trim() || referSaving" class="px-5 py-2 text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition disabled:opacity-50 flex items-center gap-2">
            <Loader2 v-if="referSaving" class="w-3.5 h-3.5 animate-spin" /> Send Referral
          </button>
        </div>
      </div>
    </div>

    <!-- ── Reject Referral Modal ───────────────────────────────────────── -->
    <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" @click.self="showRejectModal = false">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm flex flex-col">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0">
          <h3 class="text-sm font-bold text-gray-800">Reject Referral</h3>
          <button @click="showRejectModal = false" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"><X class="w-4 h-4" /></button>
        </div>
        <div class="px-6 py-4 space-y-3">
          <div v-if="rejectError" class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"><AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ rejectError }}</div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Reason for rejection <span class="text-red-500">*</span></label>
            <textarea v-model="rejectReason" rows="3" placeholder="Explain why you cannot accept this referral..." class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none" />
          </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0">
          <button @click="showRejectModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
          <button @click="submitReject" :disabled="!rejectReason.trim() || store.loading" class="px-5 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition disabled:opacity-50 flex items-center gap-2">
            <Loader2 v-if="store.loading" class="w-3.5 h-3.5 animate-spin" /> Confirm Reject
          </button>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { AlertCircle, Search, CalendarDays, Clock, Monitor, CheckCircle, CalendarCheck, MoreVertical, XCircle, Paperclip, FileText, Image as ImageIcon, Download, ArrowRightLeft, X, Loader2 } from "lucide-vue-next";
import { useAppointmentStore } from "../../stores/appointmentStore";
import doctorApi from "../../api/doctorApi";
import departmentApi from "../../api/departmentApi";
import medicalDocumentApi from "../../api/medicalDocumentApi";
import api from "../../api/axios";

const store   = useAppointmentStore();

const search         = ref("");
const statusFilter   = ref("");
const openApptMenuId = ref(null);
const activeTab      = ref("all");
const referralLoading = ref(false);

onMounted(() => store.fetchAll());

function toggleApptMenu(id) { openApptMenuId.value = openApptMenuId.value === id ? null : id; }

const stats = computed(() => [
  { label:"Today's",  value:store.appointments.filter((a)=>isToday(a.scheduled_time)).length, icon:CalendarDays, bg:"bg-blue-50",    color:"text-blue-500"  },
  { label:"Upcoming", value:store.appointments.filter((a)=>["pending","confirmed"].includes(a.status)).length, icon:CalendarCheck, bg:"bg-amber-50",   color:"text-amber-500" },
  { label:"Completed",value:store.completed.length, icon:CheckCircle, bg:"bg-emerald-50", color:"text-emerald-500" },
  { label:"Cancelled",value:store.cancelled.length, icon:Clock,       bg:"bg-red-50",     color:"text-red-400"    },
]);

const filtered = computed(() => {
  let list = store.appointments;
  if (search.value.trim()) { const q = search.value.toLowerCase(); list = list.filter((a)=>patientName(a).toLowerCase().includes(q)); }
  if (statusFilter.value) list = list.filter((a)=>a.status===statusFilter.value);
  return list;
});

function patientName(appt)     { const p = appt.patient; return p ? `${p.first_name??""} ${p.last_name??""}`.trim() : "—"; }
function patientInitials(appt) { const p = appt.patient; return p ? ((p.first_name?.[0]??"")+(p.last_name?.[0]??"")).toUpperCase() : "?"; }
function patientNameFromAppt(appt) { const u = appt?.patient?.user ?? appt?.patient; return u ? `${u.first_name??""} ${u.last_name??""}`.trim() : "—"; }
function isToday(dt) { if (!dt) return false; return new Date(dt).toDateString()===new Date().toDateString(); }
function formatDate(dt) { return dt ? new Date(dt).toLocaleDateString("en-ET",{day:"numeric",month:"short",year:"numeric"}) : "—"; }
function formatTime(dt) { return dt ? new Date(dt).toLocaleTimeString("en-ET",{hour:"2-digit",minute:"2-digit"}) : "—"; }
function formatFileSize(bytes) { if (!bytes) return ""; if (bytes<1024) return `${bytes} B`; if (bytes<1024*1024) return `${(bytes/1024).toFixed(0)} KB`; return `${(bytes/1024/1024).toFixed(1)} MB`; }
function statusClass(status) {
  return {pending:"bg-amber-50 text-amber-700 border-amber-200",confirmed:"bg-blue-50 text-blue-700 border-blue-200",completed:"bg-emerald-50 text-emerald-700 border-emerald-200",cancelled:"bg-red-50 text-red-600 border-red-200",no_show:"bg-gray-50 text-gray-500 border-gray-200"}[status]??"bg-gray-50 text-gray-500 border-gray-200";
}

async function doConfirm(id)  { openApptMenuId.value=null; try { await store.updateStatus(id,"confirmed"); } catch {} }
async function doComplete(id) { openApptMenuId.value=null; try { await store.updateStatus(id,"completed"); } catch {} }
async function doCancel(id)   { openApptMenuId.value=null; try { await store.updateStatus(id,"cancelled"); } catch {} }

// ── File viewer ──────────────────────────────────────────────────────────
const showFiles    = ref(false);
const viewingAppt  = ref(null);
const downloading  = ref({});   // { [docId]: true } while downloading

function openFiles(appt) { viewingAppt.value = appt; showFiles.value = true; }

async function downloadDoc(doc) {
  if (downloading.value[doc.id]) return;
  downloading.value[doc.id] = true;
  try {
    await medicalDocumentApi.download(doc.id, doc.file_name);
  } catch (e) {
    alert("Download failed: " + (e.message || "Unknown error"));
  } finally {
    downloading.value[doc.id] = false;
  }
}

// ── Refer modal ──────────────────────────────────────────────────────────
const showReferModal = ref(false); const referringAppt = ref(null);
const referForm = ref({ referred_to_doctor_id:"", referred_to_department_id:"", reason:"" });
const referError = ref(null); const referSaving = ref(false);
const allDoctors = ref([]); const departments = ref([]);

async function openReferModal(appt) {
  openApptMenuId.value = null;
  referringAppt.value  = appt;
  referForm.value      = { referred_to_doctor_id: "", referred_to_department_id: "", reason: "" };
  referError.value     = null;
  allDoctors.value     = [];
  departments.value    = [];
  showReferModal.value = true;

  // The appointment already carries hospital_id; load doctors + departments
  // from the SAME hospital so the doctor can only refer within their own hospital.
  const hospitalId = appt.hospital_id;

  try {
    const [docRes, deptRes] = await Promise.all([
      doctorApi.getAll(hospitalId ? { hospital_id: hospitalId } : {}),
      departmentApi.getByHospital(hospitalId),
    ]);

    // Exclude the current doctor from the list
    const currentDoctorId = appt.doctor_id;
    allDoctors.value  = (docRes.data?.data ?? docRes.data ?? [])
      .filter(d => d.id !== currentDoctorId);

    departments.value = Array.isArray(deptRes.data?.data)
      ? deptRes.data.data
      : Array.isArray(deptRes.data) ? deptRes.data : [];
  } catch (err) {
    referError.value = "Failed to load doctors/departments. " + (err.response?.data?.message || "");
  }
}
function closeReferModal() { showReferModal.value = false; referError.value = null; }

async function submitRefer() {
  if (!referForm.value.reason.trim()) return;
  if (!referForm.value.referred_to_doctor_id && !referForm.value.referred_to_department_id) {
    referError.value = "Please select a doctor or department to refer to."; return;
  }
  referError.value = null; referSaving.value = true;
  try {
    await store.refer(referringAppt.value.id, {
      referred_to_doctor_id:     referForm.value.referred_to_doctor_id || null,
      referred_to_department_id: referForm.value.referred_to_department_id || null,
      reason: referForm.value.reason,
    });
    closeReferModal();
  } catch (err) {
    const errors = err.response?.data?.errors;
    referError.value = errors ? Object.values(errors).flat().join(" ") : err.response?.data?.message || "Failed to refer.";
  } finally { referSaving.value = false; }
}

// ── Incoming referrals ───────────────────────────────────────────────────
async function loadIncoming() {
  referralLoading.value = true;
  try { await store.fetchIncomingReferrals(); }
  finally { referralLoading.value = false; }
}

// Reject modal
const showRejectModal = ref(false); const respondingReferral = ref(null);
const rejectReason = ref(""); const rejectError = ref(null);

function openReferralResponse(ref_, action) {
  respondingReferral.value = ref_;
  if (action === 'accept') {
    handleAccept(ref_.id);
  } else {
    rejectReason.value = ""; rejectError.value = null; showRejectModal.value = true;
  }
}
async function handleAccept(id) {
  try { await store.respondReferral(id, "accept"); }
  catch (err) { store.error = err.response?.data?.message || "Failed to accept."; }
}
async function submitReject() {
  if (!rejectReason.value.trim()) return;
  rejectError.value = null;
  try { await store.respondReferral(respondingReferral.value.id, "reject", rejectReason.value); showRejectModal.value = false; }
  catch (err) { rejectError.value = err.response?.data?.message || "Failed to reject."; }
}
</script>

<style scoped>
.appt-dropdown-enter-active,.appt-dropdown-leave-active { transition:opacity 0.12s,transform 0.12s; }
.appt-dropdown-enter-from,.appt-dropdown-leave-to { opacity:0; transform:translateY(-4px); }
</style>
