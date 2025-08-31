<template>
  <q-page padding>
    <q-card flat bordered class="bg-white">
      <q-card-section class="row items-center">
        <div class="text-h6">Kayıtlar</div>
        <q-space />
        <q-btn outline color="primary" icon="refresh" label="Yenile" @click="fetchAll" :loading="loading" />
      </q-card-section>
      <q-separator />
      <q-card-section>
        <q-tabs v-model="tab" active-color="primary" class="text-primary q-mb-md" inline-label>
          <q-tab name="employers" icon="business" label="İşverenler" />
          <q-tab name="candidates" icon="person" label="Adaylar" />
        </q-tabs>

        <q-tab-panels v-model="tab" animated>
          <q-tab-panel name="employers">
            <q-table
              :rows="employers"
              :columns="employerCols"
              row-key="id"
              flat
              bordered
              :loading="loading"
              no-data-label="Kayıt bulunamadı"
            />
          </q-tab-panel>

          <q-tab-panel name="candidates">
            <q-table
              :rows="candidates"
              :columns="candidateCols"
              row-key="id"
              flat
              bordered
              :loading="loading"
              no-data-label="Kayıt bulunamadı"
            />
          </q-tab-panel>
        </q-tab-panels>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from 'boot/axios'
import { Notify } from 'quasar'

const tab = ref('employers')
const loading = ref(false)
const employers = ref([])
const candidates = ref([])

const employerCols = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'sirket_adi', label: 'Şirket', field: 'sirket_adi', align: 'left' },
  { name: 'e_posta', label: 'E-posta', field: 'e_posta', align: 'left' },
  { name: 'telefon', label: 'Telefon', field: 'telefon', align: 'left' },
  { name: 'konum', label: 'Konum', field: 'konum', align: 'left' }
]

const candidateCols = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'ad', label: 'Ad', field: 'ad', align: 'left' },
  { name: 'soyad', label: 'Soyad', field: 'soyad', align: 'left' },
  { name: 'e_posta', label: 'E-posta', field: 'e_posta', align: 'left' },
  { name: 'telefon', label: 'Telefon', field: 'telefon', align: 'left' }
]

async function fetchAll () {
  loading.value = true
  try {
    const [empRes, candRes] = await Promise.all([
      api.get('/isverenler'),
      api.get('/kullanicilar')
    ])
    const empData = empRes.data?.data || empRes.data || []
    const candData = candRes.data?.data || candRes.data || []
    employers.value = Array.isArray(empData) ? empData : []
    candidates.value = Array.isArray(candData) ? candData : []
  } catch (err) {
    console.error('Records fetch failed', err?.response?.data || err)
    Notify.create({ type: 'negative', message: 'Kayıtlar yüklenemedi' })
  } finally {
    loading.value = false
  }
}

onMounted(fetchAll)
</script>

<style scoped>
.q-card.bg-white { border-radius: 12px; }
</style>
