<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="bg-white">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md text-h6">Başvurular</div>
        <div class="col-12 col-md-auto row items-center q-gutter-sm">
          <q-select v-model="statusFilter" :options="durumOptions" option-label="label" option-value="value" emit-value map-options clearable dense filled label="Durum Filtre" style="min-width: 200px" />
          <q-btn color="primary" unelevated icon="add" label="Yeni Başvuru" v-if="isCandidate" @click="openCreate()" />
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
          @row-click="(_, row) => openDetail(row)"
        >
          <template #body-cell-durum="props">
            <q-td :props="props">
              <q-badge :color="statusColor(props.row.durum)" align="middle">{{ props.row.durum || '—' }}</q-badge>
            </q-td>
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
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { api } from 'boot/axios'
import { Notify } from 'quasar'

const rows = ref([])
const loading = ref(false)
const page = ref(1)
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

const authType = (typeof localStorage !== 'undefined' && localStorage.getItem('auth_type')) || null
const isCandidate = authType === 'candidate'
const isEmployer = authType === 'employer'
const statusFilter = ref(null)

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'is_ilani', label: 'İlan', field: row => row.isIlani?.baslik || row.is_ilani_id, align: 'left' },
  { name: 'kullanici', label: 'Aday', field: row => row.kullanici ? `${row.kullanici.ad} ${row.kullanici.soyad}` : row.kullanici_id, align: 'left' },
  { name: 'durum', label: 'Durum', field: 'durum', align: 'left' },
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

const displayRows = computed(() => {
  if (!statusFilter.value) return rows.value
  return rows.value.filter(r => r.durum === statusFilter.value)
})

async function fetchData () {
  loading.value = true
  try {
    const endpoint = isCandidate ? '/basvurular/me' : '/basvurular'
    const { data } = await api.get(endpoint, { params: { page: page.value } })
    rows.value = data.data || data
    pagination.value = data
  } finally {
    loading.value = false
  }
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
