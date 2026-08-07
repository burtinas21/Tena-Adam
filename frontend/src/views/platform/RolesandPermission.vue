<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-3 sm:p-5 overflow-y-auto font-sans dark:text-slate-200">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100 tracking-tight">Roles &amp; Permissions</h1>
        <p class="text-xs text-gray-500 dark:text-slate-400 font-medium mt-0.5">Manage platform roles and their permission sets.</p>
      </div>
      <button @click="openCreateRole"
        class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-x-2 transition shadow-sm">
        <Plus class="w-3.5 h-3.5" /> New Role
      </button>
    </div>

    <!-- Error banner -->
    <div v-if="globalError"
      class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
      <AlertCircle class="w-4 h-4 flex-shrink-0" /> {{ globalError }}
    </div>

    <!-- Loading skeleton -->
    <div v-if="roleStore.loading && roleStore.roles.length === 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <div v-for="n in 6" :key="n"
        class="h-36 bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 animate-pulse" />
    </div>

    <!-- Role Cards Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <div v-for="role in roleStore.roles" :key="role.id"
        class="relative bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:border-[#004795]/30 transition-all duration-200 p-5 flex flex-col gap-3">

        <!-- Top row: avatar + badge + three-dot menu -->
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-sm font-bold text-[#004795] dark:text-blue-400 flex-shrink-0">
              {{ roleInitials(role.name) }}
            </div>
            <div>
              <p class="text-sm font-bold text-gray-800 dark:text-slate-100 leading-tight">{{ formatRoleName(role.name) }}</p>
              <span v-if="isSystemRole(role.name)"
                class="inline-block mt-0.5 text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">
                SYSTEM
              </span>
            </div>
          </div>

          <!-- Three-dot menu -->
          <div class="relative">
            <button @click.stop="toggleMenu(role.id)"
              class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition">
              <MoreVertical class="w-4 h-4" />
            </button>
            <div v-if="openMenuId === role.id"
              class="absolute right-0 top-8 z-30 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-100 dark:border-slate-700 py-1 overflow-hidden">
              <button @click="openManagePermissions(role)"
                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-[#004795] dark:hover:text-blue-400 transition">
                <ShieldCheck class="w-3.5 h-3.5" /> Manage Permissions
              </button>
              <button @click="openViewDetails(role)"
                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                <Eye class="w-3.5 h-3.5" /> View Details
              </button>
              <div class="border-t border-gray-100 dark:border-slate-700 my-1" />
              <button @click="confirmDeleteRole(role)"
                :disabled="isSystemRole(role.name)"
                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition disabled:opacity-40 disabled:cursor-not-allowed">
                <Trash2 class="w-3.5 h-3.5" /> Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Permission count -->
        <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-slate-500">
          <ShieldCheck class="w-3.5 h-3.5 text-[#004795]/50 dark:text-blue-500/50" />
          <span>{{ role.permissions?.length ?? 0 }} permissions</span>
        </div>

        <!-- Description -->
        <p v-if="role.description" class="text-[11px] text-gray-400 dark:text-slate-500 truncate">{{ role.description }}</p>

        <!-- Manage Permissions quick button -->
        <button @click="openManagePermissions(role)"
          class="mt-auto w-full flex items-center justify-center gap-1.5 py-2 rounded-lg border border-[#004795]/20 text-[#004795] dark:text-blue-400 dark:border-blue-500/20 text-xs font-semibold hover:bg-[#004795] hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition">
          <ShieldCheck class="w-3.5 h-3.5" /> Manage Permissions
        </button>
      </div>

      <div v-if="roleStore.roles.length === 0"
        class="col-span-full text-center py-16 text-sm text-gray-400 dark:text-slate-500">
        No roles found.
      </div>
    </div>

    <!-- ── Manage Permissions Modal (centered) ── -->
    <Teleport to="body">
      <div v-if="showPermPanel"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="closePermPanel">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="closePermPanel" />
        <div class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">

          <!-- Panel header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex-shrink-0">
            <div>
              <h2 class="text-sm font-bold text-gray-800 dark:text-slate-100">Manage Permissions</h2>
              <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">{{ formatRoleName(selectedRole?.name) }}</p>
            </div>
            <button @click="closePermPanel"
              class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-400 transition">
              <X class="w-4 h-4" />
            </button>
          </div>

          <!-- Search input -->
          <div class="px-6 pt-4 flex-shrink-0">
            <div class="relative">
              <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" />
              <input
                v-model="permSearch"
                type="text"
                placeholder="Search permissions…"
                class="w-full pl-9 pr-4 py-2 text-xs border border-gray-200 dark:border-slate-600 rounded-lg bg-gray-50 dark:bg-slate-800 text-gray-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 placeholder-gray-400"
              />
            </div>
          </div>

          <!-- Auto-save status bar -->
          <div class="mx-6 mt-4 flex-shrink-0">
            <div v-if="autoSaving"
              class="flex items-center gap-2 text-xs text-blue-600 dark:text-blue-400 font-medium">
              <Loader2 class="w-3.5 h-3.5 animate-spin" /> Saving…
            </div>
            <div v-else-if="autoSaveError"
              class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2">
              <AlertCircle class="w-3.5 h-3.5 flex-shrink-0" /> {{ autoSaveError }}
            </div>
            <div v-else-if="lastSaved"
              class="flex items-center gap-1.5 text-xs text-green-600 dark:text-green-400 font-medium">
              <CheckCircle2 class="w-3.5 h-3.5" /> Saved
            </div>
          </div>

          <!-- Select all row -->
          <div class="flex items-center justify-between px-6 pt-4 pb-2 flex-shrink-0">
            <p class="text-xs text-gray-500 dark:text-slate-400">
              {{ checkedPermissions.size }} of {{ permStore.permissions.length }} selected
            </p>
            <div class="flex gap-2">
              <button @click="selectAll" :disabled="autoSaving"
                class="text-[11px] font-semibold text-[#004795] dark:text-blue-400 hover:underline disabled:opacity-50 disabled:cursor-wait">Select all</button>
              <span class="text-gray-300 dark:text-slate-600">|</span>
              <button @click="deselectAll" :disabled="autoSaving"
                class="text-[11px] font-semibold text-gray-400 dark:text-slate-500 hover:underline disabled:opacity-50 disabled:cursor-wait">Deselect all</button>
            </div>
          </div>

          <!-- Permission groups (scrollable) -->
          <div class="flex-1 overflow-y-auto px-6 pb-6 space-y-5">

            <!-- No results -->
            <div v-if="filteredGroups.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
              <Search class="w-8 h-8 text-gray-200 dark:text-slate-600 mb-3" />
              <p class="text-sm font-semibold text-gray-400 dark:text-slate-500">No permissions found</p>
              <p class="text-xs text-gray-300 dark:text-slate-600 mt-1">Try a different search term.</p>
            </div>

            <div v-for="group in filteredGroups" :key="group.module" class="space-y-2">
              <div class="flex items-center justify-between">
                <h3 class="text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">{{ group.module }}</h3>
                <button @click="toggleGroup(group)"
                  :disabled="autoSaving"
                  class="text-[10px] text-[#004795] dark:text-blue-400 hover:underline font-semibold disabled:opacity-50 disabled:cursor-wait">
                  {{ isGroupAllChecked(group) ? 'Deselect all' : 'Select all' }}
                </button>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                <label v-for="perm in group.permissions" :key="perm.id"
                  class="flex items-center gap-2.5 px-3 py-2 rounded-lg border transition select-none"
                  :class="[
                    autoSaving ? 'cursor-wait opacity-70' : 'cursor-pointer hover:bg-blue-50/50 dark:hover:bg-blue-900/10',
                    checkedPermissions.has(perm.id)
                      ? 'border-[#004795]/30 bg-blue-50/60 dark:bg-blue-900/10 dark:border-blue-500/30'
                      : 'border-gray-100 dark:border-slate-700 bg-gray-50/50'
                  ]">
                  <input type="checkbox" :value="perm.id" :checked="checkedPermissions.has(perm.id)"
                    :disabled="autoSaving"
                    @change="togglePermission(perm.id)"
                    class="w-3.5 h-3.5 accent-[#004795] cursor-pointer disabled:cursor-wait" />
                  <span class="text-xs text-gray-700 dark:text-slate-300 font-medium">{{ formatPermName(perm.name) }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Panel footer: close only (auto-save handles persistence) -->
          <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex-shrink-0">
            <p class="text-[11px] text-gray-400 dark:text-slate-500">
              {{ checkedPermissions.size }} of {{ permStore.permissions.length }} permissions enabled
            </p>
            <button @click="closePermPanel"
              class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-300 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg transition">
              Close
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ── View Details Modal ── -->
    <Teleport to="body">
      <div v-if="showDetailsModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-800 dark:text-slate-100">Role Details</h3>
            <button @click="showDetailsModal = false"
              class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-400">
              <X class="w-4 h-4" />
            </button>
          </div>
          <div class="flex items-center gap-4 mb-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-xl font-bold text-[#004795] dark:text-blue-400">
              {{ roleInitials(detailRole?.name ?? '') }}
            </div>
            <div>
              <p class="text-base font-bold text-gray-800 dark:text-slate-100">{{ formatRoleName(detailRole?.name) }}</p>
              <span v-if="isSystemRole(detailRole?.name)"
                class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">SYSTEM</span>
            </div>
          </div>
          <div class="space-y-3 text-xs">
            <div class="flex justify-between border-b border-gray-100 dark:border-slate-700 pb-2">
              <span class="text-gray-400 dark:text-slate-500 font-medium">Description</span>
              <span class="text-gray-700 dark:text-slate-200 font-semibold">{{ detailRole?.description || '—' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 dark:border-slate-700 pb-2">
              <span class="text-gray-400 dark:text-slate-500 font-medium">Total Permissions</span>
              <span class="text-gray-700 dark:text-slate-200 font-semibold">{{ detailRole?.permissions?.length ?? 0 }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400 dark:text-slate-500 font-medium">Type</span>
              <span class="text-gray-700 dark:text-slate-200 font-semibold">{{ isSystemRole(detailRole?.name) ? 'System Role' : 'Custom Role' }}</span>
            </div>
          </div>
          <div class="flex justify-end mt-5">
            <button @click="showDetailsModal = false"
              class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-300 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg transition">
              Close
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ── Create / Edit Role Modal ── -->
    <Teleport to="body">
      <div v-if="showRoleForm"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-sm p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-800 dark:text-slate-100">{{ editingRole ? 'Edit Role' : 'New Role' }}</h3>
            <button @click="closeRoleForm" class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-400">
              <X class="w-4 h-4" />
            </button>
          </div>
          <form @submit.prevent="handleRoleFormSubmit" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Role Name</label>
              <input v-model="roleForm.name" type="text" placeholder="e.g. nurse, lab_technician" required
                :disabled="editingRole && isSystemRole(editingRole.name)"
                class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 disabled:opacity-50 disabled:cursor-not-allowed" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Description</label>
              <input v-model="roleForm.description" type="text" placeholder="Short description (optional)"
                class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#004795]/30" />
            </div>
            <div v-if="roleFormError"
              class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 text-xs rounded-lg px-3 py-2">
              <AlertCircle class="w-3.5 h-3.5 flex-shrink-0" /> {{ roleFormError }}
            </div>
            <div class="flex justify-end gap-2 pt-1">
              <button type="button" @click="closeRoleForm"
                class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-300 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg transition">
                Cancel
              </button>
              <button type="submit" :disabled="roleStore.loading"
                class="px-4 py-2 text-xs font-bold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-1.5">
                <Loader2 v-if="roleStore.loading" class="w-3 h-3 animate-spin" />
                {{ editingRole ? 'Save Changes' : 'Create Role' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- ── Delete Confirmation Modal ── -->
    <Teleport to="body">
      <div v-if="showDeleteConfirm"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-sm p-6">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0">
              <Trash2 class="w-5 h-5 text-red-500" />
            </div>
            <div>
              <h3 class="text-sm font-bold text-gray-800 dark:text-slate-100">Delete Role</h3>
              <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">This action cannot be undone.</p>
            </div>
          </div>
          <p class="text-sm text-gray-600 dark:text-slate-300 mb-5">
            Are you sure you want to delete
            <span class="font-semibold text-gray-800 dark:text-slate-100">{{ formatRoleName(roleToDelete?.name) }}</span>?
            All permission assignments for this role will be removed.
          </p>
          <div class="flex justify-end gap-3">
            <button @click="showDeleteConfirm = false"
              class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-slate-300 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg transition">
              Cancel
            </button>
            <button @click="handleDeleteRole" :disabled="roleStore.loading"
              class="px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition disabled:opacity-60 flex items-center gap-2">
              <Loader2 v-if="roleStore.loading" class="w-3.5 h-3.5 animate-spin" />
              Delete
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Click-outside overlay for menus -->
    <div v-if="openMenuId" class="fixed inset-0 z-20" @click="openMenuId = null" />
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Plus, Trash2, ShieldCheck, AlertCircle,
  CheckCircle2, Loader2, X, MoreVertical, Eye, Search,
} from 'lucide-vue-next'
import { useRoleStore } from '../../stores/roleStore'
import { usePermissionStore } from '../../stores/permissionStore'

const roleStore = useRoleStore()
const permStore = usePermissionStore()

// ── State ──────────────────────────────────────────────────────────────────────
const globalError = ref(null)
const openMenuId  = ref(null)

// Permissions panel
const showPermPanel      = ref(false)
const selectedRole       = ref(null)
const checkedPermissions = ref(new Set())
const permSearch         = ref('')

// Auto-save state
const autoSaving      = ref(false)
const autoSaveError   = ref(null)
const lastSaved       = ref(false)
let   autoSaveTimer   = null

// View details modal
const showDetailsModal = ref(false)
const detailRole       = ref(null)

// Create / edit role modal
const showRoleForm  = ref(false)
const editingRole   = ref(null)
const roleForm      = ref({ name: '', description: '' })
const roleFormError = ref(null)

// Delete modal
const showDeleteConfirm = ref(false)
const roleToDelete      = ref(null)

// ── Lifecycle ──────────────────────────────────────────────────────────────────
onMounted(async () => {
  const results = await Promise.allSettled([
    roleStore.fetchAll(),
    permStore.fetchAll(),
  ])
  if (results[0].status === 'rejected') {
    globalError.value = results[0].reason?.response?.data?.message
      || 'Failed to load roles. Make sure your account has the view_roles permission.'
  }
  if (results[1].status === 'rejected') {
    console.warn('Could not load permissions:', results[1].reason)
  }
})

// ── Computed ───────────────────────────────────────────────────────────────────
const filteredGroups = computed(() => {
  const q = permSearch.value.trim().toLowerCase()
  if (!q) return permStore.grouped
  return permStore.grouped
    .map(group => ({
      ...group,
      permissions: group.permissions.filter(p =>
        p.name.toLowerCase().includes(q) ||
        formatPermName(p.name).toLowerCase().includes(q)
      ),
    }))
    .filter(group => group.permissions.length > 0)
})

// ── Three-dot menu ─────────────────────────────────────────────────────────────
function toggleMenu(id) {
  openMenuId.value = openMenuId.value === id ? null : id
}

// ── Auto-save helper ───────────────────────────────────────────────────────────
async function persistPermissions() {
  if (!selectedRole.value) return
  clearTimeout(autoSaveTimer)
  autoSaveError.value = null
  lastSaved.value = false
  autoSaving.value = true
  try {
    const result = await roleStore.syncPermissions(
      selectedRole.value.id,
      [...checkedPermissions.value]
    )
    const freshRole = result ?? roleStore.roles.find(r => r.id === selectedRole.value.id)
    if (freshRole) selectedRole.value = freshRole
    lastSaved.value = true
    autoSaveTimer = setTimeout(() => { lastSaved.value = false }, 2000)
  } catch (err) {
    autoSaveError.value = err.response?.data?.message || 'Failed to save — please try again.'
    autoSaveTimer = setTimeout(() => { autoSaveError.value = null }, 4000)
  } finally {
    autoSaving.value = false
  }
}

// ── Manage Permissions panel ───────────────────────────────────────────────────
function openManagePermissions(role) {
  openMenuId.value = null
  selectedRole.value = role
  autoSaveError.value = null
  lastSaved.value = false
  permSearch.value = ''
  checkedPermissions.value = new Set((role.permissions ?? []).map(p => p.id))
  showPermPanel.value = true
}

function closePermPanel() {
  clearTimeout(autoSaveTimer)
  showPermPanel.value = false
  selectedRole.value  = null
  permSearch.value    = ''
}

async function togglePermission(id) {
  const updated = new Set(checkedPermissions.value)
  if (updated.has(id)) updated.delete(id)
  else updated.add(id)
  checkedPermissions.value = updated
  await persistPermissions()
}

async function selectAll() {
  checkedPermissions.value = new Set(permStore.permissions.map(p => p.id))
  await persistPermissions()
}

async function deselectAll() {
  checkedPermissions.value = new Set()
  await persistPermissions()
}

function isGroupAllChecked(group) {
  return group.permissions.every(p => checkedPermissions.value.has(p.id))
}

async function toggleGroup(group) {
  const updated = new Set(checkedPermissions.value)
  if (isGroupAllChecked(group)) {
    group.permissions.forEach(p => updated.delete(p.id))
  } else {
    group.permissions.forEach(p => updated.add(p.id))
  }
  checkedPermissions.value = updated
  await persistPermissions()
}

// ── View Details ───────────────────────────────────────────────────────────────
function openViewDetails(role) {
  openMenuId.value   = null
  detailRole.value   = role
  showDetailsModal.value = true
}

// ── Create / Edit Role ─────────────────────────────────────────────────────────
function openCreateRole() {
  editingRole.value  = null
  roleForm.value     = { name: '', description: '' }
  roleFormError.value = null
  showRoleForm.value  = true
}

function closeRoleForm() {
  showRoleForm.value  = false
  editingRole.value   = null
  roleFormError.value = null
}

async function handleRoleFormSubmit() {
  roleFormError.value = null
  try {
    if (editingRole.value) {
      await roleStore.update(editingRole.value.id, roleForm.value)
    } else {
      await roleStore.create(roleForm.value)
    }
    closeRoleForm()
  } catch (err) {
    const errors = err.response?.data?.errors
    roleFormError.value = errors
      ? Object.values(errors).flat().join(' ')
      : err.response?.data?.message || 'Something went wrong.'
  }
}

// ── Delete Role ────────────────────────────────────────────────────────────────
function confirmDeleteRole(role) {
  openMenuId.value    = null
  roleToDelete.value  = role
  showDeleteConfirm.value = true
}

async function handleDeleteRole() {
  if (!roleToDelete.value) return
  try {
    await roleStore.destroy(roleToDelete.value.id)
    showDeleteConfirm.value = false
    roleToDelete.value      = null
  } catch (err) {
    globalError.value = err.response?.data?.message || 'Failed to delete role.'
    showDeleteConfirm.value = false
  }
}

// ── Helpers ────────────────────────────────────────────────────────────────────
const SYSTEM_ROLES = ['platform_admin', 'hospital_admin', 'doctor', 'receptionist', 'patient']
function isSystemRole(name) { return SYSTEM_ROLES.includes(name) }

function formatRoleName(name) {
  if (!name) return ''
  return name.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

function formatPermName(name) {
  return name.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

function roleInitials(name) {
  return name.split('_').slice(0, 2).map(w => w[0]?.toUpperCase() ?? '').join('')
}
</script>
