<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-7xl mx-auto">

      <!-- Page header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Doctors & Staff</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">
            Manage doctors, schedules and receptionists.
          </p>
        </div>
        <!-- Page-level tab toggle: Doctors | Receptionists -->
        <div class="flex gap-1 bg-gray-100 p-1 rounded-xl">
          <button v-for="pt in PAGE_TABS" :key="pt.key" @click="pageTab = pt.key"
            :class="pageTab === pt.key
              ? 'bg-white text-[#004795] shadow-sm'
              : 'text-gray-500 hover:text-gray-700'"
            class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
            <component :is="pt.icon" class="w-3.5 h-3.5" />
            {{ pt.label }}
          </button>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════════════════════ -->
      <!-- DOCTORS TAB                                                        -->
      <!-- ═══════════════════════════════════════════════════════════════════ -->
      <template v-if="pageTab === 'doctors'">
        <div class="flex justify-end mb-4">
          <button @click="openCreate"
            class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-2 transition shadow-sm">
            <Plus class="w-3.5 h-3.5" />
            Add Doctor
          </button>
        </div>

        <div v-if="doctorStore.error && !showDoctorForm"
          class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
          <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ doctorStore.error }}
        </div>

        <!-- Two-column layout: list | detail -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

          <!-- Doctor list -->
          <div class="lg:col-span-1 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
              <span class="text-sm font-bold text-gray-800">All Doctors</span>
              <span class="text-xs text-gray-400">{{ doctorStore.doctors.length }} total</span>
            </div>
            <div v-if="doctorStore.loading && !doctorStore.doctors.length" class="divide-y divide-gray-50">
              <div v-for="n in 3" :key="n" class="p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gray-100 animate-pulse" />
                <div class="flex-1 space-y-2">
                  <div class="h-3 bg-gray-100 rounded animate-pulse w-3/4" />
                  <div class="h-2.5 bg-gray-100 rounded animate-pulse w-1/2" />
                </div>
              </div>
            </div>
            <div v-else-if="!doctorStore.doctors.length" class="py-12 text-center text-gray-400">
              <UserPlus class="w-8 h-8 mx-auto mb-2 text-gray-300" />
              <p class="text-sm font-medium">No doctors yet</p>
            </div>
            <div v-else class="divide-y divide-gray-50 max-h-[70vh] overflow-y-auto">
              <div v-for="doc in doctorStore.doctors" :key="doc.id"
                @click="selectDoctor(doc)"
                class="p-4 flex items-center gap-3 cursor-pointer transition-colors"
                :class="doctorStore.selectedDoctor?.id === doc.id
                  ? 'bg-blue-50 border-l-2 border-l-[#004795]'
                  : 'hover:bg-gray-50/60'">
                <div class="w-10 h-10 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0 overflow-hidden">
                  <img v-if="doc.profile_picture_url" :src="doc.profile_picture_url" class="w-full h-full object-cover" />
                  <span v-else class="text-sm font-bold text-[#004795]">
                    {{ (doc.user?.first_name?.[0] ?? '') + (doc.user?.last_name?.[0] ?? '') }}
                  </span>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-800 truncate">
                    Dr. {{ doc.user?.first_name }} {{ doc.user?.last_name }}
                  </p>
                  <p class="text-xs text-gray-400 truncate">{{ doc.department?.name ?? '—' }}</p>
                </div>
                <ChevronRight class="w-4 h-4 text-gray-300 flex-shrink-0" />
              </div>
            </div>
          </div>

          <!-- Detail panel -->
          <div class="lg:col-span-2">
            <div v-if="!doctorStore.selectedDoctor"
              class="bg-white rounded-xl border border-gray-100 shadow-sm py-20 flex flex-col items-center justify-center text-gray-400">
              <Stethoscope class="w-10 h-10 mb-3 text-gray-300" />
              <p class="text-sm font-medium">Select a doctor to view details</p>
            </div>
            <div v-else>
              <!-- Detail tabs -->
              <div class="flex gap-1 mb-4 bg-gray-100 p-1 rounded-xl w-fit">
                <button v-for="tab in DETAIL_TABS" :key="tab.key" @click="activeTab = tab.key"
                  :class="activeTab === tab.key
                    ? 'bg-white text-[#004795] shadow-sm'
                    : 'text-gray-500 hover:text-gray-700'"
                  class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all">
                  {{ tab.label }}
                </button>
              </div>

              <!-- TAB: Profile -->
              <div v-if="activeTab === 'profile'">
                <DoctorProfileCard :doctor="doctorStore.selectedDoctor" @edit="openEdit" @delete="confirmDeleteDoctor" />
              </div>

              <!-- TAB: Schedule -->
              <div v-if="activeTab === 'schedule'">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                  <div class="flex items-center justify-between mb-4">
                    <div>
                      <h3 class="text-sm font-bold text-gray-800">Weekly Schedule</h3>
                      <p class="text-xs text-gray-400 mt-0.5">Working hours per day of week</p>
                    </div>
                    <button @click="openCreateSchedule" :disabled="doctorStore.scheduleLoading"
                      class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2 px-3 rounded-lg flex items-center gap-1.5 transition disabled:opacity-50">
                      <Plus class="w-3.5 h-3.5" />Add Day
                    </button>
                  </div>
                  <div v-if="doctorStore.scheduleLoading" class="space-y-2">
                    <div v-for="n in 3" :key="n" class="h-12 bg-gray-100 rounded-xl animate-pulse" />
                  </div>
                  <ScheduleGrid v-else :schedules="doctorStore.schedules"
                    @edit-schedule="openEditSchedule" @delete-schedule="confirmDeleteSchedule" />
                </div>
              </div>

              <!-- TAB: Leaves -->
              <!-- <div v-if="activeTab === 'leaves'">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                  <div class="flex items-center justify-between mb-4">
                    <div>
                      <h3 class="text-sm font-bold text-gray-800">Leave Requests</h3>
                      <p class="text-xs text-gray-400 mt-0.5">Doctor leave and absence records</p>
                    </div>
                  </div>
                  <div v-if="doctorStore.leaveLoading" class="space-y-2">
                    <div v-for="n in 3" :key="n" class="h-12 bg-gray-100 rounded-xl animate-pulse" />
                  </div>
                  <div v-else-if="!doctorStore.leaves.length" class="py-10 text-center text-gray-400">
                    <CalendarOff class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                    <p class="text-sm font-medium">No leave requests</p>
                  </div>
                  <div v-else class="divide-y divide-gray-50">
                    <div v-for="leave in doctorStore.leaves" :key="leave.id"
                      class="py-3 flex items-center justify-between gap-4">
                      <div>
                        <p class="text-sm font-semibold text-gray-800">{{ leave.leave_date }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 capitalize">
                          {{ leave.leave_type }} · {{ leave.reason || 'No reason' }}
                        </p>
                      </div>
                      <div class="flex items-center gap-2">
                        <span :class="leaveStatusClass(leave.status)"
                          class="text-xs font-semibold px-2.5 py-0.5 rounded-full border capitalize">
                          {{ leave.status }}
                        </span>
                        <template v-if="leave.status === 'pending'">
                          <button @click="handleApproveLeave(leave.id, 'approved')"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition" title="Approve">
                            <Check class="w-3.5 h-3.5" />
                          </button>
                          <button @click="handleApproveLeave(leave.id, 'rejected')"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Reject">
                            <X class="w-3.5 h-3.5" />
                          </button>
                        </template>
                        <button @click="handleDeleteLeave(leave.id)"
                          class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                          <Trash2 class="w-3.5 h-3.5" />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div> -->
            </div>
          </div>
        </div>
      </template>

      <!-- ═══════════════════════════════════════════════════════════════════ -->
      <!-- RECEPTIONISTS TAB                                                  -->
      <!-- ═══════════════════════════════════════════════════════════════════ -->
      <template v-if="pageTab === 'receptionists'">
        <div class="flex justify-end mb-4">
          <button @click="openReceptionistForm"
            class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-2 transition shadow-sm">
            <Plus class="w-3.5 h-3.5" />
            Add Receptionist
          </button>
        </div>

        <div v-if="receptionistError && !showReceptionistForm"
          class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
          <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ receptionistError }}
        </div>

        <!-- Receptionist grid -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <span class="text-sm font-bold text-gray-800">All Receptionists</span>
            <span class="text-xs text-gray-400">{{ receptionists.length }} total</span>
          </div>

          <!-- Loading -->
          <div v-if="receptionistLoading && !receptionists.length" class="divide-y divide-gray-50">
            <div v-for="n in 3" :key="n" class="px-5 py-4 flex items-center gap-4">
              <div class="w-10 h-10 rounded-full bg-gray-100 animate-pulse flex-shrink-0" />
              <div class="flex-1 space-y-2">
                <div class="h-3 bg-gray-100 animate-pulse rounded w-1/3" />
                <div class="h-2.5 bg-gray-100 animate-pulse rounded w-1/4" />
              </div>
            </div>
          </div>

          <!-- Empty -->
          <div v-else-if="!receptionists.length" class="py-16 text-center text-gray-400">
            <Users class="w-8 h-8 mx-auto mb-2 text-gray-300" />
            <p class="text-sm font-medium">No receptionists added yet</p>
          </div>

          <!-- List -->
          <div v-else class="divide-y divide-gray-50">
            <div v-for="r in receptionists" :key="r.id"
              class="px-5 py-4 flex items-center gap-4 hover:bg-gray-50/60 transition-colors">
              <div class="w-10 h-10 rounded-full bg-[#004795]/10 flex items-center justify-center text-sm font-bold text-[#004795] flex-shrink-0">
                {{ (r.first_name?.[0] ?? '') + (r.last_name?.[0] ?? '') }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800">{{ r.first_name }} {{ r.last_name }}</p>
                <p class="text-xs text-gray-400">{{ r.email }}</p>
              </div>
              <p class="text-xs text-gray-500 hidden sm:block">{{ r.phone || '—' }}</p>
              <span :class="r.is_active !== false
                  ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                  : 'bg-gray-50 text-gray-500 border-gray-200'"
                class="text-[11px] font-semibold px-2 py-0.5 rounded-full border">
                {{ r.is_active !== false ? 'Active' : 'Inactive' }}
              </span>
              <button @click="confirmDeleteReceptionist(r)"
                class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition flex-shrink-0" title="Remove">
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </template>

    </div><!-- /max-w-7xl -->

    <!-- ── DOCTOR FORM MODAL ─────────────────────────────────────────────── -->
    <div v-if="showDoctorForm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="closeDoctorForm">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0">
          <h3 class="text-sm font-bold text-gray-800">{{ editingDoctor ? 'Edit Doctor' : 'Add New Doctor' }}</h3>
          <button @click="closeDoctorForm" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
            <X class="w-4 h-4" />
          </button>
        </div>
        <form @submit.prevent="handleDoctorSubmit" class="px-6 py-4 space-y-4 overflow-y-auto flex-1">
          <div v-if="doctorFormError"
            class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ doctorFormError }}
          </div>
          <template v-if="!editingDoctor">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
                <input v-model="doctorForm.first_name" type="text" required placeholder="Abebe"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
                <input v-model="doctorForm.last_name" type="text" required placeholder="Girma"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
              </div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
              <input v-model="doctorForm.email" type="email" required placeholder="doctor@hospital.et"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone</label>
                <input v-model="doctorForm.phone" type="text" placeholder="+251911000000"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
              </div>
            </div>
            <!-- Invitation notice -->
            <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-200 rounded-lg px-3 py-3">
              <svg class="w-4 h-4 text-[#004795] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <p class="text-xs text-blue-700 leading-relaxed">
                An invitation email will be sent to the doctor. They will set their own password by clicking the activation link.
              </p>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">License Number <span class="text-red-500">*</span></label>
              <input v-model="doctorForm.license_number" type="text" required placeholder="ETH-MED-10001"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Department <span class="text-red-500">*</span></label>
              <select v-model="doctorForm.department_id" required
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition">
                <option value="" disabled>Select department</option>
                <option v-for="dept in deptStore.departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Practice Start Date <span class="text-red-500">*</span></label>
                <input v-model="doctorForm.practice_start_date" type="date" required
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Consultation Fee (ETB)</label>
                <input v-model.number="doctorForm.consultation_fee" type="number" min="0"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
              </div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Profile Picture</label>
              <div class="flex items-center gap-3">
                <div v-if="profilePicPreview" class="w-14 h-14 rounded-full overflow-hidden border-2 border-[#004795]/30 flex-shrink-0">
                  <img :src="profilePicPreview" class="w-full h-full object-cover" alt="Preview" />
                </div>
                <div v-else class="w-14 h-14 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0 border-2 border-dashed border-[#004795]/20">
                  <UserPlus class="w-5 h-5 text-[#004795]/40" />
                </div>
                <input type="file" accept="image/*" @change="onProfilePic"
                  class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#004795]/10 file:text-[#004795] hover:file:bg-[#004795]/20 transition" />
              </div>
            </div>
          </template>
          <template v-else>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Department</label>
              <select v-model="doctorForm.department_id"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition">
                <option v-for="dept in deptStore.departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Consultation Fee (ETB)</label>
              <input v-model.number="doctorForm.consultation_fee" type="number" min="0"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bio</label>
              <textarea v-model="doctorForm.bio" rows="3" placeholder="Short bio..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none" />
            </div>
            <div class="flex items-center gap-3">
              <button type="button" @click="doctorForm.is_telehealth_available = !doctorForm.is_telehealth_available"
                :class="doctorForm.is_telehealth_available ? 'bg-[#004795]' : 'bg-gray-200'"
                class="relative w-10 h-5 rounded-full transition-colors duration-200">
                <span :class="doctorForm.is_telehealth_available ? 'translate-x-5' : 'translate-x-0'"
                  class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-300" />
              </button>
              <span class="text-sm text-gray-700 font-medium">Telemedicine Available</span>
            </div>
          </template>
        </form>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0">
          <button type="button" @click="closeDoctorForm"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
          <button @click="handleDoctorSubmit" :disabled="doctorStore.loading"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="doctorStore.loading" class="w-3.5 h-3.5 animate-spin" />
            {{ editingDoctor ? 'Save Changes' : 'Add Doctor' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ── RECEPTIONIST FORM MODAL ───────────────────────────────────────── -->
    <div v-if="showReceptionistForm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="closeReceptionistForm">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md flex flex-col">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0">
          <div>
            <h3 class="text-sm font-bold text-gray-800">Add Receptionist</h3>
            <p class="text-xs text-gray-400 mt-0.5">Create a receptionist account for this hospital.</p>
          </div>
          <button @click="closeReceptionistForm" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
            <X class="w-4 h-4" />
          </button>
        </div>
        <form @submit.prevent="handleReceptionistSubmit" class="px-6 py-5 space-y-4 overflow-y-auto flex-1">
          <div v-if="receptionistFormError"
            class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ receptionistFormError }}
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
              <input v-model="receptionistForm.first_name" type="text" required placeholder="Hana"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
              <input v-model="receptionistForm.last_name" type="text" required placeholder="Tadesse"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
            <input v-model="receptionistForm.email" type="email" required placeholder="reception@hospital.et"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone</label>
            <input v-model="receptionistForm.phone" type="text" placeholder="+251911000000"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
          </div>
          <!-- Invitation notice -->
          <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-200 rounded-lg px-3 py-3">
            <svg class="w-4 h-4 text-[#004795] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <p class="text-xs text-blue-700 leading-relaxed">
              An invitation email will be sent to the receptionist. They will set their own password by clicking the activation link.
            </p>
          </div>
        </form>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0">
          <button type="button" @click="closeReceptionistForm"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
          <button @click="handleReceptionistSubmit" :disabled="receptionistLoading"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="receptionistLoading" class="w-3.5 h-3.5 animate-spin" />
            Add Receptionist
          </button>
        </div>
      </div>
    </div>

    <!-- ── SCHEDULE FORM MODAL ───────────────────────────────────────────── -->
    <ScheduleControls
      v-if="showScheduleForm"
      :schedule="editingSchedule"
      :doctor-id="doctorStore.selectedDoctor?.id ?? ''"
      :used-days="doctorStore.scheduledDays"
      :loading="doctorStore.scheduleLoading"
      :error="scheduleFormError"
      @close="closeScheduleForm"
      @submit="handleScheduleSubmit"
    />

    <!-- ── DELETE DOCTOR CONFIRM ─────────────────────────────────────────── -->
    <div v-if="showDeleteDoctor"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <Trash2 class="w-5 h-5 text-red-500" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-800">Remove Doctor</h3>
            <p class="text-xs text-gray-400 mt-0.5">This will delete the doctor account.</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-5">
          Remove <span class="font-semibold text-gray-800">
            Dr. {{ doctorToDelete?.user?.first_name }} {{ doctorToDelete?.user?.last_name }}
          </span>?
        </p>
        <div class="flex items-center justify-end gap-3">
          <button @click="showDeleteDoctor = false"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
          <button @click="handleDeleteDoctor" :disabled="doctorStore.loading"
            class="px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="doctorStore.loading" class="w-3.5 h-3.5 animate-spin" />Remove
          </button>
        </div>
      </div>
    </div>

    <!-- ── DELETE SCHEDULE CONFIRM ───────────────────────────────────────── -->
    <div v-if="showDeleteSchedule"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <Trash2 class="w-5 h-5 text-red-500" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-800">Delete Schedule</h3>
            <p class="text-xs text-gray-400 mt-0.5">This removes the day from the schedule.</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-5">
          Delete <span class="font-semibold">{{ DAY_LABELS[scheduleToDelete?.day_of_week] }}</span> schedule?
        </p>
        <div class="flex items-center justify-end gap-3">
          <button @click="showDeleteSchedule = false"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
          <button @click="handleDeleteSchedule" :disabled="doctorStore.scheduleLoading"
            class="px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="doctorStore.scheduleLoading" class="w-3.5 h-3.5 animate-spin" />Delete
          </button>
        </div>
      </div>
    </div>

    <!-- ── DELETE RECEPTIONIST CONFIRM ──────────────────────────────────── -->
    <div v-if="showDeleteReceptionist"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <Trash2 class="w-5 h-5 text-red-500" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-800">Remove Receptionist</h3>
            <p class="text-xs text-gray-400 mt-0.5">This will delete the account permanently.</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-5">
          Remove <span class="font-semibold text-gray-800">
            {{ receptionistToDelete?.first_name }} {{ receptionistToDelete?.last_name }}
          </span>?
        </p>
        <div class="flex items-center justify-end gap-3">
          <button @click="showDeleteReceptionist = false"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
          <button @click="handleDeleteReceptionist" :disabled="receptionistLoading"
            class="px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="receptionistLoading" class="w-3.5 h-3.5 animate-spin" />Remove
          </button>
        </div>
      </div>
    </div>

  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
import {
  Plus, AlertCircle, ChevronRight, Stethoscope, UserPlus,
  Trash2, Loader2, X, Check, CalendarOff, Users,
} from "lucide-vue-next";
import { useDoctorStore }     from "../../stores/doctorStore";
import { useDepartmentStore } from "../../stores/departmentStore";
import receptionistStaffApi   from "../../api/receptionistStaffApi";
import DoctorProfileCard  from "../../components/hospital-admin/doctor/DoctorProfileCard.vue";
import ScheduleGrid       from "../../components/hospital-admin/doctor/ScheduleGrid.vue";
import ScheduleControls   from "../../components/hospital-admin/doctor/ScheduleControls.vue";
import { useToast } from "../../composables/useToast";

const { showToast } = useToast();

const DAY_LABELS = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
const DETAIL_TABS = [
  { key: "profile",  label: "Profile"  },
  { key: "schedule", label: "Schedule" },
  { key: "leaves",   label: "Leaves"   },
];
const PAGE_TABS = [
  { key: "doctors",       label: "Doctors",       icon: Stethoscope },
  { key: "receptionists", label: "Receptionists", icon: Users       },
];

const doctorStore = useDoctorStore();
const deptStore   = useDepartmentStore();
const pageTab     = ref("doctors");
const activeTab   = ref("profile");

// ── Doctor form ───────────────────────────────────────────────────────────────
const showDoctorForm  = ref(false);
const editingDoctor   = ref(null);
const doctorFormError = ref(null);
const doctorForm      = ref({});
const profilePicFile  = ref(null);
const profilePicPreview = ref(null);

function openCreate() {
  editingDoctor.value   = null;
  doctorFormError.value = null;
  profilePicFile.value  = null;
  doctorForm.value = {
    first_name: "", last_name: "", email: "", phone: "",
    license_number: "", department_id: "", practice_start_date: "",
    consultation_fee: 0, is_telehealth_available: false,
  };
  showDoctorForm.value = true;
}

function openEdit(doctor) {
  editingDoctor.value   = doctor;
  doctorFormError.value = null;
  doctorForm.value = {
    department_id:           doctor.department?.id ?? "",
    consultation_fee:        doctor.consultation_fee ?? 0,
    bio:                     doctor.bio ?? "",
    is_telehealth_available: doctor.is_telehealth_available ?? false,
  };
  showDoctorForm.value = true;
}

function closeDoctorForm() {
  showDoctorForm.value  = false;
  editingDoctor.value   = null;
  doctorFormError.value = null;
  profilePicFile.value  = null;
  profilePicPreview.value = null;
}

function onProfilePic(e) {
  const file = e.target.files[0] ?? null;
  profilePicFile.value = file;
  if (file) {
    profilePicPreview.value = URL.createObjectURL(file);
  } else {
    profilePicPreview.value = null;
  }
}

async function handleDoctorSubmit() {
  doctorFormError.value = null;
  const isEdit = !!editingDoctor.value;
  try {
    if (isEdit) {
      await doctorStore.update(editingDoctor.value.id, doctorForm.value);
      showToast("Doctor updated successfully", "success");
    } else {
      const payload = { ...doctorForm.value };
      if (profilePicFile.value) payload.profile_picture = profilePicFile.value;
      await doctorStore.create(payload);
      showToast("Doctor added successfully", "success");
    }
    closeDoctorForm();
  } catch (err) {
    const errors = err.response?.data?.errors;
    const msg = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
    doctorFormError.value = msg;
    showToast(msg, "error");
  }
}

// ── Delete doctor ─────────────────────────────────────────────────────────────
const showDeleteDoctor = ref(false);
const doctorToDelete   = ref(null);

function confirmDeleteDoctor(doctor) {
  doctorToDelete.value   = doctor;
  showDeleteDoctor.value = true;
}

async function handleDeleteDoctor() {
  const name = `Dr. ${doctorToDelete.value?.user?.first_name} ${doctorToDelete.value?.user?.last_name}`.trim();
  try {
    await doctorStore.destroy(doctorToDelete.value.id);
    showDeleteDoctor.value = false;
    doctorToDelete.value   = null;
    activeTab.value = "profile";
    showToast(`${name} removed successfully`, "success");
  } catch (err) {
    const msg = err.response?.data?.message || "Failed to remove doctor.";
    showToast(msg, "error");
  }
}

// ── Doctor selection ──────────────────────────────────────────────────────────
async function selectDoctor(doc) {
  doctorStore.selectDoctor(doc);
  activeTab.value = "profile";
  await Promise.all([
    doctorStore.fetchSchedules(doc.id),
    doctorStore.fetchLeavesForDoctor(doc.id),
  ]);
}

// ── Schedule form ─────────────────────────────────────────────────────────────
const showScheduleForm  = ref(false);
const editingSchedule   = ref(null);
const scheduleFormError = ref(null);

function openCreateSchedule() {
  editingSchedule.value   = null;
  scheduleFormError.value = null;
  showScheduleForm.value  = true;
}

function openEditSchedule(schedule) {
  editingSchedule.value   = schedule;
  scheduleFormError.value = null;
  showScheduleForm.value  = true;
}

function closeScheduleForm() {
  showScheduleForm.value  = false;
  editingSchedule.value   = null;
  scheduleFormError.value = null;
}

async function handleScheduleSubmit(payload) {
  scheduleFormError.value = null;
  const isEdit = !!editingSchedule.value;
  try {
    if (isEdit) {
      await doctorStore.updateSchedule(editingSchedule.value.id, payload);
      showToast("Schedule updated successfully", "success");
    } else {
      await doctorStore.createSchedule(payload);
      showToast("Schedule added successfully", "success");
    }
    closeScheduleForm();
  } catch (err) {
    const errors = err.response?.data?.errors;
    const msg = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
    scheduleFormError.value = msg;
    showToast(msg, "error");
  }
}

const showDeleteSchedule = ref(false);
const scheduleToDelete   = ref(null);

function confirmDeleteSchedule(schedule) {
  scheduleToDelete.value   = schedule;
  showDeleteSchedule.value = true;
}

async function handleDeleteSchedule() {
  const day = DAY_LABELS[scheduleToDelete.value?.day_of_week] ?? "day";
  try {
    await doctorStore.destroySchedule(scheduleToDelete.value.id);
    showDeleteSchedule.value = false;
    scheduleToDelete.value   = null;
    showToast(`${day} schedule deleted successfully`, "success");
  } catch (err) {
    const msg = err.response?.data?.message || "Failed to delete schedule.";
    showToast(msg, "error");
  }
}

// ── Leave helpers ─────────────────────────────────────────────────────────────
function leaveStatusClass(status) {
  return {
    pending:  "bg-amber-50 text-amber-700 border-amber-200",
    approved: "bg-emerald-50 text-emerald-700 border-emerald-200",
    rejected: "bg-red-50 text-red-600 border-red-200",
  }[status] ?? "bg-gray-50 text-gray-500 border-gray-200";
}

async function handleApproveLeave(id, status) {
  try { await doctorStore.approveLeave(id, status); } catch { /* store error */ }
}

async function handleDeleteLeave(id) {
  try { await doctorStore.destroyLeave(id); } catch { /* store error */ }
}

// ── Receptionists ─────────────────────────────────────────────────────────────
const receptionists           = ref([]);
const receptionistLoading     = ref(false);
const receptionistError       = ref(null);
const showReceptionistForm    = ref(false);
const receptionistFormError   = ref(null);
const receptionistForm        = ref({});
const showDeleteReceptionist  = ref(false);
const receptionistToDelete    = ref(null);

async function fetchReceptionists() {
  try {
    receptionistLoading.value = true;
    receptionistError.value   = null;
    const res = await receptionistStaffApi.getAll();
    receptionists.value = res.data?.data ?? res.data ?? [];
  } catch (err) {
    receptionistError.value = err.response?.data?.message || "Failed to load receptionists";
  } finally {
    receptionistLoading.value = false;
  }
}

function openReceptionistForm() {
  receptionistFormError.value = null;
  receptionistForm.value = {
    first_name: "", last_name: "", email: "", phone: "",
  };
  showReceptionistForm.value = true;
}

function closeReceptionistForm() {
  showReceptionistForm.value  = false;
  receptionistFormError.value = null;
}

async function handleReceptionistSubmit() {
  receptionistFormError.value = null;
  try {
    receptionistLoading.value = true;
    const res = await receptionistStaffApi.create(receptionistForm.value);
    const created = res.data?.user ?? res.data;
    receptionists.value.unshift(created);
    closeReceptionistForm();
    showToast(`Receptionist "${created.first_name} ${created.last_name}" added successfully`, "success");
  } catch (err) {
    const errors = err.response?.data?.errors;
    const msg = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
    receptionistFormError.value = msg;
    showToast(msg, "error");
  } finally {
    receptionistLoading.value = false;
  }
}

function confirmDeleteReceptionist(r) {
  receptionistToDelete.value   = r;
  showDeleteReceptionist.value = true;
}

async function handleDeleteReceptionist() {
  const name = `${receptionistToDelete.value?.first_name} ${receptionistToDelete.value?.last_name}`.trim();
  try {
    receptionistLoading.value = true;
    await receptionistStaffApi.destroy(receptionistToDelete.value.id);
    receptionists.value = receptionists.value.filter(
      (r) => r.id !== receptionistToDelete.value.id
    );
    showDeleteReceptionist.value = false;
    receptionistToDelete.value   = null;
    showToast(`Receptionist "${name}" removed successfully`, "success");
  } catch (err) {
    const msg = err.response?.data?.message || "Failed to delete receptionist.";
    receptionistError.value = msg;
    showToast(msg, "error");
  } finally {
    receptionistLoading.value = false;
  }
}

// ── Mount ─────────────────────────────────────────────────────────────────────
onMounted(async () => {
  await Promise.all([
    doctorStore.fetchAll(),
    deptStore.fetchAll(),
    fetchReceptionists(),
  ]);
});
</script>
