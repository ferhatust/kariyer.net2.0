<template>
  <q-page class="q-pa-none">
    <!-- Top Hero -->
    <div class="homepage-hero q-py-xl q-px-md">
      <div class="container">
        <div class="row items-center q-col-gutter-xl">
          <div class="col-12 col-md-7">
            <div class="text-h4 text-weight-bold q-mb-sm">Kariyerine güç kat</div>
            <div class="text-body1 text-grey-8 q-mb-lg">Binlerce güncel ilan arasından hayalindeki işi keşfet. Hemen ara, filtrele ve başvur.</div>
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-8">
                <q-input v-model="q" dense standout="bg-white text-dark" placeholder="Pozisyon, şirket veya anahtar kelime" @keyup.enter="goJobs">
                  <template #prepend><q-icon name="search" /></template>
                </q-input>
              </div>
              <div class="col-6 col-md-2">
                <q-btn color="primary" unelevated class="full-width" label="Ara" @click="goJobs" />
              </div>
              <div class="col-6 col-md-2">
                <q-btn outline color="primary" class="full-width" label="Tüm İlanlar" @click="router.push('/jobs')" />
              </div>
            </div>
            <div class="q-mt-md">
              <q-chip v-for="c in categories" :key="c" clickable outline color="primary" @click="quickCategory(c)">{{ c }}</q-chip>
            </div>
          </div>
          <div class="col-12 col-md-5">
            <q-card flat bordered class="bg-white promo-card">
              <q-card-section class="row items-center">
                <q-avatar rounded size="56px" class="q-mr-md promo-avatar"><q-icon name="work" color="primary" size="32px"/></q-avatar>
                <div>
                  <div class="text-subtitle1 text-weight-medium">İşveren misiniz?</div>
                  <div class="text-caption text-grey-7">Dakikalar içinde ilan yayınlayın ve adaylara ulaşın.</div>
                </div>
                <q-space />
                <q-btn color="primary" flat label="İlan Yayınla" @click="handleCreateJob" />
              </q-card-section>
            </q-card>
          </div>
        </div>
      </div>
    </div>

    <!-- Portal style content -->
    <div class="q-px-md q-pt-lg q-pb-xl">
      <div class="container">
        <div class="row q-col-gutter-xl">
          <!-- Left column: Featured jobs -->
          <div class="col-12 col-lg-8">
            <div class="row items-center q-mb-md">
              <div class="col text-h6">Öne Çıkan İlanlar</div>
              <div class="col-auto"><q-btn flat dense color="primary" label="Tümünü Gör" @click="router.push('/jobs')"/></div>
            </div>
            <div class="column">
              <JobCard
                v-for="item in featured"
                :key="item.id"
                :item="item"
                :max-chars="120"
                @detail="goDetail"
                @apply="goDetail"
              />
            </div>
          </div>

          <!-- Right column: widgets -->
          <div class="col-12 col-lg-4">
            <SidebarWidgets :popular="popular" :tips="tips" @select="quickCategory" @more-tips="moreTips" @upload-cv="uploadCv" @open-job="goDetail" />
            <q-card flat bordered class="bg-white q-mt-md recent-card" v-if="recentJobs.length">
              <q-card-section class="text-subtitle2">Son Görüntülenenler</q-card-section>
              <q-separator />
              <q-list>
                <q-item v-for="r in recentJobs" :key="r.id" clickable @click="goDetail(r.id)">
                  <q-item-section avatar>
                    <q-avatar rounded size="28px"><img :src="r.logo_url || '/icons/favicon-32x32.png'" alt="logo" loading="lazy"/></q-avatar>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="ellipsis">{{ r.baslik }}</q-item-label>
                    <q-item-label caption class="text-grey-7 ellipsis">{{ r.sirket_adi }}</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card>
          </div>
        </div>
      </div>
    </div>
    <UploadCvDialog v-model="uploadOpen" @uploaded="onCvUploaded" />
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { api } from 'boot/axios'
import { Notify } from 'quasar'
import SidebarWidgets from 'components/SidebarWidgets.vue'
import UploadCvDialog from 'components/UploadCvDialog.vue'
import JobCard from 'components/JobCard.vue'

const router = useRouter()
const q = ref('')
const categories = ['Yazılım', 'Satış', 'Pazarlama', 'Muhasebe', 'İK', 'Tasarım']
const popular = ['Uzaktan yazılım', 'İstanbul satış', 'Stajyer', 'Senior React']
const tips = [
  'CV’nizi kısa ve etkili tutun.',
  'Pozisyona uygun anahtar kelimeler kullanın.',
  'Mülakata hazırlık için şirketi araştırın.'
]

const featured = ref([])
const uploadOpen = ref(false)
const recentJobs = ref([])

function goDetail (id) {
  try { router.push({ path: `/jobs/${id}` }) } catch (e) { void e }
}

function saveRecentSearch (term) {
  try {
    if (!term) return
    const raw = localStorage.getItem('recent_searches')
    const arr = raw ? JSON.parse(raw) : []
    const list = Array.isArray(arr) ? arr : []
    const next = [term, ...list.filter(t => t !== term)].slice(0, 10)
    localStorage.setItem('recent_searches', JSON.stringify(next))
  } catch { /* ignore */ }
}

function goJobs () {
  const params = q.value ? { query: { q: q.value } } : {}
  if (q.value) saveRecentSearch(q.value)
  router.push({ path: '/jobs', ...params })
}

function quickCategory (c) {
  saveRecentSearch(c)
  router.push({ path: '/jobs', query: { q: c } })
}

function moreTips () {
  Notify.create({ type: 'info', message: 'Yakında: daha fazla kariyer içeriği!' })
}

function uploadCv () {
  uploadOpen.value = true
}

function onCvUploaded () {
  try { localStorage.setItem('cv_uploaded_at', String(Date.now())) } catch { /* ignore */ }
}

function handleCreateJob () {
  try { localStorage.setItem('open_create_job', '1') } catch (e) { void e }
  router.push('/jobs')
}

onMounted(async () => {
  try {
    const { data } = await api.get('/is_ilanlari', { params: { page: 1, per_page: 6 } })
    featured.value = data.data || data
  } catch (e) {
    console.error(e)
    Notify.create({ type: 'negative', message: 'İlanlar yüklenemedi' })
  }
  // load recent jobs from localStorage
  try {
    const raw = localStorage.getItem('recent_jobs')
    const arr = raw ? JSON.parse(raw) : []
    recentJobs.value = Array.isArray(arr) ? arr : []
  } catch { /* ignore */ }
})
</script>

<style scoped>
.homepage-hero {
  background: linear-gradient(135deg, rgba(93,62,188,0.10), rgba(93,62,188,0.04));
}
.promo-card { border: 1px solid #e8eaf0; border-radius: 12px; }
.promo-avatar { background: #f4f2fb; }
.job-row { border: 1px solid #e8eaf0; border-radius: 10px; }
.job-row:hover { box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
.job-logo { background: #f2f5ff; }
.container { max-width: 1200px; margin: 0 auto; }

/* Dark mode for recent card */
body.body--dark :deep(.recent-card) { border-color: #2a2b31; }

/* Dark mode overrides */
body.body--dark .homepage-hero,
:deep(.q-dark) .homepage-hero {
  background: linear-gradient(135deg, rgba(93,62,188,0.18), rgba(0,0,0,0.20));
}
body.body--dark .promo-card,
:deep(.q-dark) .promo-card {
  border-color: #2a2b31;
}
body.body--dark .promo-avatar,
:deep(.q-dark) .promo-avatar { background: #2a2b31; }

/* Search input in hero (standout forces bg/text) */
body.body--dark .homepage-hero :deep(.q-field--standout .q-field__control) {
  background: #2a2b31 !important;
  color: #e6e7eb !important;
}
body.body--dark .homepage-hero :deep(.q-field--standout .q-field__native),
body.body--dark .homepage-hero :deep(.q-field--standout .q-field__label) {
  color: #d6d7dc !important;
}
</style>
