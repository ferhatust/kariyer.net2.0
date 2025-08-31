<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="toggleLeft" />
        <q-toolbar-title>İşveren Paneli</q-toolbar-title>
        <q-space />
        <q-btn
          flat
          round
          dense
          :icon="darkMode ? 'light_mode' : 'dark_mode'"
          class="q-mr-sm"
          :aria-label="darkMode ? 'Açık moda geç' : 'Koyu moda geç'"
          @click="toggleDarkMode"
        />
        <q-btn flat dense round icon="logout" @click="logout" />
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="left"
      show-if-above
      bordered
      :mini="mini"
      :width="232"
      :mini-width="68"
      class="bg-white drawer-shadow"
      @mouseover="mini=false"
      @mouseout="mini=true"
    >
      <q-scroll-area class="fit">
        <div class="row items-center q-pa-sm q-pl-md">
          <q-avatar size="28px" class="q-mr-sm">
            <img src="/icons/favicon-32x32.png" alt="logo" />
          </q-avatar>
          <div v-show="!mini" class="text-weight-medium">İşveren Paneli</div>
          <q-space />
          <q-btn dense round flat :icon="mini ? 'chevron_right' : 'chevron_left'" @click="mini = !mini" :aria-label="mini ? 'Genişlet' : 'Daralt'" />
        </div>
        <q-separator />
        <q-list padding>
        <q-item clickable v-ripple to="/admin" exact active-class="active-link">
          <q-item-section avatar><q-icon name="dashboard" /></q-item-section>
          <q-item-section>
            <q-item-label>Dashboard</q-item-label>
            <q-item-label caption>Genel bakış</q-item-label>
          </q-item-section>
          <q-tooltip v-if="mini" anchor="center right" self="center left" :offset="[10,10]">Dashboard</q-tooltip>
        </q-item>
        <q-item clickable v-ripple to="/jobs" active-class="active-link">
          <q-item-section avatar><q-icon name="work" /></q-item-section>
          <q-item-section>
            <q-item-label>İlanlar</q-item-label>
            <q-item-label caption>İş ilanları listesi</q-item-label>
          </q-item-section>
          <q-tooltip v-if="mini" anchor="center right" self="center left" :offset="[10,10]">İlanlar</q-tooltip>
        </q-item>

        <q-item clickable v-ripple to="/applications" active-class="active-link">
          <q-item-section avatar><q-icon name="assignment" /></q-item-section>
          <q-item-section>
            <q-item-label>Başvurular</q-item-label>
            <q-item-label caption>Aday başvuruları</q-item-label>
          </q-item-section>
          <q-item-section side v-if="applicationsCount > 0 && !mini">
            <q-badge color="primary" align="middle">{{ applicationsCount }}</q-badge>
          </q-item-section>
          <q-tooltip v-if="mini" anchor="center right" self="center left" :offset="[10,10]">Başvurular</q-tooltip>
        </q-item>

        <q-item clickable v-ripple to="/admin/records" active-class="active-link">
          <q-item-section avatar><q-icon name="people" /></q-item-section>
          <q-item-section>
            <q-item-label>Kayıtlar</q-item-label>
            <q-item-label caption>İşverenler ve adaylar</q-item-label>
          </q-item-section>
          <q-tooltip v-if="mini" anchor="center right" self="center left" :offset="[10,10]">Kayıtlar</q-tooltip>
        </q-item>
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <q-page-container class="bg-grey-2">
      <div class="q-pa-md container">
        <router-view />
      </div>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { useRouter } from 'vue-router'
import { api } from 'boot/axios'
import { Dark } from 'quasar'

const left = ref(false)
const mini = ref(true)
const router = useRouter()
const applicationsCount = ref(0)
const darkMode = ref(false)

function toggleLeft () { left.value = !left.value }

function logout () {
  localStorage.removeItem('api_token')
  localStorage.removeItem('auth_type')
  router.push('/auth/login')
}

let removeAfterEach = null
onMounted(() => {
  // restore drawer state
  try {
    const savedMini = localStorage.getItem('admin_drawer_mini')
    const savedOpen = localStorage.getItem('admin_drawer_open')
    if (savedMini != null) mini.value = savedMini === '1'
    if (savedOpen != null) left.value = savedOpen === '1'
  } catch { /* ignore */ }

  // auto-close on route for small screens
  removeAfterEach = router.afterEach(() => {
    try { if (window.innerWidth < 1024) left.value = false } catch { /* ignore */ }
  })
  // fetch applications count (best-effort)
  fetchApplicationsCount()
  // apply dark mode from storage
  try {
    const saved = localStorage.getItem('theme_dark')
    if (saved != null) {
      darkMode.value = saved === '1'
      Dark.set(darkMode.value)
    } else {
      darkMode.value = Dark.isActive === true
    }
  } catch { /* ignore */ }
})

watch(mini, v => { try { localStorage.setItem('admin_drawer_mini', v ? '1' : '0') } catch { /* ignore */ } })
watch(left, v => { try { localStorage.setItem('admin_drawer_open', v ? '1' : '0') } catch { /* ignore */ } })

onBeforeUnmount(() => {
  if (typeof removeAfterEach === 'function') {
    try { removeAfterEach() } catch { /* ignore */ }
  }
})

async function fetchApplicationsCount () {
  try {
    const authType = localStorage.getItem('auth_type')
    const endpoint = authType === 'candidate' ? '/basvurular/me' : '/basvurular'
    const { data } = await api.get(endpoint, { params: { page: 1, per_page: 1 } })
    applicationsCount.value = Number(
      (data && (data.total || data.count)) ??
      (Array.isArray(data?.data) ? data.data.length : (Array.isArray(data) ? data.length : 0))
    )
  } catch { /* ignore */ }
}

function toggleDarkMode () {
  darkMode.value = !darkMode.value
  Dark.set(darkMode.value)
  try { localStorage.setItem('theme_dark', darkMode.value ? '1' : '0') } catch { /* ignore */ }
}
</script>

<style scoped>
.container { max-width: 1200px; margin: 0 auto; }
.active-link { color: var(--q-primary); background: rgba(33,150,243,.08); border-left: 3px solid var(--q-primary); }
.drawer-shadow { box-shadow: 0 4px 18px rgba(0,0,0,0.06); }
:deep(.q-dark) .drawer-shadow,
body.body--dark .drawer-shadow { box-shadow: 0 6px 24px rgba(0,0,0,0.35); }
:deep(.q-dark) .active-link,
body.body--dark .active-link { background: rgba(255,255,255,0.06); border-left-color: var(--q-primary); }

/* Dark page background */
body.body--dark :deep(.q-page-container),
:deep(.q-dark) .q-page-container {
  background: #121316 !important;
}

/* Admin header gradient + transitions in dark mode */
body.body--dark :deep(.q-header.bg-primary),
:deep(.q-dark) .q-header.bg-primary {
  background: linear-gradient(180deg, rgba(18,19,22,0.95), rgba(18,19,22,0.86)) !important;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
:deep(.q-header) { transition: background-color .2s ease, border-color .2s ease, box-shadow .2s ease; }
</style>
