<template>
  <q-page>
    <div class="row q-col-gutter-md items-stretch">
      <div class="col-12">
        <q-banner rounded class="bg-primary text-white q-pa-md">
          <div class="text-h6">Hoş geldiniz</div>
          <div class="text-subtitle2">İşveren kontrol paneli</div>
        </q-banner>
      </div>
      <div class="col-12 col-md-4">
        <q-card class="bg-white" flat bordered>
          <q-item>
            <q-item-section avatar><q-icon name="work" color="primary" /></q-item-section>
            <q-item-section>
              <q-item-label class="text-subtitle1">İlanlarım</q-item-label>
              <q-item-label caption>Yayınladığınız iş ilanları</q-item-label>
            </q-item-section>
          </q-item>
          <q-separator />
          <q-card-section>
            <q-btn to="/jobs" color="primary" unelevated label="İlanları Yönet" />
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-4">
        <q-card class="bg-white" flat bordered>
          <q-item>
            <q-item-section avatar><q-icon name="assignment" color="primary" /></q-item-section>
            <q-item-section>
              <q-item-label class="text-subtitle1">Başvurular</q-item-label>
              <q-item-label caption>İlanlarınıza gelen başvurular</q-item-label>
            </q-item-section>
          </q-item>
          <q-separator />
          <q-card-section>
            <q-btn to="/applications" color="primary" unelevated label="Başvuruları Gör" />
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-4">
        <q-card class="bg-white" flat bordered>
          <q-item>
            <q-item-section avatar><q-icon name="person" color="primary" /></q-item-section>
            <q-item-section>
              <q-item-label class="text-subtitle1">Profil</q-item-label>
              <q-item-label caption>Şirket bilgileri</q-item-label>
            </q-item-section>
          </q-item>
          <q-separator />
          <q-card-section>
            <q-btn to="/profile" color="primary" unelevated label="Profili Düzenle" />
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script setup>
</script>
