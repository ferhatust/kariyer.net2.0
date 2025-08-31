<template>
  <div class="job-card q-pa-md q-mb-sm bg-white" @click="emitDetail" role="button" tabindex="0" @keydown.enter.prevent="emitDetail">
    <div class="row items-center no-wrap">
      <div class="col-auto">
        <q-avatar size="48px" rounded class="job-logo">
          <img :src="item.logo_url || '/icons/favicon-32x32.png'" alt="logo" loading="lazy" />
        </q-avatar>
      </div>
      <div class="col">
        <div class="row items-center no-wrap">
          <div class="col">
            <div class="text-subtitle1 text-weight-medium ellipsis">{{ item.baslik }}</div>
            <div class="text-caption text-grey-7">{{ item.sirket_adi }} • {{ item.konum || 'Belirtilmedi' }}</div>
          </div>
          <div class="col-auto text-right">
            <q-btn :icon="isBookmarked(item.id) ? 'bookmark' : 'bookmark_border'" flat dense class="q-mr-xs" @click.stop="toggleBookmark(item.id)" :aria-label="isBookmarked(item.id) ? 'Yer işaretinden kaldır' : 'Yer işaretine ekle'" />
            <q-btn color="primary" dense unelevated label="Başvur" @click.stop="emitApply" />
          </div>
        </div>
        <div class="q-mt-xs text-body2 text-grey-8">
          {{ snippet }}<span v-if="(item.aciklama||'').length>maxChars">...</span>
        </div>
        <div class="q-mt-sm row items-center">
          <q-chip v-if="item.uzaktan_mi" size="sm" color="blue-1" text-color="primary" icon="home_work">Uzaktan</q-chip>
          <q-chip v-if="item.deneyim_duzeyi" size="sm" color="grey-2" text-color="grey-9" icon="trending_up">{{ item.deneyim_duzeyi }}</q-chip>
          <q-chip v-if="item.maas" size="sm" color="green-1" text-color="green-8" icon="payments">{{ item.maas }} {{ item.para_birimi || '' }}</q-chip>
          <q-space />
          <q-btn flat color="primary" dense label="Detay" @click.stop="emitDetail" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Notify } from 'quasar'

const props = defineProps({
  item: { type: Object, required: true },
  maxChars: { type: Number, default: 140 }
})
const emit = defineEmits(['detail', 'apply'])

const snippet = computed(() => (props.item.aciklama || '').slice(0, props.maxChars))

function emitDetail () { emit('detail', props.item.id) }
function emitApply () { emit('apply', props.item) }

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
</script>

<style scoped>
.job-card {
  border: 1px solid #e8eaf0;
  border-radius: 10px;
  transition: box-shadow 0.2s ease;
}
.job-card:hover, .job-card:focus {
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
  outline: none;
}
.job-logo { background: #f2f5ff; }
</style>
