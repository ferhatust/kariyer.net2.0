<template>
  <q-page class="q-pa-md">
    <div class="row q-col-gutter-md">
      <div class="col-12 col-md-8">
        <q-card flat bordered class="bg-white">
          <q-card-section class="row items-center">
            <div class="text-h6">Profil</div>
            <q-space />
            <q-chip square color="grey-3" text-color="grey-8">{{ roleLabel }}</q-chip>
          </q-card-section>
          <q-separator />
          <q-card-section>
            <q-banner v-if="!token" class="bg-orange-2 text-orange-9 q-mb-md" rounded>
              Profil düzenlemek için giriş yapın.
            </q-banner>

            <q-form @submit.prevent="onSave" v-if="token">
              <div class="row q-col-gutter-md" v-if="isEmployer">
                <div class="col-12 col-sm-6">
                  <q-input v-model="employer.sirket_adi" label="Şirket Adı" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="employer.e_posta" type="email" label="E-posta" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="employer.konum" label="Konum" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="employer.sektor" label="Sektör" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="employer.website" label="Website" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="employer.telefon" label="Telefon" filled />
                </div>
                <div class="col-12">
                  <q-input v-model="employer.hakkinda" type="textarea" autogrow label="Şirket Hakkında" filled />
                </div>
                <div class="col-12">
                  <q-input v-model="employer.sifre" type="password" label="Şifre (değiştirmek için)" filled />
                </div>
              </div>

              <div class="row q-col-gutter-md" v-else>
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidate.ad" label="Ad" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidate.soyad" label="Soyad" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidate.e_posta" type="email" label="E-posta" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidate.telefon" label="Telefon" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidate.okudugu_egitim" label="Okuduğu Eğitim" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidate.eski_calistigi_yer" label="Eski Çalıştığı Yer" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidate.deneyim_duzeyi" label="Deneyim Düzeyi" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidate.sifre" type="password" label="Şifre (değiştirmek için)" filled />
                </div>
              </div>

              <div class="row q-mt-md">
                <div class="col-12">
                  <q-btn :loading="saving" type="submit" color="primary" unelevated icon="save" label="Kaydet" />
                </div>
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-4">
        <q-card flat bordered class="bg-white">
          <q-card-section class="text-h6">Hızlı Aksiyonlar</q-card-section>
          <q-separator />
          <q-card-section>
            <q-btn outline color="primary" icon="work" label="İş İlanlarını Gör" to="/jobs" class="full-width q-mb-sm" />
            <q-btn outline color="primary" icon="assignment" label="Başvurularımı Gör" to="/applications" class="full-width q-mb-sm" />
            <q-btn v-if="isEmployer" color="primary" unelevated icon="add" label="Yeni İlan Oluştur" class="full-width" @click="goCreateJob" />
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>

</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Notify } from 'quasar'
import { api } from 'boot/axios'

const router = useRouter()
const token = ref(localStorage.getItem('api_token') || '')
const authType = ref(localStorage.getItem('auth_type') || '')

const isEmployer = computed(() => authType.value === 'employer')
const roleLabel = computed(() => isEmployer.value ? 'İşveren' : (authType.value === 'candidate' ? 'Aday' : 'Bilinmiyor'))

const employer = ref({ sirket_adi: '', e_posta: '', konum: '', sektor: '', website: '', telefon: '', hakkinda: '', sifre: '' })
const candidate = ref({ ad: '', soyad: '', e_posta: '', telefon: '', okudugu_egitim: '', eski_calistigi_yer: '', deneyim_duzeyi: '', sifre: '' })
const saving = ref(false)

async function loadProfile () {
  if (!token.value) return
  try {
    const url = isEmployer.value ? '/isverenler/me' : '/kullanicilar/me'
    const { data } = await api.get(url)
    if (isEmployer.value) {
      employer.value = { ...employer.value, ...data, sifre: '' }
    } else {
      candidate.value = { ...candidate.value, ...data, sifre: '' }
    }
  } catch (err) {
    console.error('Profil yüklenemedi', err?.response?.data || err)
    Notify.create({ type: 'negative', message: 'Profil yüklenemedi' })
  }
}

async function onSave () {
  saving.value = true
  try {
    const url = isEmployer.value ? '/isverenler/me' : '/kullanicilar/me'
    const payload = isEmployer.value ? { ...employer.value } : { ...candidate.value }
    if (!payload.sifre) delete payload.sifre
    const { data } = await api.patch(url, payload)
    Notify.create({ type: 'positive', message: 'Profil güncellendi' })
    // refresh fields (server may have mutated)
    if (isEmployer.value) {
      employer.value = { ...employer.value, ...data, sifre: '' }
    } else {
      candidate.value = { ...candidate.value, ...data, sifre: '' }
    }
  } catch (err) {
    console.error('Kaydetme hatası', err?.response?.data || err)
    Notify.create({ type: 'negative', message: 'Kaydetme başarısız' })
  } finally {
    saving.value = false
  }
}

function goCreateJob () {
  if (!isEmployer.value) return
  try { localStorage.setItem('open_create_job', '1') } catch (e) { void e }
  router.push('/jobs')
}

onMounted(loadProfile)
</script>
