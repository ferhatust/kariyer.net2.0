<template>
  <q-page class="q-pa-md">
    <div class="container">
      <q-breadcrumbs class="q-mb-md">
        <q-breadcrumbs-el label="İlanlar" icon="work" to="/jobs" />
        <q-breadcrumbs-el :label="job?.baslik || 'Detay'" />
      </q-breadcrumbs>

      <q-card flat bordered class="bg-white q-mb-md">
        <q-card-section class="row items-center">
          <div class="col-auto">
            <q-avatar size="56px" rounded class="job-logo q-mr-md">
              <img :src="job?.logo_url || '/icons/favicon-32x32.png'" alt="logo" />
            </q-avatar>
          </div>
          <div class="col">
            <div class="text-h6">{{ job?.baslik }}</div>
            <div class="text-caption text-grey-7">{{ job?.sirket_adi }} • {{ job?.konum || 'Belirtilmedi' }}</div>
          </div>
          <div class="col-auto">
            <q-btn :icon="isBookmarked(job?.id) ? 'bookmark' : 'bookmark_border'" flat dense class="q-mr-xs" @click="toggleBookmark(job?.id)" />
            <q-btn color="primary" unelevated label="Başvur" @click="openApply(job)" />
          </div>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <div class="q-mb-sm">
            <q-chip v-if="job?.uzaktan_mi" size="sm" color="blue-1" text-color="primary" icon="home_work">Uzaktan</q-chip>
            <q-chip v-if="job?.deneyim_duzeyi" size="sm" color="grey-2" text-color="grey-9" icon="trending_up">{{ job?.deneyim_duzeyi }}</q-chip>
            <q-chip v-if="job?.maas" size="sm" color="green-1" text-color="green-8" icon="payments">{{ job?.maas }} {{ job?.para_birimi || '' }}</q-chip>
          </div>
          <div class="text-body1" style="white-space: pre-line">{{ job?.aciklama }}</div>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="bg-white">
        <q-card-section class="text-subtitle2">Benzer İlanlar</q-card-section>
        <q-separator />
        <q-list>
          <q-item v-for="rel in related" :key="rel.id" clickable @click="goDetail(rel.id)">
            <q-item-section avatar><q-avatar rounded size="32px"><img :src="rel.logo_url || '/icons/favicon-32x32.png'"/></q-avatar></q-item-section>
            <q-item-section>{{ rel.baslik }}</q-item-section>
            <q-item-section side class="text-caption text-grey-7">{{ rel.sirket_adi }}</q-item-section>
          </q-item>
        </q-list>
      </q-card>

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
            <q-btn color="primary" :loading="submitting" unelevated label="Gönder" @click="submitApply" />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from 'boot/axios'
import { Notify } from 'quasar'

const route = useRoute()
const router = useRouter()
const id = Number(route.params.id)

const job = ref(null)
const related = ref([])

const applyOpen = ref(false)
const coverLetter = ref('')
const submitting = ref(false)

function isBookmarked (jobId) {
  try {
    const raw = localStorage.getItem('bookmarks')
    const set = raw ? new Set(JSON.parse(raw)) : new Set()
    return set.has(jobId)
  } catch { return false }
}
function toggleBookmark (jobId) {
  try {
    const raw = localStorage.getItem('bookmarks')
    const set = raw ? new Set(JSON.parse(raw)) : new Set()
    if (set.has(jobId)) set.delete(jobId); else set.add(jobId)
    localStorage.setItem('bookmarks', JSON.stringify(Array.from(set)))
    Notify.create({ type: 'positive', message: set.has(jobId) ? 'Kaydedildi' : 'Kayıt kaldırıldı' })
  } catch (e) { console.error(e) }
}

function openApply () {
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
  } catch (e) { void e }
  applyOpen.value = true
}

async function submitApply () {
  submitting.value = true
  try {
    const payload = { is_ilani_id: id, on_yazi: coverLetter.value || '' }
    await api.post('/basvurular', payload)
    Notify.create({ type: 'positive', message: 'Başvuru gönderildi' })
    applyOpen.value = false
    coverLetter.value = ''
  } catch (err) {
    console.error('apply failed', err?.response?.data || err)
    const msg = err?.response?.data?.message || 'Başvuru gönderilemedi'
    Notify.create({ type: 'negative', message: msg })
  } finally { submitting.value = false }
}

function goDetail (toId) { router.push({ path: `/jobs/${toId}` }) }

onMounted(async () => {
  try {
    const { data } = await api.get(`/is_ilanlari/${id}`)
    job.value = data
  } catch (e) { console.error(e) }
  try {
    const { data } = await api.get('/is_ilanlari', { params: { page: 1, per_page: 5 } })
    related.value = (data.data || data).filter(j => j.id !== id).slice(0, 5)
  } catch (e) { console.error(e) }
})
</script>

<style scoped>
.container { max-width: 1100px; margin: 0 auto; }
.job-logo { background: #f2f5ff; }
</style>
