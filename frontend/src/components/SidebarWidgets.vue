<template>
  <aside class="sidebar">
    <!-- CV Upload CTA -->
    <q-card flat bordered class="widget-card q-mb-md fade-in">
      <q-card-section class="row items-center">
        <q-avatar color="purple-6" text-color="white" size="40px" class="q-mr-md"><q-icon name="description"/></q-avatar>
        <div>
          <div class="text-subtitle2">CV’nizi Yükleyin</div>
          <div class="text-caption text-grey-7">Daha hızlı başvuru için CV’nizi ekleyin.</div>
        </div>
        <q-space />
        <q-btn color="primary" unelevated label="Yükle" @click="emit('upload-cv')" />
      </q-card-section>
    </q-card>

    <!-- Saved Jobs -->
    <q-card flat bordered class="widget-card q-mb-md fade-in">
      <q-card-section class="row items-center">
        <q-avatar icon="bookmark" color="teal-6" text-color="white" size="32px" class="q-mr-sm" />
        <div class="text-subtitle2">Kaydedilen İlanlar</div>
      </q-card-section>
      <q-separator />
      <q-list v-if="savedJobs.length" class="compact-list">
        <q-item v-for="j in savedJobs" :key="j.id" clickable @click="emit('open-job', j.id)">
          <q-item-section avatar>
            <q-avatar rounded size="28px"><img :src="j.logo_url || '/icons/favicon-32x32.png'"/></q-avatar>
          </q-item-section>
          <q-item-section>
            <div class="text-body2 ellipsis">{{ j.baslik }}</div>
            <div class="text-caption text-grey-7 ellipsis">{{ j.sirket_adi }}</div>
          </q-item-section>
          <q-item-section side class="row items-center no-wrap">
            <q-btn dense round flat color="grey-7" icon="bookmark_remove" @click.stop="removeSaved(j.id)" :aria-label="`Kaydı kaldır: ${j.baslik}`" />
            <q-icon name="chevron_right" color="grey-6" class="q-ml-xs"/>
          </q-item-section>
        </q-item>
      </q-list>
      <div v-else class="text-caption text-grey-7 q-pa-sm">Henüz kaydedilmiş ilan yok.</div>
    </q-card>

    <!-- Recent Searches -->
    <q-card flat bordered class="widget-card q-mb-md fade-in">
      <q-card-section class="row items-center">
        <q-avatar icon="history" color="blue-6" text-color="white" size="32px" class="q-mr-sm" />
        <div class="text-subtitle2">Son Aramalar</div>
        <q-space />
        <q-btn v-if="recent.length" flat dense color="grey-7" icon="delete" label="Temizle" @click="clearRecent" />
      </q-card-section>
      <q-separator />
      <div class="q-pa-sm">
        <q-chip v-for="(s, i) in recent" :key="i" clickable outline color="primary" class="q-mr-sm q-mb-sm" @click="onSelect(s)">{{ s }}</q-chip>
        <div v-if="!recent.length" class="text-caption text-grey-7">Arama geçmişiniz burada görünecek.</div>
      </div>
    </q-card>

    <!-- Popular Searches -->
    <q-card flat bordered class="widget-card q-mb-md fade-in">
      <q-card-section class="row items-center">
        <q-avatar icon="search" color="primary" text-color="white" size="32px" class="q-mr-sm" />
        <div class="text-subtitle2">Popüler Aramalar</div>
      </q-card-section>
      <q-separator />
      <q-list separator class="fancy-list">
        <q-item v-for="t in popular" :key="t" clickable @click="onSelect(t)" class="fancy-item">
          <q-item-section avatar>
            <q-avatar size="28px" color="blue-1" text-color="primary"><q-icon name="tag"/></q-avatar>
          </q-item-section>
          <q-item-section>{{ t }}</q-item-section>
          <q-item-section side><q-icon name="chevron_right" color="grey-6"/></q-item-section>
        </q-item>
      </q-list>
    </q-card>

    <!-- Tips -->
    <q-card flat bordered class="widget-card fade-in">
      <q-card-section class="row items-center">
        <q-avatar icon="lightbulb" color="amber-7" text-color="white" size="32px" class="q-mr-sm" />
        <div class="text-subtitle2">Kariyer İpuçları</div>
      </q-card-section>
      <q-separator />
      <q-list class="tips-list">
        <q-item v-for="(tip, i) in tips" :key="i" class="tip-item">
          <q-item-section>
            <div class="row no-wrap items-start">
              <q-icon name="check_circle" color="green-6" size="18px" class="q-mr-sm q-mt-xs" />
              <div class="text-body2">{{ tip }}</div>
            </div>
          </q-item-section>
        </q-item>
      </q-list>
      <q-separator />
      <q-card-actions align="right" class="q-pa-sm">
        <q-btn flat dense color="primary" icon="open_in_new" label="Daha Fazla" @click="emit('more-tips')" />
      </q-card-actions>
    </q-card>
  </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from 'boot/axios'

const { popular, tips } = defineProps({
  popular: { type: Array, default: () => [] },
  tips: { type: Array, default: () => [] }
})
const emit = defineEmits(['select', 'more-tips', 'upload-cv', 'open-job'])

const savedJobs = ref([])
const recent = ref([])

function onSelect (t) { emit('select', t) }

function readRecent () {
  try {
    const raw = localStorage.getItem('recent_searches')
    const arr = raw ? JSON.parse(raw) : []
    recent.value = Array.isArray(arr) ? arr.slice(0, 10) : []
  } catch { recent.value = [] }
}

async function readSavedJobs () {
  try {
    const raw = localStorage.getItem('bookmarks')
    const ids = raw ? JSON.parse(raw) : []
    if (!Array.isArray(ids) || !ids.length) { savedJobs.value = []; return }
    const first = ids.slice(0, 5)
    const results = []
    for (const id of first) {
      try {
        const { data } = await api.get(`/is_ilanlari/${id}`)
        results.push(data)
      } catch { /* ignore missing */ }
    }
    savedJobs.value = results
  } catch { savedJobs.value = [] }
}

function clearRecent () {
  try {
    localStorage.removeItem('recent_searches')
  } catch { /* ignore */ }
  recent.value = []
}

function removeSaved (id) {
  try {
    const raw = localStorage.getItem('bookmarks')
    const arr = raw ? JSON.parse(raw) : []
    const list = Array.isArray(arr) ? arr : []
    const next = list.filter(x => String(x) !== String(id))
    localStorage.setItem('bookmarks', JSON.stringify(next))
  } catch { /* ignore */ }
  savedJobs.value = savedJobs.value.filter(j => String(j.id) !== String(id))
}

onMounted(() => {
  readRecent()
  readSavedJobs()
  // refresh on storage change
  try {
    window.addEventListener('storage', (e) => {
      if (e.key === 'recent_searches') readRecent()
      if (e.key === 'bookmarks') readSavedJobs()
    })
  } catch { /* noop */ }
})
</script>

<style scoped>
.sidebar { position: sticky; top: 12px; }
.widget-card {
  border: 1px solid #e8eaf0;
  border-radius: 14px;
  overflow: hidden;
  background: linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
  box-shadow: 0 8px 24px rgba(0,0,0,0.04);
  transition: transform 0.15s ease;
}
.widget-card:hover { transform: translateY(-1px); }
.fancy-list { padding: 4px 0; }
.fancy-item { transition: background 0.2s ease, transform 0.05s ease; }
.fancy-item:hover { background: #f6f7fb; }
.fancy-item:active { transform: translateY(1px); }
.tips-list { padding: 4px 0; }
.tip-item + .tip-item { border-top: 1px dashed #eef0f6; }
.compact-list .q-item { padding: 6px 8px; }
.fade-in { animation: fadeIn 220ms ease both; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(6px);} to { opacity: 1; transform: translateY(0);} }

/* Dark mode tweaks */
:deep(.q-dark) .widget-card,
body.body--dark .widget-card {
  background: linear-gradient(180deg, #1e1f25 0%, #1a1b21 100%);
  border-color: #2a2c35;
  box-shadow: 0 8px 24px rgba(0,0,0,0.3);
}
:deep(.q-dark) .fancy-item:hover,
body.body--dark .fancy-item:hover { background: rgba(255,255,255,0.04); }
.ellipsis { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>
