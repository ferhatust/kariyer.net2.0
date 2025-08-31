<template>
  <q-layout view="lHh Lpr lFf">
    <!-- Top Navbar -->
    <q-header
      elevated
      class="header-gradient text-dark"
    >
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />
        <q-toolbar-title class="row items-center no-wrap">
          <q-avatar
            size="28px"
            class="q-mr-sm"
          >
            <img
              src="/icons/favicon-32x32.png"
              alt="logo"
            />
          </q-avatar>
          <span class="text-weight-bold">Kariyer Dünyam</span>
        </q-toolbar-title>
        <q-input
          dense
          standout="bg-white text-dark"
          v-model="search"
          placeholder="Ara..."
          class="q-mr-md header-search"
          clearable
        >
          <template #prepend>
            <q-icon name="search" />
          </template>
        </q-input>
        <q-btn
          color="primary"
          unelevated
          class="q-mr-sm q-px-md"
          icon="bolt"
          label="Hızlı Aksiyonlar"
          @click="showQuick = true"
        />
        <q-btn
          flat
          round
          dense
          :icon="darkMode ? 'light_mode' : 'dark_mode'"
          class="q-mr-sm"
          :aria-label="darkMode ? 'Açık moda geç' : 'Koyu moda geç'"
          @click="toggleDarkMode"
        />
        <q-btn
          flat
          round
        >
          <q-avatar size="32px">
            <img
              src="https://cdn.quasar.dev/img/boy-avatar.png"
              alt="avatar"
            />
          </q-avatar>
          <q-menu
            anchor="bottom right"
            self="top right"
          >
            <q-list style="min-width: 180px">
              <q-item
                clickable
                v-ripple
                to="/profile"
              >
                <q-item-section avatar><q-icon name="person" /></q-item-section>
                <q-item-section>Profil</q-item-section>
              </q-item>
              <q-separator />
              <q-item
                clickable
                v-ripple
                @click="logout"
              >
                <q-item-section avatar><q-icon name="logout" /></q-item-section>
                <q-item-section>Çıkış</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <!-- Sidebar -->
    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      class="bg-white drawer-shadow"
      :mini="miniState"
      :width="232"
      :mini-width="68"
      @mouseover="miniState = false"
      @mouseout="miniState = true"
    >
      <q-scroll-area class="fit">
        <!-- Drawer Header -->
        <div class="row items-center q-pa-sm q-pl-md">
          <q-avatar
            size="28px"
            class="q-mr-sm"
          >
            <img
              src="/icons/favicon-32x32.png"
              alt="logo"
            />
          </q-avatar>
          <div
            v-show="!miniState"
            class="text-weight-medium"
          >Kariyer Dünyam</div>
          <q-space />
          <q-btn
            dense
            round
            flat
            :icon="miniState ? 'chevron_right' : 'chevron_left'"
            @click="miniState = !miniState"
            :aria-label="miniState ? 'Genişlet' : 'Daralt'"
          />
        </div>
        <q-separator />
        <q-list padding>
          <q-item-label
            header
            class="text-grey-7"
          >Genel</q-item-label>

          <q-item
            clickable
            v-ripple
            to="/jobs"
            active-class="active-link"
          >
            <q-item-section avatar>
              <q-icon name="work" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Jobs</q-item-label>
              <q-item-label caption>İş ilanları</q-item-label>
            </q-item-section>
            <q-tooltip
              v-if="miniState"
              anchor="center right"
              self="center left"
              :offset="[10, 10]"
            >Jobs</q-tooltip>
          </q-item>

          <q-item
            clickable
            v-ripple
            to="/applications"
            active-class="active-link"
          >
            <q-item-section avatar>
              <q-icon name="assignment" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Applications</q-item-label>
              <q-item-label caption>Başvurular</q-item-label>
            </q-item-section>
            <q-item-section side v-if="applicationsCount > 0 && !miniState">
              <q-badge color="primary" align="middle">{{ applicationsCount }}</q-badge>
            </q-item-section>
            <q-tooltip
              v-if="miniState"
              anchor="center right"
              self="center left"
              :offset="[10, 10]"
            >Applications</q-tooltip>
          </q-item>

          <q-separator
            spaced
            inset
          />
          <q-item-label
            header
            class="text-grey-7"
          >Hesap</q-item-label>

          <q-item
            clickable
            v-ripple
            to="/profile"
            active-class="active-link"
          >
            <q-item-section avatar>
              <q-icon name="person" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Profile</q-item-label>
              <q-item-label caption>Profilim</q-item-label>
            </q-item-section>
            <q-tooltip
              v-if="miniState"
              anchor="center right"
              self="center left"
              :offset="[10, 10]"
            >Profile</q-tooltip>
          </q-item>
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <!-- Content area -->
    <q-page-container class="bg-grey-2">
      <div class="q-pa-md container">
        <router-view />
      </div>
    </q-page-container>

    <q-footer class="bg-white text-grey-7">
      <q-toolbar inset>
        <q-toolbar-title class="text-caption">© {{ year }} Kariyer Dünyam • Tüm hakları saklıdır</q-toolbar-title>
      </q-toolbar>
    </q-footer>

    <!-- Quick Actions Dialog -->
    <QuickActionsDialog
      v-model="showQuick"
      @create:job="handleCreateJob"
    />
    <!-- Auth Dialog shown if not authenticated -->
    <AuthDialog
      v-model="showAuth"
      @authenticated="onAuthenticated"
    />
  </q-layout>

</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { useRouter } from 'vue-router'
import { api } from 'boot/axios'
import { Dark } from 'quasar'
import QuickActionsDialog from 'components/QuickActionsDialog.vue'
import AuthDialog from 'components/AuthDialog.vue'

const leftDrawerOpen = ref(false)
const miniState = ref(true)
const search = ref('')
const year = new Date().getFullYear()
const router = useRouter()
const showQuick = ref(false)
const showAuth = ref(false)
const applicationsCount = ref(0)
const darkMode = ref(false)

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

function logout() {
  localStorage.removeItem('api_token')
  localStorage.removeItem('auth_type')
  // show auth dialog instead of hard redirect
  showAuth.value = true
  router.push('/jobs')
}

function handleCreateJob() {
  // mark intent then go to jobs; JobsPage will pick it up and open dialog
  try { localStorage.setItem('open_create_job', '1') } catch { void 0 }
  router.push('/jobs')
}

let removeAfterEach = null
onMounted(() => {
  // restore drawer states
  try {
    const savedMini = localStorage.getItem('drawer_mini')
    const savedOpen = localStorage.getItem('drawer_open')
    if (savedMini != null) miniState.value = savedMini === '1'
    if (savedOpen != null) leftDrawerOpen.value = savedOpen === '1'
  } catch { /* ignore */ }

  // auth dialog if needed
  try {
    const token = localStorage.getItem('api_token')
    if (!token) { showAuth.value = true }
  } catch { /* ignore */ }

  // auto-close drawer on route change for small screens
  removeAfterEach = router.afterEach(() => {
    try {
      if (window.innerWidth < 1024) leftDrawerOpen.value = false
    } catch { /* ignore */ }
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

watch(miniState, v => {
  try { localStorage.setItem('drawer_mini', v ? '1' : '0') } catch { /* ignore */ }
})
watch(leftDrawerOpen, v => {
  try { localStorage.setItem('drawer_open', v ? '1' : '0') } catch { /* ignore */ }
})

onBeforeUnmount(() => {
  if (typeof removeAfterEach === 'function') {
    try { removeAfterEach() } catch { /* ignore */ }
  }
})

function onAuthenticated(payload) {
  showAuth.value = false
  // If employer, optionally route to admin
  if (payload?.type === 'employer') {
    router.push('/admin')
  }
}

async function fetchApplicationsCount () {
  try {
    const authType = localStorage.getItem('auth_type')
    const endpoint = authType === 'candidate' ? '/basvurular/me' : '/basvurular'
    const { data } = await api.get(endpoint, { params: { page: 1, per_page: 1 } })
    // try common shapes: { total }, { data:[], total }, []
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
.header-gradient {
  background: linear-gradient(180deg, #ffffff 0%, #f4f2fb 100%);
}

.header-search {
  width: 320px;
}

@media (max-width: 768px) {
  .header-search {
    width: 160px;
  }
}

.active-link {
  color: var(--q-primary);
  background: rgba(93, 62, 188, 0.08);
  border-left: 3px solid var(--q-primary);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

/* footer subtle divider */
::v-deep(.q-footer) {
  border-top: 1px solid #e8eaf0;
}

/* drawer visuals */
.drawer-shadow { box-shadow: 0 4px 18px rgba(0,0,0,0.06); }
.active-link {
  color: var(--q-primary);
  background: rgba(93, 62, 188, 0.08);
  border-left: 3px solid var(--q-primary);
}

/* dark mode tweaks */
:deep(.q-dark) .drawer-shadow,
body.body--dark .drawer-shadow { box-shadow: 0 6px 24px rgba(0,0,0,0.35); }
:deep(.q-dark) .active-link,
body.body--dark .active-link {
  background: rgba(255,255,255,0.06);
  border-left-color: var(--q-primary);
}

/* Dark header gradient and page background */
body.body--dark .header-gradient,
:deep(.q-dark) .header-gradient {
  background: linear-gradient(180deg, #1e1f24 0%, #181a1f 100%);
  color: #e6e7eb;
}
body.body--dark :deep(.q-page-container),
:deep(.q-dark) .q-page-container {
  background: #121316 !important;
}

/* Header search input contrast in dark */
body.body--dark .header-search :deep(.q-field__control) {
  background: #2a2b31 !important;
  color: #e6e7eb;
}
body.body--dark .header-search :deep(.q-field__label),
body.body--dark .header-search :deep(.q-field__native) {
  color: #d0d1d6;
}
</style>
