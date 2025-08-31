<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="apps-card">
      <!-- Header & Actions -->
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md text-h6">Başvurular</div>
        <div class="col-12 col-md-auto row items-center q-gutter-sm">
          <q-input v-model="searchTerm" dense filled clearable placeholder="Ara (İlan / Aday)" class="apps-search">
            <template #prepend><q-icon name="search" /></template>
          </q-input>
          <q-select v-model="statusFilter" :options="durumOptions" option-label="label" option-value="value" emit-value map-options clearable dense filled label="Durum" style="min-width: 160px" />
          <q-select v-model="jobFilter" :options="jobOptions" option-label="label" option-value="value" emit-value map-options clearable dense filled label="İlan" style="min-width: 200px" />
          <!-- Date range filters -->
          <q-input v-model="fromDate" dense filled clearable label="Tarih (Başlangıç)" mask="####-##-##" style="max-width: 160px">
            <template #append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy transition-show="scale" transition-hide="scale">
                  <q-date v-model="fromDate" mask="YYYY-MM-DD" />
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-input v-model="toDate" dense filled clearable label="Tarih (Bitiş)" mask="####-##-##" style="max-width: 160px">
            <template #append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy transition-show="scale" transition-hide="scale">
                  <q-date v-model="toDate" mask="YYYY-MM-DD" />
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-btn flat round dense icon="refresh" :loading="loading" @click="refresh" :aria-label="'Yenile'" />
          <q-btn flat round dense icon="filter_alt_off" @click="resetFilters" :aria-label="'Filtreleri Temizle'" />
          <q-btn color="primary" unelevated icon="add" label="Yeni Başvuru" v-if="isCandidate" @click="openCreate()" />
          <q-select v-model="perPage" :options="[10,20,50]" dense filled label="Sayfa" style="width: 90px" />
          <q-select v-model="sortBy" :options="sortByOptions" dense filled label="Sırala" style="width: 140px" />
          <q-select v-model="sortDir" :options="sortDirOptions" dense filled label="Yön" style="width: 110px" />
        </div>
      </q-card-section>
      <!-- Status Summary -->
      <q-card-section class="row q-col-gutter-sm">
        <div class="col-auto">
          <q-chip square color="grey-3" text-color="dark" icon="list_alt">Toplam: {{ stats.total }}</q-chip>
        </div>
        <div class="col-auto">
          <q-chip square color="primary" text-color="white" icon="send">Başvuruldu: {{ stats.basvuruldu }}</q-chip>
        </div>
        <div class="col-auto">
          <q-chip square color="warning" text-color="dark" icon="emoji_people">Ön Seçildi: {{ stats.on_secildi }}</q-chip>
        </div>
        <div class="col-auto">
          <q-chip square color="negative" text-color="white" icon="block">Reddedildi: {{ stats.reddedildi }}</q-chip>
        </div>
        <div class="col-auto">
          <q-chip square color="positive" text-color="white" icon="task_alt">İşe Alındı: {{ stats.ise_alindi }}</q-chip>
        </div>
      </q-card-section>
      <!-- Bulk Actions (Employer) -->
      <q-card-section v-if="isEmployer" class="row items-center q-col-gutter-sm">
        <div class="col-auto">
          <q-select v-model="bulkStatus" :options="durumOptions" option-label="label" option-value="value" emit-value map-options dense filled label="Toplu Durum" style="min-width: 180px" />
        </div>
        <div class="col-auto">
          <q-btn color="primary" unelevated :disable="!selectedRows.length || !bulkStatus" label="Uygula" @click="confirmBulk = true" />
        </div>
        <q-space />
        <div class="col-auto">
          <q-btn flat icon="download" label="CSV Dışa Aktar" @click="exportCsv" />
        </div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <q-table
          :rows="displayRows"
          :columns="columns"
          row-key="id"
          flat
          bordered
          :loading="loading"
          :rows-per-page-options="[10,20,50]"
          :selection="isEmployer ? 'multiple' : 'none'"
          v-model:selected="selectedRows"
          class="apps-table"
        >
          <!-- Loading skeletons -->
          <template #loading>
            <div class="q-pa-md">
              <div v-for="i in 6" :key="i" class="row items-center q-col-gutter-md q-mb-sm">
                <div class="col-6"><q-skeleton type="text" width="85%" /></div>
                <div class="col-3"><q-skeleton type="text" width="60%" /></div>
                <div class="col-2"><q-skeleton type="text" width="40%" /></div>
                <div class="col-1"><q-skeleton type="QBtn" /></div>
              </div>
            </div>
          </template>
          <!-- Ilan cell with icon -->
          <template #body-cell-is_ilani="props">
            <q-td :props="props">
              <div class="row items-center no-wrap">
                <q-avatar size="28px" class="q-mr-sm">
                  <img :src="props.row.isIlani?.logo_url || '/icons/favicon-32x32.png'" alt="logo" loading="lazy" />
                </q-avatar>
                <div>
                  <div class="text-body1 ellipsis">{{ props.row.isIlani?.baslik || props.row.is_ilani_id }}</div>
                  <div class="text-caption text-grey-7">#{{ props.row.id }}</div>
                </div>
              </div>
            </q-td>
          </template>

          <!-- Kullanici cell with initials avatar -->
          <template #body-cell-kullanici="props">
            <q-td :props="props">
              <div class="row items-center no-wrap">
                <q-avatar size="28px" color="grey-4" text-color="dark" class="q-mr-sm">
                  {{ initials(props.row.kullanici ? `${props.row.kullanici.ad} ${props.row.kullanici.soyad}` : String(props.row.kullanici_id)) }}
                </q-avatar>
                <div class="ellipsis">
                  {{ props.row.kullanici ? `${props.row.kullanici.ad} ${props.row.kullanici.soyad}` : props.row.kullanici_id }}
                </div>
              </div>
            </q-td>
          </template>

          <!-- Durum badge -->
          <template #body-cell-durum="props">
            <q-td :props="props">
              <q-badge :color="statusColor(props.row.durum)" align="middle">{{ props.row.durum || '—' }}</q-badge>
            </q-td>
          </template>

          <!-- Tarih column -->
          <template #body-cell-tarih="props">
            <q-td :props="props">
              {{ formatDate(props.row.created_at || props.row.updated_at || props.row.tarih || props.row.date) }}
            </q-td>
          </template>

          <!-- Actions column -->
          <template #body-cell-actions="props">
            <q-td :props="props">
              <q-btn dense flat round icon="visibility" @click.stop="openDetail(props.row)" :aria-label="'Detay'" />
            </q-td>
          </template>

          <!-- No data / Empty state -->
          <template #no-data>
            <div class="column items-center q-pa-xl text-grey-7 empty-state">
              <q-icon name="inventory_2" size="48px" class="q-mb-sm" />
              <div class="text-subtitle1 q-mb-xs">Gösterilecek başvuru yok</div>
              <div class="text-caption">Filtreleri temizlemeyi veya yeni başvuru oluşturmayı deneyin.</div>
            </div>
          </template>
        </q-table>
        <div class="q-mt-md flex flex-center" v-if="pagination?.last_page > 1">
          <q-pagination v-model="page" :max="pagination.last_page" @update:model-value="fetchData" />
        </div>
      </q-card-section>
    </q-card>

    <!-- Create Application Dialog (Candidates) -->
    <q-dialog v-model="createOpen" persistent>
      <q-card style="min-width: 600px; max-width: 90vw">
        <q-card-section class="text-h6">Yeni Başvuru</q-card-section>
        <q-separator />
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-sm-6">
              <q-input v-model.number="form.is_ilani_id" type="number" label="İlan ID" filled :rules="[v=>!!v||'Zorunlu']" />
            </div>
            <div class="col-12">
              <q-input v-model="form.on_yazi" type="textarea" autogrow label="Ön Yazı" filled />
            </div>
          </div>
        </q-card-section>
        <q-separator />
        <q-card-actions align="right">
          <q-btn flat label="Vazgeç" v-close-popup />
          <q-btn color="primary" :loading="submitting" unelevated label="Gönder" @click="submitCreate" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Detail Dialog -->
    <q-dialog v-model="detailOpen">
      <q-card style="min-width: 700px; max-width: 95vw">
        <q-card-section class="text-h6">Başvuru Detayı</q-card-section>
        <q-separator />
        <q-card-section v-if="selected">
          <div class="q-mb-sm"><b>İlan:</b> {{ selected.isIlani?.baslik || selected.is_ilani_id }}</div>
          <div class="q-mb-sm"><b>Aday:</b> {{ selected.kullanici ? `${selected.kullanici.ad} ${selected.kullanici.soyad}` : selected.kullanici_id }}</div>
          <div class="q-mb-sm" v-if="!isEmployer"><b>Durum:</b> <q-badge :color="statusColor(selected.durum)">{{ selected.durum || '—' }}</q-badge></div>
          <div class="row items-center q-col-gutter-sm q-mb-sm" v-else>
            <div class="col-auto"><b>Durum:</b></div>
            <div class="col-12 col-sm">
              <q-select v-model="selectedDurum" :options="durumOptions" option-label="label" option-value="value" emit-value map-options dense filled />
            </div>
          </div>
          <div class="q-mt-md">
            <div class="text-subtitle2 q-mb-xs">Ön Yazı</div>
            <div class="text-body2" style="white-space: pre-line">{{ selected.on_yazi || '—' }}</div>
          </div>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Kapat" v-close-popup />
          <q-btn v-if="isEmployer" color="primary" :loading="savingStatus" :disable="!selected || selected.durum === selectedDurum" unelevated label="Durumu Kaydet" @click="submitStatus()" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Confirm Bulk Update Dialog -->
    <q-dialog v-model="confirmBulk">
      <q-card style="min-width: 420px">
        <q-card-section class="text-h6">Toplu Durum Güncelleme</q-card-section>
        <q-separator />
        <q-card-section>
          <div class="q-mb-sm">Seçili kayıt: <b>{{ selectedRows.length }}</b></div>
          <div>Yeni durum: <q-badge :color="statusColor(bulkStatus)">{{ bulkStatus || '—' }}</q-badge></div>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Vazgeç" v-close-popup />
          <q-btn color="primary" unelevated :loading="loading" :disable="!selectedRows.length || !bulkStatus" label="Onayla" @click="onConfirmBulk" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { api } from 'boot/axios'
import { Notify } from 'quasar'

const rows = ref([])
const loading = ref(false)
const page = ref(1)
const perPage = ref(10)
const sortBy = ref(null)
const sortDir = ref('desc')
const pagination = ref(null)
const createOpen = ref(false)
const submitting = ref(false)
const detailOpen = ref(false)
const selected = ref(null)
const durumOptions = [
  { label: 'Başvuruldu', value: 'basvuruldu' },
  { label: 'Ön Seçildi', value: 'on_secildi' },
  { label: 'Reddedildi', value: 'reddedildi' },
  { label: 'İşe Alındı', value: 'ise_alindi' },
]
const form = ref({ is_ilani_id: null, on_yazi: '' })
const searchTerm = ref('')
const fromDate = ref('') // YYYY-MM-DD
const toDate = ref('')   // YYYY-MM-DD
const selectedRows = ref([])
const bulkStatus = ref(null)
const confirmBulk = ref(false)

const authType = (typeof localStorage !== 'undefined' && localStorage.getItem('auth_type')) || null
const isCandidate = authType === 'candidate'
const isEmployer = authType === 'employer'
const statusFilter = ref(null)
const jobFilter = ref(null)

// Sorting UI options
const sortByOptions = [
  { label: 'Tarih', value: 'created_at' },
  { label: 'ID', value: 'id' },
  { label: 'Durum', value: 'status' }
]
const sortDirOptions = [
  { label: 'Azalan', value: 'desc' },
  { label: 'Artan', value: 'asc' }
]

const columns = [
  { name: 'is_ilani', label: 'İlan', field: row => row.isIlani?.baslik || row.is_ilani_id, align: 'left' },
  { name: 'kullanici', label: 'Aday', field: row => row.kullanici ? `${row.kullanici.ad} ${row.kullanici.soyad}` : row.kullanici_id, align: 'left' },
  { name: 'durum', label: 'Durum', field: 'durum', align: 'left' },
  { name: 'tarih', label: 'Tarih', field: row => row.created_at || row.updated_at || row.tarih || row.date, align: 'left' },
  { name: 'actions', label: '', field: 'id', align: 'right' },
]

function statusColor (s) {
  switch (s) {
    case 'basvuruldu': return 'primary'
    case 'on_secildi': return 'warning'
    case 'reddedildi': return 'negative'
    case 'ise_alindi': return 'positive'
    default: return 'grey'
  }
}

function formatDate (val) {
  if (!val) return '—'
  const d = new Date(val)
  if (isNaN(d.getTime())) return String(val)
  const yyyy = d.getFullYear()
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const dd = String(d.getDate()).padStart(2, '0')
  const hh = String(d.getHours()).padStart(2, '0')
  const mi = String(d.getMinutes()).padStart(2, '0')
  return `${yyyy}-${mm}-${dd} ${hh}:${mi}`
}

function onConfirmBulk () {
  confirmBulk.value = false
  applyBulkStatus()
}

const displayRows = computed(() => {
  let list = rows.value
  if (statusFilter.value) list = list.filter(r => r.durum === statusFilter.value)
  if (jobFilter.value) list = list.filter(r => String(r.is_ilani_id) === String(jobFilter.value))
  if (searchTerm.value) {
    const q = searchTerm.value.toLowerCase()
    list = list.filter(r => {
      const ilan = (r.isIlani?.baslik || String(r.is_ilani_id)).toLowerCase()
      const aday = (r.kullanici ? `${r.kullanici.ad} ${r.kullanici.soyad}` : String(r.kullanici_id)).toLowerCase()
      const durum = String(r.durum || '').toLowerCase()
      return ilan.includes(q) || aday.includes(q) || durum.includes(q)
    })
  }
  if (fromDate.value || toDate.value) {
    const fromTs = fromDate.value ? new Date(fromDate.value + 'T00:00:00').getTime() : null
    const toTs = toDate.value ? new Date(toDate.value + 'T23:59:59').getTime() : null
    list = list.filter(r => {
      const tsRaw = r.created_at || r.updated_at || r.tarih || r.date
      if (!tsRaw) return true // if no timestamp, don't exclude
      const ts = new Date(tsRaw).getTime()
      if (Number.isNaN(ts)) return true
      if (fromTs && ts < fromTs) return false
      if (toTs && ts > toTs) return false
      return true
    })
  }
  return list
})

const jobOptions = computed(() => {
  const seen = new Map()
  for (const r of rows.value) {
    const id = r.is_ilani_id
    const title = r.isIlani?.baslik || String(id)
    if (!seen.has(id)) seen.set(id, { label: title, value: id })
  }
  return Array.from(seen.values())
})

const stats = computed(() => {
  const base = { total: rows.value.length, basvuruldu: 0, on_secildi: 0, reddedildi: 0, ise_alindi: 0 }
  for (const r of rows.value) {
    if (r.durum && base[r.durum] !== undefined) base[r.durum]++
  }
  return base
})

async function fetchData () {
  loading.value = true
  try {
    const endpoints = isCandidate
      ? ['/basvurular/me', '/basvurular']
      : ['/basvurular', '/basvurular/me']

    let loaded = false
    let lastErr = null
    for (const ep of endpoints) {
      try {
        const { rows: r, pagination: p } = await loadRowsFrom(ep)
        rows.value = r
        pagination.value = p
        loaded = true
        break
      } catch (err) {
        lastErr = err
        // Continue to next endpoint on auth/not found issues
        const code = err?.response?.status
        if (![401, 403, 404].includes(code)) {
          break
        }
      }
    }
    if (!loaded) {
      throw lastErr || new Error('Veri yüklenemedi')
    }
  } catch (err) {
    console.error('fetchData failed', err)
    Notify.create({ type: 'negative', message: 'Başvurular yüklenemedi' })
  } finally {
    loading.value = false
  }
}

async function loadRowsFrom (endpoint) {
  const params = {
    page: page.value,
    per_page: perPage.value,
    q: searchTerm.value || undefined,
    status: statusFilter.value || undefined,
    job_id: jobFilter.value || undefined,
    from: fromDate.value || undefined,
    to: toDate.value || undefined,
    sort_by: sortBy.value || undefined,
    sort_dir: sortDir.value || undefined
  }
  const { data } = await api.get(endpoint, { params })
  // Normalize common shapes:
  // 1) { data: [...], total, last_page, ... }
  // 2) [...]
  // 3) { items: [...], meta: { ... } }
  let r = []
  let p = null
  if (Array.isArray(data)) {
    r = data
  } else if (data && Array.isArray(data.data)) {
    r = data.data
    p = data
  } else if (data && Array.isArray(data.items)) {
    r = data.items
    p = data.meta || null
  } else {
    // try to detect a single record or unknown shape
    r = []
  }
  return { rows: r, pagination: p }
}

function refresh () { fetchData() }

// Debounce helper
let debounceT = null
function fetchDataDebounced (ms = 300) {
  if (debounceT) window.clearTimeout(debounceT)
  debounceT = window.setTimeout(() => { fetchData() }, ms)
}

function initials (name) {
  const parts = String(name || '').trim().split(/\s+/)
  return (parts[0]?.[0] || '') + (parts[1]?.[0] || '')
}

function resetFilters () {
  searchTerm.value = ''
  statusFilter.value = null
  jobFilter.value = null
  fromDate.value = ''
  toDate.value = ''
}

// persist filters
const FILTERS_KEY = 'apps_filters'
onMounted(() => {
  try {
    const raw = localStorage.getItem(FILTERS_KEY)
    if (raw) {
      const saved = JSON.parse(raw)
      searchTerm.value = saved.searchTerm || ''
      statusFilter.value = saved.statusFilter ?? null
      jobFilter.value = saved.jobFilter ?? null
      fromDate.value = saved.fromDate || ''
      toDate.value = saved.toDate || ''
    }
  } catch { /* ignore */ }
})

// React to filters with debounce to mimic server-side filtering
watch([searchTerm, statusFilter, jobFilter, fromDate, toDate], () => {
  page.value = 1
  fetchDataDebounced()
})
watch(page, () => fetchData())
watch(perPage, () => { page.value = 1; fetchData() })
watch([sortBy, sortDir], () => { page.value = 1; fetchData() })

watch([searchTerm, statusFilter, jobFilter, fromDate, toDate], ([s, st, jf, fd, td]) => {
  try {
    localStorage.setItem(FILTERS_KEY, JSON.stringify({
      searchTerm: s,
      statusFilter: st,
      jobFilter: jf,
      fromDate: fd,
      toDate: td
    }))
  } catch { /* ignore */ }
})

async function applyBulkStatus () {
  if (!isEmployer || !bulkStatus.value || !selectedRows.value.length) return
  try {
    loading.value = true
    const ids = selectedRows.value.map(r => r.id)
    await Promise.all(ids.map(id => api.patch(`/basvurular/${id}`, { durum: bulkStatus.value })))
    // update local rows
    for (const id of ids) {
      const idx = rows.value.findIndex(r => r.id === id)
      if (idx !== -1) rows.value[idx].durum = bulkStatus.value
    }
    Notify.create({ type: 'positive', message: `Toplu durum güncellendi (${ids.length})` })
    selectedRows.value = []
  } catch (err) {
    console.error('Bulk update failed', err)
    Notify.create({ type: 'negative', message: 'Toplu güncelleme başarısız' })
  } finally {
    loading.value = false
  }
}

function exportCsv () {
  const items = selectedRows.value.length ? selectedRows.value : displayRows.value
  const header = ['ID','İlan','Aday','Durum','Tarih']
  const rowsCsv = items.map(r => [
    r.id,
    (r.isIlani?.baslik || String(r.is_ilani_id)).replaceAll('"', '""'),
    (r.kullanici ? `${r.kullanici.ad} ${r.kullanici.soyad}` : String(r.kullanici_id)).replaceAll('"', '""'),
    r.durum || '',
    (r.created_at || r.updated_at || r.tarih || r.date || '').toString().replaceAll('"', '""')
  ].map(x => `"${String(x)}"`).join(','))
  const csv = [header.join(','), ...rowsCsv].join('\n')
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.setAttribute('download', `basvurular_${new Date().toISOString().slice(0,10)}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

function openCreate () {
  createOpen.value = true
}

async function submitCreate () {
  submitting.value = true
  try {
    const payload = { ...form.value }
    await api.post('/basvurular', payload)
    Notify.create({ type: 'positive', message: 'Başvuru oluşturuldu' })
    createOpen.value = false
    form.value = { is_ilani_id: null, on_yazi: '' }
    await fetchData()
  } catch (err) {
    console.error('Create application failed', err)
    Notify.create({ type: 'negative', message: 'Başvuru oluşturulamadı' })
  } finally {
    submitting.value = false
  }
}

const selectedDurum = ref(null)
const savingStatus = ref(false)

function openDetail (row) {
  selected.value = row
  selectedDurum.value = row?.durum || 'basvuruldu'
  detailOpen.value = true
}

async function submitStatus () {
  if (!selected.value) return
  try {
    savingStatus.value = true
    const id = selected.value.id
    await api.patch(`/basvurular/${id}`, { durum: selectedDurum.value })
    // update local row
    selected.value.durum = selectedDurum.value
    const idx = rows.value.findIndex(r => r.id === id)
    if (idx !== -1) rows.value[idx].durum = selectedDurum.value
    Notify.create({ type: 'positive', message: 'Durum güncellendi' })
  } catch (err) {
    console.error('Update status failed', err)
    Notify.create({ type: 'negative', message: 'Durum güncellenemedi' })
  } finally {
    savingStatus.value = false
  }
}

onMounted(fetchData)
</script>

<style scoped>
.apps-search { min-width: 220px; }

/* Light theme defaults (keep subtle) */
.apps-card { background: #fff; }
.apps-table :deep(thead tr) { background: #fafafa; }
.apps-table :deep(tbody tr:hover) { background: rgba(0,0,0,0.03); }

/* Dark theme enhancements */
body.body--dark .apps-card,
:deep(.q-dark) .apps-card {
  background: #1e1f24;
  border-color: #2a2b31 !important;
}
body.body--dark .apps-card :deep(.q-separator),
:deep(.q-dark) .apps-card .q-separator { background: #2a2b31; }

/* Inputs (filled) better contrast in dark */
body.body--dark :deep(.q-field--filled .q-field__control) {
  background: #2a2b31;
}
body.body--dark :deep(.q-field--filled .q-field__native),
body.body--dark :deep(.q-field__label) {
  color: #d6d7dc;
}

/* Table visuals */
body.body--dark .apps-table :deep(thead tr) {
  background: #25262b;
}
body.body--dark .apps-table :deep(tbody tr) {
  border-color: #2f3036;
}
body.body--dark .apps-table :deep(tbody tr:hover) {
  background: rgba(255,255,255,0.04);
}
/* zebra striping for readability */
body.body--dark .apps-table :deep(tbody tr:nth-child(odd)) {
  background: rgba(255,255,255,0.02);
}

/* Chips and badges contrast tweaks */
body.body--dark :deep(.q-chip[color="warning"]) { color: #1e1f24; }
body.body--dark :deep(.q-badge) { filter: saturate(0.95) brightness(0.95); }

/* Empty state */
body.body--dark .empty-state { color: #c7c8cd !important; }
body.body--dark .empty-state :deep(.q-icon) { color: #9aa0a6; }

/* Dialogs inside dark card */
body.body--dark :deep(.q-dialog__inner > .q-card) {
  background: #1f2025;
  color: #e6e7eb;
}
</style>
