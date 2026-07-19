<template>
  <main class="flex-1 bg-[#F8FAFC] p-4 sm:p-6 lg:p-8 overflow-y-auto font-sans">
    <div class="max-w-[1440px] mx-auto space-y-6">

      <!-- Page header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Medical Encounters</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">
            Electronic Medical Records — consultations, vitals, and prescriptions.
          </p>
        </div>
      </div>

      <!-- Global error -->
      <div
        v-if="encounterStore.error"
        class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" />
        {{ encounterStore.error }}
      </div>

      <!-- Loading skeleton -->
      <div v-if="encounterStore.loading && !encounterStore.encounters.length" class="space-y-4">
        <div v-for="n in 3" :key="n" class="h-16 bg-white rounded-xl animate-pulse border border-slate-100" />
      </div>

      <!-- Main layout: list + detail -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- ── Left: Active consultations ───────────────────── -->
        <div class="lg:col-span-4 space-y-3">
          <!-- Header label -->
          <div class="flex items-center justify-between">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Active Consultations</p>
            <span class="text-[10px] bg-blue-50 text-blue-700 border border-blue-100 font-bold px-2 py-0.5 rounded-full">
              {{ filteredEncounters.length }} in progress
            </span>
          </div>
          <!-- Search bar -->
          <div class="relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
            <input
              v-model="search"
              type="text"
              placeholder="Search by patient name..."
              class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#004795]"
            />
          </div>

          <!-- Empty state -->
          <div
            v-if="!encounterStore.loading && !filteredEncounters.length"
            class="bg-white border border-slate-200 rounded-xl p-8 text-center"
          >
            <ClipboardList class="w-8 h-8 mx-auto mb-2 text-slate-200" />
            <p class="text-xs text-slate-400 font-medium">No active consultations</p>
            <p class="text-[10px] text-slate-300 mt-1">Patients appear here when their queue is in consultation</p>
          </div>

          <!-- Encounter cards -->
          <div
            v-for="enc in filteredEncounters"
            :key="enc.id"
            @click="selectEncounter(enc)"
            class="bg-white border rounded-xl p-4 cursor-pointer transition hover:shadow-md"
            :class="selectedEncounter?.id === enc.id
              ? 'border-[#004795] ring-1 ring-[#004795]/20'
              : 'border-slate-200 hover:border-slate-300'"
          >
            <div class="flex items-start justify-between gap-2">
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-800 truncate">
                  {{ patientName(enc) }}
                </p>
                <p class="text-[10px] text-slate-400 mt-0.5">
                  {{ formatDate(enc.encounter_date) }}
                </p>
                <p v-if="enc.diagnosis" class="text-xs text-slate-600 mt-1 truncate">
                  {{ enc.diagnosis }}
                </p>
                <p v-else-if="enc.chief_complaint" class="text-xs text-slate-400 mt-1 truncate italic">
                  {{ enc.chief_complaint }}
                </p>
              </div>
              <span
                :class="statusClass(enc.status)"
                class="flex-shrink-0 text-[9px] font-bold px-1.5 py-0.5 rounded border capitalize"
              >
                {{ enc.status?.replace("_", " ") }}
              </span>
            </div>
          </div>
        </div>

        <!-- ── Right: Detail panel ────────────────────────────── -->
        <div class="lg:col-span-8 space-y-4">

          <!-- Patient header -->
          <PatientHeaderCard
            v-if="selectedEncounter"
            :name="patientName(selectedEncounter)"
            :patient-id="selectedEncounter.patient?.id?.slice(-8)?.toUpperCase() ?? ''"
            :age="calcAge(selectedEncounter.patient?.date_of_birth)"
            :dob="formatDate(selectedEncounter.patient?.date_of_birth)"
            :gender="selectedEncounter.patient?.gender ?? ''"
            :blood-type="selectedEncounter.patient?.blood_type ?? ''"
            :allergies="selectedEncounter.patient?.allergies ?? ''"
            :phone="selectedEncounter.patient?.phone ?? ''"
            :contact-name="emergencyContact?.contact_name ?? ''"
            :contact-relation="emergencyContact?.relationship ?? ''"
            :contact-phone="emergencyContact?.phone ?? ''"
            :status="selectedEncounter.status"
          />

          <!-- Patient Medical Profile Edit (doctor fills blood_type / allergies / medical_history) -->
          <div v-if="selectedEncounter && selectedEncounter.status === 'in_progress'" class="bg-white border border-slate-200 rounded-xl shadow-sm">
            <button
              @click="showMedicalProfileForm = !showMedicalProfileForm"
              class="w-full flex items-center justify-between px-6 py-3.5 text-left hover:bg-slate-50/60 transition rounded-xl"
            >
              <div class="flex items-center gap-2">
                <UserCog class="w-4 h-4 text-cyan-600" />
                <span class="text-sm font-bold text-slate-800">Patient Medical Profile</span>
                <span class="text-[10px] text-slate-400 font-normal ml-1">
                  — blood type, allergies, medical history
                </span>
              </div>
              <span class="text-[10px] font-semibold text-cyan-600">
                {{ showMedicalProfileForm ? "Close" : "Edit" }}
              </span>
            </button>

            <div v-if="showMedicalProfileForm" class="px-6 pb-5 pt-1 border-t border-slate-100 space-y-4">
              <div v-if="medProfileError" class="flex items-center gap-2 text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ medProfileError }}
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Blood Type -->
                <div class="space-y-1.5">
                  <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Blood Type</label>
                  <select
                    v-model="medProfileForm.blood_type"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:border-cyan-400"
                  >
                    <option value="">Unknown / not set</option>
                    <option v-for="bt in BLOOD_TYPES" :key="bt" :value="bt">{{ bt }}</option>
                  </select>
                </div>

                <!-- Allergies -->
                <div class="space-y-1.5">
                  <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Allergies</label>
                  <input
                    v-model="medProfileForm.allergies"
                    type="text"
                    placeholder="e.g. Penicillin, Aspirin, Peanuts"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400"
                  />
                </div>
              </div>

              <!-- Medical History -->
              <div class="space-y-1.5">
                <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Past Medical History</label>
                <textarea
                  v-model="medProfileForm.medical_history"
                  rows="4"
                  placeholder="Chronic conditions, previous surgeries, family history, current medications..."
                  class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-400 resize-none"
                ></textarea>
              </div>

              <div class="flex items-center gap-3 pt-1">
                <button
                  @click="saveMedicalProfile"
                  :disabled="medProfileSaving"
                  class="flex items-center gap-2 px-5 py-2 bg-cyan-600 text-white text-xs font-bold rounded-lg hover:bg-cyan-700 transition disabled:opacity-50"
                >
                  <Save class="w-3.5 h-3.5" />
                  {{ medProfileSaving ? "Saving…" : "Save Profile" }}
                </button>
                <button
                  @click="showMedicalProfileForm = false"
                  class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition"
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>

          <!-- No selection placeholder -->
          <div
            v-if="!selectedEncounter"
            class="bg-white border border-slate-200 rounded-xl p-12 text-center"
          >
            <FileEdit class="w-10 h-10 mx-auto mb-3 text-slate-200" />
            <p class="text-sm font-semibold text-slate-400">Select an encounter from the list</p>
          </div>

          <!-- Vitals section -->
          <MedicalVitalsGrid
            v-if="selectedEncounter"
            :vital="currentVital"
            :encounter-id="selectedEncounter.id"
            :patient-id="selectedEncounter.patient?.id"
            :can-edit="selectedEncounter.status === 'in_progress'"
            @saved="onVitalSaved"
          />

          <!-- History timeline + encounter form -->
          <div v-if="selectedEncounter" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <!-- Patient history -->
            <div class="lg:col-span-4">
              <EncounterTimeline
                :encounters="pastEncounters"
                :patient="selectedEncounter.patient"
                :selected-id="selectedEncounter.id"
                @select="selectEncounter"
              />
            </div>
            <!-- Encounter form -->
            <div class="lg:col-span-8">
              <EncounterForm
                :encounter="selectedEncounter"
                @saved="onEncounterSaved"
                @completed="onEncounterCompleted"
              />
            </div>
          </div>

          <!-- Prescriptions section -->
          <div v-if="selectedEncounter" class="bg-white border border-slate-200 rounded-xl shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
              <div class="flex items-center gap-2">
                <Pill class="w-4 h-4 text-violet-600" />
                <h3 class="text-sm font-bold text-slate-900">Prescriptions</h3>
                <span class="text-[10px] text-slate-400">
                  ({{ prescriptionStore.prescriptions.length }})
                </span>
              </div>
              <button
                v-if="selectedEncounter.status === 'in_progress'"
                @click="showPrescriptionForm = true"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-violet-600 bg-violet-50 border border-violet-100 rounded-lg hover:bg-violet-100 transition"
              >
                <Plus class="w-3.5 h-3.5" />
                Add Prescription
              </button>
            </div>

            <!-- Add prescription form -->
            <div v-if="showPrescriptionForm" class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1 sm:col-span-2">
                  <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                    Medication Name <span class="text-rose-400">*</span>
                  </label>
                  <input
                    v-model="rxForm.medication_name"
                    type="text"
                    placeholder="e.g. Amoxicillin, Metformin 500mg"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-violet-400"
                  />
                </div>
                <div class="space-y-1">
                  <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                    Dosage <span class="text-rose-400">*</span>
                  </label>
                  <input
                    v-model="rxForm.dosage"
                    type="text"
                    placeholder="e.g. 500mg"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-violet-400"
                  />
                </div>
                <div class="space-y-1">
                  <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                    Frequency <span class="text-rose-400">*</span>
                  </label>
                  <input
                    v-model="rxForm.frequency"
                    type="text"
                    placeholder="e.g. Three times daily"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-violet-400"
                  />
                </div>
                <div class="space-y-1">
                  <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Route</label>
                  <select
                    v-model="rxForm.route"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-violet-400"
                  >
                    <option value="">Select route</option>
                    <option value="oral">Oral</option>
                    <option value="iv">IV</option>
                    <option value="im">IM</option>
                    <option value="topical">Topical</option>
                    <option value="inhalation">Inhalation</option>
                    <option value="sublingual">Sublingual</option>
                  </select>
                </div>
                <div class="space-y-1">
                  <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Duration (days)</label>
                  <input
                    v-model.number="rxForm.duration_days"
                    type="number"
                    min="1"
                    placeholder="7"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-violet-400"
                  />
                </div>
                <div class="space-y-1">
                  <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Quantity</label>
                  <input
                    v-model.number="rxForm.quantity"
                    type="number"
                    min="1"
                    placeholder="21"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-violet-400"
                  />
                </div>
                <div class="space-y-1 sm:col-span-2">
                  <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Instructions / Special Notes</label>
                  <textarea
                    v-model="rxForm.instructions"
                    rows="2"
                    placeholder="Take with food, avoid alcohol..."
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-violet-400"
                  ></textarea>
                </div>
              </div>

              <div v-if="prescriptionStore.error" class="mt-3 flex items-center gap-2 text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                <AlertCircle class="w-4 h-4 flex-shrink-0" />
                {{ prescriptionStore.error }}
              </div>

              <div class="flex items-center gap-3 mt-4">
                <button
                  @click="savePrescription"
                  :disabled="prescriptionStore.saving"
                  class="flex items-center gap-2 px-5 py-2 bg-violet-600 text-white text-xs font-bold rounded-lg hover:bg-violet-700 transition disabled:opacity-50"
                >
                  <Save class="w-3.5 h-3.5" />
                  {{ prescriptionStore.saving ? "Saving…" : "Save Prescription" }}
                </button>
                <button
                  @click="cancelRxForm"
                  class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition"
                >
                  Cancel
                </button>
              </div>
            </div>

            <!-- Prescription list -->
            <div v-if="prescriptionStore.loading" class="p-6 space-y-2">
              <div v-for="n in 2" :key="n" class="h-12 bg-slate-50 rounded-lg animate-pulse" />
            </div>
            <div v-else-if="!prescriptionStore.prescriptions.length" class="px-6 py-8 text-center">
              <Pill class="w-8 h-8 mx-auto mb-2 text-slate-200" />
              <p class="text-xs text-slate-400">No prescriptions for this encounter</p>
            </div>
            <div v-else class="divide-y divide-slate-100">
              <div
                v-for="rx in prescriptionStore.prescriptions"
                :key="rx.id"
                class="px-6 py-4 flex items-start justify-between gap-4"
              >
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-sm font-bold text-slate-800">{{ rx.medication_name }}</p>
                    <span class="text-[10px] font-mono text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                      {{ rx.dosage }}
                    </span>
                    <span :class="rxStatusClass(rx.status)" class="text-[9px] font-bold px-1.5 py-0.5 rounded border capitalize">
                      {{ rx.status }}
                    </span>
                  </div>
                  <p class="text-xs text-slate-500 mt-0.5">
                    {{ rx.frequency }}
                    <span v-if="rx.route">· {{ rx.route }}</span>
                    <span v-if="rx.duration_days">· {{ rx.duration_days }} days</span>
                    <span v-if="rx.quantity">· Qty: {{ rx.quantity }}</span>
                  </p>
                  <p v-if="rx.instructions" class="text-[11px] text-slate-400 mt-1 italic">{{ rx.instructions }}</p>
                </div>
                <div v-if="rx.status === 'active'" class="flex items-center gap-1.5 flex-shrink-0">
                  <button
                    @click="completePrescription(rx.id)"
                    class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded hover:bg-emerald-100 transition"
                  >
                    Complete
                  </button>
                  <button
                    @click="cancelPrescription(rx.id)"
                    class="text-[10px] font-bold text-red-500 bg-red-50 border border-red-100 px-2 py-1 rounded hover:bg-red-100 transition"
                  >
                    Cancel
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

          <!-- ── Documents section ─────────────────────────────── -->
          <div v-if="selectedEncounter" class="bg-white border border-slate-200 rounded-xl shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
              <div class="flex items-center gap-2">
                <FileArchive class="w-4 h-4 text-teal-600" />
                <h3 class="text-sm font-bold text-slate-900">Medical Documents</h3>
                <span class="text-[10px] text-slate-400">
                  ({{ documentStore.documents.length }})
                </span>
              </div>
              <button
                v-if="selectedEncounter.status === 'in_progress'"
                @click="showDocUpload = true"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-teal-600 bg-teal-50 border border-teal-100 rounded-lg hover:bg-teal-100 transition"
              >
                <Upload class="w-3.5 h-3.5" />
                Upload
              </button>
            </div>

            <!-- Upload form -->
            <div v-if="showDocUpload" class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 space-y-3">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Document Type <span class="text-rose-400">*</span></label>
                  <select v-model="docForm.document_type" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-teal-400">
                    <option value="">Select type</option>
                    <option value="lab_report">Lab Report</option>
                    <option value="xray">X-Ray</option>
                    <option value="mri">MRI</option>
                    <option value="ct_scan">CT Scan</option>
                    <option value="prescription">Prescription</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="space-y-1">
                  <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Description</label>
                  <input v-model="docForm.description" type="text" placeholder="Brief note..." class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-400" />
                </div>
              </div>
              <div class="space-y-1">
                <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">File <span class="text-rose-400">*</span></label>
                <div class="flex items-center gap-3">
                  <label class="flex items-center gap-2 px-4 py-2 border border-dashed border-slate-300 rounded-lg text-xs text-slate-500 hover:border-teal-400 hover:text-teal-600 cursor-pointer transition">
                    <Paperclip class="w-3.5 h-3.5" />
                    {{ docForm.file ? docForm.file.name : "Choose file (max 10 MB)" }}
                    <input type="file" class="hidden" @change="onDocFileChange" />
                  </label>
                  <span v-if="docForm.file" class="text-[10px] text-slate-400">{{ formatFileSize(docForm.file.size) }}</span>
                </div>
              </div>
              <div v-if="documentStore.error" class="flex items-center gap-2 text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ documentStore.error }}
              </div>
              <div class="flex items-center gap-3">
                <button @click="saveDocument" :disabled="documentStore.uploading" class="flex items-center gap-2 px-5 py-2 bg-teal-600 text-white text-xs font-bold rounded-lg hover:bg-teal-700 transition disabled:opacity-50">
                  <Upload class="w-3.5 h-3.5" />
                  {{ documentStore.uploading ? "Uploading…" : "Upload Document" }}
                </button>
                <button @click="cancelDocForm" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">Cancel</button>
              </div>
            </div>

            <!-- Documents list -->
            <div v-if="documentStore.loading" class="p-6 space-y-2">
              <div v-for="n in 2" :key="n" class="h-12 bg-slate-50 rounded-lg animate-pulse" />
            </div>
            <div v-else-if="!documentStore.documents.length" class="px-6 py-8 text-center">
              <FileArchive class="w-8 h-8 mx-auto mb-2 text-slate-200" />
              <p class="text-xs text-slate-400">No documents attached to this encounter</p>
            </div>
            <div v-else class="divide-y divide-slate-100">
              <div v-for="doc in documentStore.documents" :key="doc.id" class="px-6 py-3 flex items-center gap-4">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" :class="docIconBg(doc.document_type)">
                  <FileText class="w-4 h-4" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-xs font-bold text-slate-800 truncate">{{ doc.file_name }}</p>
                  <p class="text-[10px] text-slate-400">
                    <span class="capitalize">{{ doc.document_type?.replace('_', ' ') }}</span>
                    <span v-if="doc.description"> · {{ doc.description }}</span>
                    <span v-if="doc.file_size"> · {{ formatFileSize(doc.file_size) }}</span>
                  </p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                  <a :href="storageUrl(doc.file_url)" target="_blank" rel="noopener" class="text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-100 px-2 py-1 rounded hover:bg-blue-100 transition">View</a>
                  <a :href="documentStore.downloadUrl(doc.id)" class="text-[10px] font-bold text-slate-600 bg-white border border-slate-200 px-2 py-1 rounded hover:bg-slate-50 transition">Download</a>
                  <button @click="deleteDocument(doc.id)" class="text-[10px] font-bold text-red-500 bg-red-50 border border-red-100 px-2 py-1 rounded hover:bg-red-100 transition">Delete</button>
                </div>
              </div>
            </div>
          </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import {
  AlertCircle, Search, ClipboardList, FileEdit,
  Pill, Plus, Save, FileArchive, Upload, Paperclip, FileText, UserCog,
} from "lucide-vue-next";

import PatientHeaderCard    from "../../components/emr/doctor/PatientHeaderCard.vue";
import MedicalVitalsGrid   from "../../components/emr/doctor/MedicalVitalsGrid.vue";
import EncounterTimeline   from "../../components/emr/doctor/EncounterTimeline.vue";
import EncounterForm       from "../../components/emr/doctor/EncounterForm.vue";

import { useMedicalEncounterStore }  from "../../stores/medicalEncounterStore";
import { usePrescriptionStore }      from "../../stores/prescriptionStore";
import { useMedicalDocumentStore }   from "../../stores/medicalDocumentStore";

const encounterStore    = useMedicalEncounterStore();
const prescriptionStore = usePrescriptionStore();
const documentStore     = useMedicalDocumentStore();

const search               = ref("");
const selectedEncounter    = ref(null);
const patientHistory       = ref([]);   // all encounters for this patient across all doctors
const showPrescriptionForm = ref(false);
const showDocUpload        = ref(false);

// Patient medical profile edit (blood_type, allergies, medical_history)
const showMedicalProfileForm = ref(false);
const medProfileSaving       = ref(false);
const medProfileError        = ref(null);
const BLOOD_TYPES = ["A+","A-","B+","B-","AB+","AB-","O+","O-"];
const medProfileForm = ref({ blood_type: "", allergies: "", medical_history: "" });

const rxForm = ref({
  medication_name: "",
  dosage: "",
  frequency: "",
  route: "",
  duration_days: null,
  quantity: null,
  instructions: "",
});

const docForm = ref({
  document_type: "",
  description: "",
  file: null,
});

onMounted(async () => {
  await encounterStore.fetchAll();
});

/* ── Computed ─────────────────────────────────────────────── */

const filteredEncounters = computed(() => {
  let list = encounterStore.encounters;  // already only in_progress from API
  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter(
      (e) =>
        patientName(e).toLowerCase().includes(q) ||
        (e.chief_complaint ?? "").toLowerCase().includes(q)
    );
  }
  return list;
});

const currentVital = computed(() => selectedEncounter.value?.vitals ?? null);

const pastEncounters = computed(() => {
  if (!selectedEncounter.value?.patient?.id) return [];
  return patientHistory.value.filter((e) => e.id !== selectedEncounter.value.id);
});

const emergencyContact = computed(() => null);

// Whether the current encounter is editable
const isInProgress = computed(() => selectedEncounter.value?.status === "in_progress");

/* ── Actions ──────────────────────────────────────────────── */

async function selectEncounter(enc) {
  selectedEncounter.value = enc;
  showPrescriptionForm.value = false;
  showDocUpload.value = false;
  showMedicalProfileForm.value = false;
  medProfileError.value = null;
  encounterStore.clearError();
  prescriptionStore.clearError();
  documentStore.clearError();

  const full = await encounterStore.fetchById(enc.id);
  selectedEncounter.value = full;

  // Load full cross-doctor patient history for the timeline
  if (full?.patient?.id) {
    patientHistory.value = await encounterStore.fetchPatientHistory(full.patient.id);
  }

  await Promise.all([
    prescriptionStore.fetchByEncounter(enc.id),
    documentStore.fetchByEncounter(enc.id),
  ]);
}

function openMedicalProfileForm() {
  const p = selectedEncounter.value?.patient;
  medProfileForm.value = {
    blood_type:      p?.blood_type ?? "",
    allergies:       p?.allergies ?? "",
    medical_history: p?.medical_history ?? "",
  };
  medProfileError.value = null;
  showMedicalProfileForm.value = true;
}

async function saveMedicalProfile() {
  medProfileError.value = null;
  medProfileSaving.value = true;
  try {
    const updated = await encounterStore.updatePatientMedical(
      selectedEncounter.value.id,
      medProfileForm.value
    );
    // Patch selectedEncounter patient fields immediately
    if (selectedEncounter.value?.patient) {
      selectedEncounter.value = {
        ...selectedEncounter.value,
        patient: { ...selectedEncounter.value.patient, ...updated },
      };
    }
    showMedicalProfileForm.value = false;
  } catch {
    medProfileError.value = encounterStore.error || "Failed to save patient profile.";
  } finally {
    medProfileSaving.value = false;
  }
}

function onEncounterSaved(updated)    { selectedEncounter.value = updated; }
function onEncounterCompleted(done)   {
  selectedEncounter.value = done;
  // Refresh timeline so completed status appears
  if (done?.patient?.id) {
    encounterStore.fetchPatientHistory(done.patient.id).then((h) => { patientHistory.value = h; });
  }
}
function onVitalSaved(vital) {
  if (selectedEncounter.value)
    selectedEncounter.value = { ...selectedEncounter.value, vitals: vital };
}

/* Prescriptions */
async function savePrescription() {
  if (!rxForm.value.medication_name || !rxForm.value.dosage || !rxForm.value.frequency) {
    prescriptionStore.error = "Medication name, dosage and frequency are required.";
    return;
  }
  prescriptionStore.clearError();
  try {
    await prescriptionStore.create({ encounter_id: selectedEncounter.value.id, ...rxForm.value });
    cancelRxForm();
  } catch { /* error shown via store */ }
}
async function completePrescription(id) { try { await prescriptionStore.complete(id); } catch {} }
async function cancelPrescription(id)   { try { await prescriptionStore.cancel(id);   } catch {} }
function cancelRxForm() {
  showPrescriptionForm.value = false;
  rxForm.value = { medication_name: "", dosage: "", frequency: "", route: "", duration_days: null, quantity: null, instructions: "" };
  prescriptionStore.clearError();
}

/* Documents */
function onDocFileChange(e) { docForm.value.file = e.target.files[0] ?? null; }
async function saveDocument() {
  if (!docForm.value.document_type || !docForm.value.file) {
    documentStore.error = "Document type and file are required.";
    return;
  }
  documentStore.clearError();
  try {
    await documentStore.upload({
      patient_id:    selectedEncounter.value.patient?.id,
      encounter_id:  selectedEncounter.value.id,
      document_type: docForm.value.document_type,
      description:   docForm.value.description || undefined,
      file:          docForm.value.file,
    });
    cancelDocForm();
  } catch { /* error shown via store */ }
}
async function deleteDocument(id) {
  try { await documentStore.destroy(id); } catch {}
}
function cancelDocForm() {
  showDocUpload.value = false;
  docForm.value = { document_type: "", description: "", file: null };
  documentStore.clearError();
}

/* ── Helpers ──────────────────────────────────────────────── */

const BACKEND_URL = "http://127.0.0.1:8000/storage";
function storageUrl(path) {
  if (!path) return "#";
  if (path.startsWith("http")) return path;
  return `${BACKEND_URL}/${path}`;
}

function patientName(enc) {
  const p = enc.patient;
  if (!p) return "—";
  return `${p.first_name ?? ""} ${p.last_name ?? ""}`.trim() || "—";
}
function formatDate(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleDateString("en-ET", { day: "numeric", month: "short", year: "numeric" });
}
function calcAge(dob) {
  if (!dob) return null;
  return Math.floor((Date.now() - new Date(dob).getTime()) / (1000 * 60 * 60 * 24 * 365.25));
}
function formatFileSize(bytes) {
  if (!bytes) return "";
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / 1048576).toFixed(1)} MB`;
}
function statusClass(status) {
  return {
    in_progress: "bg-blue-50 text-blue-700 border-blue-100",
    completed:   "bg-emerald-50 text-emerald-700 border-emerald-100",
    cancelled:   "bg-red-50 text-red-500 border-red-100",
  }[status] ?? "bg-slate-50 text-slate-500 border-slate-100";
}
function rxStatusClass(status) {
  return {
    active:    "bg-blue-50 text-blue-700 border-blue-100",
    completed: "bg-emerald-50 text-emerald-700 border-emerald-100",
    cancelled: "bg-red-50 text-red-500 border-red-100",
  }[status] ?? "bg-slate-50 text-slate-500 border-slate-100";
}
function docIconBg(type) {
  return {
    lab_report:   "bg-emerald-50 text-emerald-600",
    xray:         "bg-blue-50 text-blue-600",
    mri:          "bg-indigo-50 text-indigo-600",
    ct_scan:      "bg-violet-50 text-violet-600",
    prescription: "bg-amber-50 text-amber-600",
    other:        "bg-slate-100 text-slate-500",
  }[type] ?? "bg-slate-100 text-slate-500";
}
</script>

<style>
.custom-scroll::-webkit-scrollbar { width: 4px; }
.custom-scroll::-webkit-scrollbar-track { background: transparent; }
.custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
</style>
