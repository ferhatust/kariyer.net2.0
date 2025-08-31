<template>
  <q-page class="q-pa-none">
    <!-- Hero header -->
    <div class="hero q-pa-md q-pa-lg-md">
      <div class="container">
        <div class="text-h5 text-weight-medium q-mb-xs">Kariyerini ileri taşı</div>
        <div class="text-body2 text-grey-8 q-mb-md">Binlerce güncel ilan arasından sana uygun işi bul</div>
        <div class="row q-col-gutter-sm items-center">
          <div class="col-12 col-md-6">
            <q-input v-model="filters.q" dense clearable placeholder="Pozisyon, şirket veya anahtar kelime">
              <template #prepend><q-icon name="search" /></template>
            </q-input>
          </div>
          <div class="col-6 col-md-3">
            <q-input v-model="filters.konum" dense clearable placeholder="Konum" />
          </div>
          <div class="col-6 col-md-3">
            <q-select v-model="filters.deneyim_duzeyi" :options="deneyimOptions" dense clearable placeholder="Deneyim" />
          </div>
        </div>
      </div>
    </div>
    <div class="q-pa-md">
    <div class="row q-col-gutter-md">
      <div class="col-12 col-lg-8">
        <q-card flat bordered class="bg-white">
          <q-card-section class="row items-center">
            <div class="col text-h6">Jobs</div>
            <div class="col-auto">
              <q-btn color="primary" unelevated icon="add" label="Yeni İlan" @click="openCreate()" />
            </div>
          </q-card-section>
          <q-separator />
          <!-- Filters -->
          <q-card-section>
            <div class="row q-col-gutter-md items-end">
              <div class="col-12 col-md-4">
                <q-input v-model="filters.q" dense clearable debounce="0" placeholder="Ara: başlık, açıklama, şirket">
                  <template #append>
                    <q-icon name="search" />
                  </template>
                </q-input>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <q-input v-model="filters.konum" dense clearable label="Konum" />
              </div>
              <div class="col-6 col-sm-3 col-md-2">
                <q-toggle v-model="filters.uzaktan_mi" dense label="Uzaktan" />
              </div>
              <div class="col-6 col-sm-3 col-md-3">
                <q-select v-model="filters.deneyim_duzeyi" :options="deneyimOptions" dense clearable label="Deneyim" />
              </div>
            </div>
          </q-card-section>
          <q-separator />
          <q-card-section>
            <div v-if="loading" class="row q-col-gutter-md">
              <div v-for="n in 6" :key="n" class="col-12">
                <q-skeleton type="rect" height="100px" />
              </div>
            </div>
            <div v-else>
              <JobCard
                v-for="item in jobs"
                :key="item.id"
                :item="item"
                @detail="goDetail"
                @apply="openApplyFromList"
              />
              <div v-if="!jobs.length" class="q-mt-md">
                <q-banner class="bg-grey-3">Kayıt bulunamadı</q-banner>
              </div>
              <div class="q-mt-md flex flex-center" v-if="(pagination?.last_page || 1) > 1">
                <q-pagination
                  v-model="page"
                  :max="pagination?.last_page || 1"
                  color="primary"
                  boundary-links
                  @update:model-value="onChangePage"
                />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <!-- Right Sidebar Widgets -->
      <div class="col-12 col-lg-4">
        <SidebarWidgets :popular="popular" :tips="tips" @select="quickCategory" @more-tips="moreTips" @upload-cv="uploadCv" @open-job="openJob" />
      </div>
    </div>
    </div>

    <!-- Create Job Dialog -->
    <q-dialog v-model="createOpen" persistent>
      <q-card style="min-width: 480px; max-width: 86vw">
        <q-card-section class="text-h6 q-pb-sm">Yeni İş İlanı</q-card-section>
        <q-separator />
        <q-card-section class="q-pt-md q-pb-sm">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-sm-6">
              <q-input dense v-model="form.isveren_id" type="number" label="İşveren ID" filled :rules="[val=>!!val||'Zorunlu']" />
            </div>
            <div class="col-12 col-sm-6">
              <q-input dense v-model="form.sirket_adi" label="Şirket Adı" filled :rules="[val=>!!val||'Zorunlu']" />
            </div>
            <div class="col-12 col-sm-6">
              <q-input dense v-model="form.baslik" label="Başlık" filled :rules="[val=>!!val||'Zorunlu']" />
            </div>
            <div class="col-12 col-sm-6">
              <q-input dense v-model="form.konum" label="Konum" filled />
            </div>
            <div class="col-12">
              <q-input dense v-model="form.aciklama" type="textarea" label="Açıklama" filled autogrow :rules="[val=>!!val||'Zorunlu']" />
            </div>
            <div class="col-12 col-sm-4">
              <q-toggle dense v-model="form.uzaktan_mi" label="Uzaktan" />
            </div>
            <div class="col-12 col-sm-4">
              <q-input dense v-model.number="form.maas" type="number" label="Maaş" filled />
            </div>
            <div class="col-12 col-sm-4">
              <q-input dense v-model="form.para_birimi" label="Para Birimi" filled />
            </div>
            <div class="col-12 col-sm-6">
              <q-input dense v-model="form.deneyim_duzeyi" label="Deneyim Düzeyi" filled />
            </div>
            <div class="col-12 col-sm-6">
              <q-input dense v-model="form.son_tarih" type="date" label="Son Tarih" filled />
            </div>
          </div>
        </q-card-section>
        <q-separator />
        <q-card-actions align="right" class="q-py-sm">
          <q-btn flat label="Vazgeç" v-close-popup />
          <q-btn color="primary" :loading="submitting" unelevated label="Kaydet" @click="submitCreate" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Apply Dialog -->
    <q-dialog v-model="applyOpen">
      <q-card style="min-width: 520px; max-width: 90vw">
        <q-card-section class="text-h6">Başvuru Gönder</q-card-section>
        <q-separator />
        <q-card-section>
          <q-input v-model="coverLetter" type="textarea" autogrow filled label="Ön Yazı" />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Kapat" v-close-popup />
          <q-btn color="primary" :loading="applySubmitting" unelevated label="Gönder" @click="submitApply" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Upload CV Dialog -->
    <UploadCvDialog v-model="uploadOpen" @uploaded="onCvUploaded" />
  </q-page>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { api } from 'boot/axios'
import { Notify, debounce } from 'quasar'
import JobCard from 'components/JobCard.vue'
import UploadCvDialog from 'components/UploadCvDialog.vue'
import SidebarWidgets from 'components/SidebarWidgets.vue'

const jobs = ref([])
const page = ref(1)
const pagination = ref(null)
const loading = ref(false)
const router = useRouter()
// no router usage yet; detail navigation can be added later

const createOpen = ref(false)
const uploadOpen = ref(false)
const submitting = ref(false)
// apply dialog state
const applyOpen = ref(false)
const applySubmitting = ref(false)
const selectedJobId = ref(null)
const coverLetter = ref('')
// sidebar widgets data
const popular = [
  'Uzaktan yazılım',
  'İstanbul satış',
  'Stajyer',
  'Senior React'
]
const tips = [
  'CV’nizi kısa ve etkili tutun.',
  'Pozisyona uygun anahtar kelimeler kullanın.',
  'Mülakata hazırlık için şirketi araştırın.'
]
const form = ref({
  isveren_id: '',
  sirket_adi: '',
  baslik: '',
  aciklama: '',
  konum: '',
  uzaktan_mi: false,
  maas: null,
  para_birimi: 'TRY',
  deneyim_duzeyi: '',
  son_tarih: ''
})

// Filters
const filters = ref({
  q: '',
  konum: '',
  uzaktan_mi: null,
  deneyim_duzeyi: null
})
const deneyimOptions = [
  'Stajyer',
  'Yeni Mezun',
  'Junior',
  'Mid',
  'Senior',
  'Lead'
]

async function fetchJobs (append = false) {
  loading.value = true
  try {
    const params = { page: page.value, per_page: 20 }
    if (filters.value.q) params.q = filters.value.q
    if (filters.value.konum) params.konum = filters.value.konum
    if (filters.value.deneyim_duzeyi) params.deneyim_duzeyi = filters.value.deneyim_duzeyi
    if (filters.value.uzaktan_mi !== null) params.uzaktan_mi = filters.value.uzaktan_mi

    const { data } = await api.get('/is_ilanlari', { params })
    const pageItems = data.data || data
    if (append) {
      jobs.value = jobs.value.concat(pageItems)
    } else {
      jobs.value = pageItems
    }
    pagination.value = data
  } catch (err) {
    console.error('Jobs fetch failed', err?.response?.data || err)
    Notify.create({ type: 'negative', message: 'İlanlar yüklenemedi' })
  } finally {
    loading.value = false
  }
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

const debouncedFetch = debounce(() => {
  page.value = 1
  fetchJobs(false)
  saveRecentSearch(filters.value.q)
}, 300)

function openCreate () {
  // require employer auth to create a job
  try {
    const token = localStorage.getItem('api_token')
    const type = localStorage.getItem('auth_type')
    if (!token) {
      Notify.create({ type: 'warning', message: 'İlan oluşturmak için giriş yapın' })
      // redirect to login preserving current path
      const current = router.currentRoute.value.fullPath
      router.push({ path: '/auth/login', query: { redirect: current } })
      return
    }
    if (type !== 'employer') {
      Notify.create({ type: 'warning', message: 'Sadece işverenler ilan oluşturabilir' })
      return
    }
  } catch (e) { void e }
  
  try {
    const empId = localStorage.getItem('employer_id')
    const empName = localStorage.getItem('employer_name')
    if (empId && !form.value.isveren_id) {
      const n = Number(empId)
      if (!Number.isNaN(n)) form.value.isveren_id = n
    }
    if (empName && !form.value.sirket_adi) {
      form.value.sirket_adi = empName
    }
  } catch (e) { void e }
  createOpen.value = true
}

async function submitCreate () {
  submitting.value = true
  try {
    const payload = { ...form.value }
    // Convert empty strings to null for optional fields
    Object.keys(payload).forEach(k => { if (payload[k] === '') payload[k] = null })
    if (!payload.isveren_id || !payload.sirket_adi || !payload.baslik || !payload.aciklama) {
      Notify.create({ type: 'warning', message: 'Lütfen zorunlu alanları doldurun' })
      return
    }
    await api.post('/is_ilanlari', payload)
    Notify.create({ type: 'positive', message: 'İlan oluşturuldu' })
    createOpen.value = false
    resetForm()
    await fetchJobs()
  } catch (err) {
    console.error('Create job failed', err)
    const backendMsg = err?.response?.data?.message
    Notify.create({ type: 'negative', message: backendMsg || 'İlan oluşturulamadı' })
  } finally {
    submitting.value = false
  }
}

function resetForm () {
  form.value = {
    isveren_id: '',
    sirket_adi: '',
    baslik: '',
    aciklama: '',
    konum: '',
    uzaktan_mi: false,
    maas: null,
    para_birimi: 'TRY',
    deneyim_duzeyi: '',
    son_tarih: ''
  }
}

// goDetail is defined below with router navigation

onMounted(async () => {
  await fetchJobs()
  try {
    if (localStorage.getItem('open_create_job') === '1') {
      localStorage.removeItem('open_create_job')
      openCreate()
    }
  } catch (e) { void e }
})
function onChangePage () {
  fetchJobs(false)
}

function goDetail (id) {
  try {
    router.push({ path: `/jobs/${id}` })
  } catch (e) { void e }
}

// Apply flow
function openApplyFromList (item) {
  try {
    const token = localStorage.getItem('api_token')
    const type = localStorage.getItem('auth_type')
    if (!token) {
      Notify.create({ type: 'warning', message: 'Başvuru için giriş yapın' })
      const current = router.currentRoute.value.fullPath
      router.push({ path: '/auth/login', query: { redirect: current } })
      return
    }
    if (type !== 'candidate') {
      Notify.create({ type: 'warning', message: 'Sadece adaylar başvurabilir' })
      return
    }
  } catch { /* ignore */ }
  selectedJobId.value = item?.id || null
  applyOpen.value = true
}

async function submitApply () {
  if (!selectedJobId.value) { applyOpen.value = false; return }
  applySubmitting.value = true
  try {
    const payload = { is_ilani_id: selectedJobId.value, on_yazi: coverLetter.value || '' }
    await api.post('/basvurular', payload)
    Notify.create({ type: 'positive', message: 'Başvuru gönderildi' })
    applyOpen.value = false
    coverLetter.value = ''
  } catch (err) {
    console.error('apply failed', err?.response?.data || err)
    const msg = err?.response?.data?.message || 'Başvuru gönderilemedi'
    Notify.create({ type: 'negative', message: msg })
  } finally { applySubmitting.value = false }
}

// react to filter changes
watch(() => ({ ...filters.value }), () => {
  debouncedFetch()
})

// sidebar quick category apply
function quickCategory (t) {
  filters.value.q = t
  debouncedFetch()
}

function moreTips () {
  Notify.create({ type: 'info', message: 'Yakında: daha fazla kariyer içeriği!' })
}

function uploadCv () {
  uploadOpen.value = true
}

function onCvUploaded () {
  try {
    localStorage.setItem('cv_uploaded_at', String(Date.now()))
  } catch { /* ignore */ }
}

function openJob (id) {
  try { router.push({ path: `/jobs/${id}` }) } catch { /* ignore */ }
}

// no table; using list + q-pagination
</script>

<style scoped>
.hero {
  background: linear-gradient(180deg, #ffffff 0%, #f6f7fb 100%);
}
.job-row {
  border: 1px solid #e8eaf0;
  border-radius: 10px;
}
.job-row:hover {
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}
.job-logo {
  background: #f2f5ff;
}
</style>
