<template>
  <q-page class="q-pa-md flex flex-center">
    <q-card flat bordered style="width: 100%; max-width: 640px">
      <q-card-section>
        <div class="text-h6">Kayıt Ol</div>
        <div class="text-caption text-grey-7">Hesap tipinizi seçin</div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <q-tabs v-model="tab" class="text-primary" inline-label>
          <q-tab name="employer" icon="business" label="İşveren" />
          <q-tab name="candidate" icon="person" label="Aday" />
        </q-tabs>
        <q-separator />
        <q-tab-panels v-model="tab" animated>
          <q-tab-panel name="employer">
            <q-form @submit.prevent="registerEmployer">
              <div class="row q-col-gutter-md">
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="employer.sirket_adi" label="Şirket Adı" filled :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="employer.e_posta" type="email" label="E-posta" filled :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="employer.sifre" type="password" label="Şifre" filled :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="employer.konum" label="Konum" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="employer.telefon" label="Telefon" filled />
                </div>
                <div class="col-12">
                  <q-input dense v-model="employer.hakkinda" type="textarea" autogrow label="Hakkında" filled />
                </div>
              </div>
              <div class="q-mt-md">
                <q-btn :loading="loading" type="submit" color="primary" unelevated label="Kayıt Ol" class="full-width" />
              </div>
            </q-form>
          </q-tab-panel>
          <q-tab-panel name="candidate">
            <q-form @submit.prevent="registerCandidate">
              <div class="row q-col-gutter-md">
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="candidate.ad" label="Ad" filled :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="candidate.soyad" label="Soyad" filled :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="candidate.e_posta" type="email" label="E-posta" filled :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="candidate.sifre" type="password" label="Şifre" filled :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="candidate.telefon" label="Telefon" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="candidate.okudugu_egitim" label="Eğitim" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="candidate.eski_calistigi_yer" label="Son İş" filled />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense v-model="candidate.deneyim_duzeyi" label="Deneyim Düzeyi" filled />
                </div>
              </div>
              <div class="q-mt-md">
                <q-btn :loading="loading" type="submit" color="primary" unelevated label="Kayıt Ol" class="full-width" />
              </div>
            </q-form>
          </q-tab-panel>
        </q-tab-panels>
      </q-card-section>
      <q-separator />
      <q-card-section class="text-caption text-center">
        Hesabınız var mı?
        <q-btn flat color="primary" to="/auth/login" label="Giriş yap" />
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Dialog } from 'quasar'
import { api } from 'boot/axios'

const router = useRouter()
const tab = ref('employer')
const loading = ref(false)

const employer = ref({ sirket_adi: '', e_posta: '', sifre: '', konum: '', telefon: '', hakkinda: '' })
const candidate = ref({ ad: '', soyad: '', e_posta: '', sifre: '', telefon: '', okudugu_egitim: '', eski_calistigi_yer: '', deneyim_duzeyi: '' })

async function registerEmployer () {
  loading.value = true
  try {
    const created = await api.post('/isverenler', employer.value)
    // auto-login
    const { data } = await api.post('/isverenler/login', { e_posta: employer.value.e_posta, sifre: employer.value.sifre })
    localStorage.setItem('api_token', data.token)
    localStorage.setItem('auth_type', 'employer')
    // persist employer info for job creation defaults
    try {
      const emp = created?.data
      if (emp?.id) localStorage.setItem('employer_id', String(emp.id))
      if (emp?.sirket_adi) localStorage.setItem('employer_name', emp.sirket_adi)
    } catch { /* ignore */ }
    Dialog.create({ title: 'Başarılı', message: 'Kayıt başarılı. Admin panele yönlendiriliyorsunuz.' })
    router.push('/admin')
  } catch (err) {
    const resp = err?.response?.data
    const msg = resp?.message || (resp && typeof resp === 'object' ? JSON.stringify(resp) : 'Kayıt başarısız')
    console.error('Employer register/login failed', resp || err)
    Dialog.create({ title: 'Kayıt Hatası', message: msg })
  } finally {
    loading.value = false
  }
}

async function registerCandidate () {
  loading.value = true
  try {
    await api.post('/kullanicilar', candidate.value)
    // auto-login
    const { data } = await api.post('/kullanicilar/login', { e_posta: candidate.value.e_posta, sifre: candidate.value.sifre })
    localStorage.setItem('api_token', data.token)
    localStorage.setItem('auth_type', 'candidate')
    Dialog.create({ title: 'Başarılı', message: 'Kayıt başarılı. İş ilanlarına yönlendiriliyorsunuz.' })
    router.push('/jobs')
  } catch (err) {
    const resp = err?.response?.data
    const msg = resp?.message || (resp && typeof resp === 'object' ? JSON.stringify(resp) : 'Kayıt başarısız')
    console.error('Candidate register/login failed', resp || err)
    Dialog.create({ title: 'Kayıt Hatası', message: msg })
  } finally {
    loading.value = false
  }
}
</script>
