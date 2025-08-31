<template>
  <q-layout view="lHh Lpr lFf">
    <!-- Top Navbar -->
    <q-header elevated class="header-gradient text-dark">
      <q-toolbar>
        <q-btn flat dense round icon="menu" aria-label="Menu" @click="toggleLeftDrawer" />
        <q-toolbar-title class="row items-center no-wrap">
          <q-avatar size="28px" class="q-mr-sm">
            <img src="/icons/favicon-32x32.png" alt="logo" />
          </q-avatar>
          <span class="text-weight-bold">Kariyer Dünyam</span>
        </q-toolbar-title>
        <q-input dense standout="bg-white text-dark" v-model="search" placeholder="Ara..." class="q-mr-md header-search" clearable>
          <template #prepend>
            <q-icon name="search" />
          </template>
        </q-input>
        <q-btn color="primary" unelevated class="q-mr-sm q-px-md" icon="bolt" label="Hızlı Aksiyonlar" @click="showQuick = true" />
        <q-btn flat round>
          <q-avatar size="32px">
            <img src="https://cdn.quasar.dev/img/boy-avatar.png" alt="avatar" />
          </q-avatar>
          <q-menu anchor="bottom right" self="top right">
            <q-list style="min-width: 180px">
              <q-item clickable v-ripple to="/profile">
                <q-item-section avatar><q-icon name="person" /></q-item-section>
                <q-item-section>Profil</q-item-section>
              </q-item>
              <q-separator />
              <q-item clickable v-ripple @click="logout">
                <q-item-section avatar><q-icon name="logout" /></q-item-section>
                <q-item-section>Çıkış</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <!-- Sidebar -->
    <q-drawer v-model="leftDrawerOpen" show-if-above bordered class="bg-white" :mini="miniState" @mouseover="miniState=false" @mouseout="miniState=true">
      <q-scroll-area class="fit">
        <q-list padding>
          <q-item-label header class="text-grey-7">Genel</q-item-label>

          <q-item clickable v-ripple to="/jobs" active-class="active-link">
            <q-item-section avatar>
              <q-icon name="work" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Jobs</q-item-label>
              <q-item-label caption>İş ilanları</q-item-label>
            </q-item-section>
          </q-item>

          <q-item clickable v-ripple to="/applications" active-class="active-link">
            <q-item-section avatar>
              <q-icon name="assignment" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Applications</q-item-label>
              <q-item-label caption>Başvurular</q-item-label>
            </q-item-section>
          </q-item>

          <q-separator spaced inset />
          <q-item-label header class="text-grey-7">Hesap</q-item-label>

          <q-item clickable v-ripple to="/profile" active-class="active-link">
            <q-item-section avatar>
              <q-icon name="person" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Profile</q-item-label>
              <q-item-label caption>Profilim</q-item-label>
            </q-item-section>
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
    <QuickActionsDialog v-model="showQuick" @create:job="handleCreateJob" />
    <!-- Auth Dialog shown if not authenticated -->
    <AuthDialog v-model="showAuth" @authenticated="onAuthenticated" />
  </q-layout>
  
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import QuickActionsDialog from 'components/QuickActionsDialog.vue'
import AuthDialog from 'components/AuthDialog.vue'

const leftDrawerOpen = ref(false)
const miniState = ref(true)
const search = ref('')
const year = new Date().getFullYear()
const router = useRouter()
const showQuick = ref(false)
const showAuth = ref(false)

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

onMounted(() => {
  try {
    const token = localStorage.getItem('api_token')
    if (!token) {
      showAuth.value = true
    }
  } catch { /* ignore */ }
})

function onAuthenticated (payload) {
  showAuth.value = false
  // If employer, optionally route to admin
  if (payload?.type === 'employer') {
    router.push('/admin')
  }
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
  .header-search { width: 160px; }
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
</style>
