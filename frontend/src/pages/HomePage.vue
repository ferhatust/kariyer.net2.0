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
              <div v-for="item in featured" :key="item.id" class="job-row q-pa-md q-mb-sm bg-white">
                <div class="row items-center no-wrap">
                  <div class="col-auto">
                    <q-avatar size="48px" rounded class="job-logo">
                      <img :src="item.logo_url || '/icons/favicon-32x32.png'" alt="logo" />
                    </q-avatar>
                  </div>
                  <div class="col">
                    <div class="text-subtitle1 text-weight-medium">{{ item.baslik }}</div>
                    <div class="text-caption text-grey-7">{{ item.sirket_adi }} • {{ item.konum || 'Belirtilmedi' }}</div>
                    <div class="q-mt-xs text-body2 text-grey-8">{{ (item.aciklama || '').slice(0, 120) }}<span v-if="(item.aciklama||'').length>120">...</span></div>
                    <div class="q-mt-sm row items-center">
                      <q-chip v-if="item.uzaktan_mi" size="sm" color="blue-1" text-color="primary" icon="home_work">Uzaktan</q-chip>
                      <q-chip v-if="item.deneyim_duzeyi" size="sm" color="grey-2" text-color="grey-9" icon="trending_up">{{ item.deneyim_duzeyi }}</q-chip>
                      <q-chip v-if="item.maas" size="sm" color="green-1" text-color="green-8" icon="payments">{{ item.maas }} {{ item.para_birimi || '' }}</q-chip>
                      <q-space />
                      <q-btn flat color="primary" dense label="Detay" @click="goDetail(item.id)" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right column: widgets -->
          <div class="col-12 col-lg-4">
            <q-card flat bordered class="bg-white q-mb-md">
              <q-card-section class="text-subtitle2">Popüler Aramalar</q-card-section>
              <q-separator />
              <q-list separator>
                <q-item v-for="t in popular" :key="t" clickable @click="quickCategory(t)">
                  <q-item-section avatar><q-icon name="search"/></q-item-section>
                  <q-item-section>{{ t }}</q-item-section>
                </q-item>
              </q-list>
            </q-card>

            <q-card flat bordered class="bg-white">
              <q-card-section class="text-subtitle2">Kariyer İpuçları</q-card-section>
              <q-separator />
              <q-list>
                <q-item v-for="(tip, i) in tips" :key="i">
                  <q-item-section>{{ tip }}</q-item-section>
                </q-item>
              </q-list>
            </q-card>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { api } from 'boot/axios'
import { Notify } from 'quasar'

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

function goDetail (id) {
  try { router.push({ path: `/jobs/${id}` }) } catch (e) { void e }
}

function goJobs () {
  const params = q.value ? { query: { q: q.value } } : {}
  router.push({ path: '/jobs', ...params })
}

function quickCategory (c) {
  router.push({ path: '/jobs', query: { q: c } })
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
</style>
