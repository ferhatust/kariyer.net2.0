# kariyer.net 2.0 🚀  

Modern bir **iş bulma platformu**.  
Laravel tabanlı **backend API** ve Quasar/Vue tabanlı **frontend** ile geliştirilmiştir.  

---

## 📂 Proje Yapısı

```
kariyer.net2.0/
│── backend/         # Laravel API (iş ilanları, kullanıcılar, başvurular)
│   ├── app/
│   ├── database/
│   ├── routes/
│   └── ...
│
│── frontend/        # Quasar (Vue.js) ile frontend
│   ├── src/
│   ├── public/
│   └── ...
│
│── README.md        # Proje açıklaması
```

---

## ⚙️ Backend (Laravel)

- RESTful API mimarisi  
- Kullanıcı, iş ilanı, başvuru modelleri  
- MySQL veritabanı  
- Adminer üzerinden yönetim  

📌 Çalıştırmak için:  
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

---

## 💻 Frontend (Quasar + Vue.js)

- Quasar Framework  
- Responsive UI (LinkedIn benzeri modern tema)  
- API ile entegre frontend  

📌 Çalıştırmak için:  
```bash
cd frontend
npm install
quasar dev
```

---

## 🚀 Çalıştırma

1. **Backend’i başlat** → `http://localhost:8000`  
2. **Frontend’i çalıştır** → `http://localhost:9000`  
3. Frontend, backend API ile haberleşir.  

---

## 📌 Katkıda Bulunma

- Yeni özellik eklemek için **issue aç**  
- Pull request göndererek katkıda bulunabilirsin  

---

## 📝 Lisans
MIT License © 2025 [ferhatust](https://github.com/ferhatust)  