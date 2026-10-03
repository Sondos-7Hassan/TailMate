# هيكل المشروع (TailMate)

```
Project-Sherif/
├── index.php            ← بيحوّل تلقائيًا على pages/home.php
├── config/
│   ├── config.php       ← BASE_URL / BASE_PATH / UPLOADS_PATH (بيتحسبوا أوتوماتيك)
│   └── connection.php   ← الاتصال بقاعدة البيانات (finalproject)
├── includes/            ← head, user_navbar, admin_navbar, admin_sidebar, footer, validate, validation
├── auth/                ← login, register, logout
├── pages/               ← صفحات اليوزر: home, about, contact, howto, cats(+details), dogs(+details),
│                          adoptionform, profile, notifications (مشتركة بين الأدمن واليوزر)
├── admin/               ← dashboard, users, requests, availablepets, create_pet, adoption_history, message
├── assets/
│   ├── images/          ← كانت layouts/images
│   └── css/style.css    ← كانت layouts/style.css
├── uploads/             ← صور اليوزرز (زي ما هي)
├── database/database_updates.sql
├── docs/                ← IMPLEMENTATION_NOTES.md + الملف ده
└── _unused/             ← ملفات قديمة مش مستخدمة في أي مكان (ممكن تتمسح)
```

## تشغيل المشروع
حط الفولدر في `htdocs` (أي اسم) وافتح `http://localhost/<اسم-الفولدر>/` — الداتابيز `finalproject` زي ما هي.

## أماكن الملفات القديمة -> الجديدة
| قديم | جديد |
|---|---|
| login / register / logout | auth/ |
| dashboard, users, requests, availablepets, create_pet, adoption_history, message | admin/ |
| home, about, contact, howto, cats, cats_details, dogs, dogs_details, adoptionform, profile, notifications | pages/ |
| head, user_navbar, admin_navbar, admin_sidebar, validate, validation | includes/ |
| footer.html | includes/footer.php |
| connection.php | config/connection.php |
| layouts/images/* | assets/images/* |
| layouts/style.css | assets/css/style.css |
| connect.php, layouts.php, avaliablepets.php, logo.jpg, logoo.png, logoo.png.jpg | _unused/ |

## الباثيز
كل اللينكات والـ redirects والصور بتستخدم `BASE_URL` (مثال: `<?= BASE_URL ?>/pages/cats.php`)،
وكل مسارات الرفع بتستخدم `UPLOADS_PATH`. مفيش مسار مكتوب ثابت على اسم فولدر المشروع.
