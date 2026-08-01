# منصة إدارة المسابقات

منصة ويب متكاملة مع تطبيق موبايل لإدارة وتنظيم وتقييم المسابقات. تم بناؤها باستخدام Laravel و Inertia.js و React و Tailwind CSS، وتدعم اللغة العربية والدينار الليبي (LYD) وتسجيل الدخول المطلوب للمشاركة في المسابقات، بالإضافة إلى تطبيق موبايل خاص.

---

## فهرس المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [الميزات](#الميزات)
3. [الهيكل التقني والتقنيات المستخدمة](#الهيكل-التقني-والتقنيات-المستخدمة)
4. [هيكل المشروع](#هيكل-المشروع)
5. [المتطلبات](#المتطلبات)
6. [التثبيت](#التثبيت)
7. [الإعدادات](#الإعدادات)
8. [قاعدة البيانات](#قاعدة-البيانات)
9. [طريقة الاستخدام](#طريقة-الاستخدام)
10. [API](#api)
11. [أدوار المستخدمين والصلاحيات](#أدوار-المستخدمين-والصلاحيات)
12. [تطبيق الموبايل](#تطبيق-الموبايل)
13. [الاختبارات](#الاختبارات)
14. [النشر](#النشر)
15. [الأمان](#الأمان)
16. [الترخيص](#الترخيص)

---

## نظرة عامة

منصة إدارة المسابقات تبسّط دورة حياة المسابقة الكاملة — من الإنشاء والتسجيل إلى تقديم الأعمال والتقييم والترتيب وإصدار التقارير. توفر لوحات تحكم مخصصة للإداريين والمنظمين والحكام والمشاركين، بحيث يمتلك كل دور الأدوات التي يحتاجها لإنجاز مهامه.

أبرز المميزات:

- توطين عربي مع دعم الدينار الليبي (LYD).
- اشتراط التسجيل للمشاركة في المسابقات.
- لوحات تحكم مبنية على الأدوار والصلاحيات.
- REST API للموبايل والتكاملات الخارجية.
- إشعارات فورية عبر FCM tokens.
- إدارة الملفات المرفقة للمشاركات والتقارير.
- واجهة أمامية حديثة باستخدام React و Inertia.js.

---

## الميزات

### إدارة المسابقات

- إنشاء وتصنيف المسابقات مع أنواع مخصصة.
- إعداد الجوائز والتواريخ ومتطلبات التسجيل.
- نشر وإخفاء المسابقات.
- نشر النتائج مع تحديد وقت النشر.
- دعم المسابقات التي تتطلب تسجيل مسبق.

### المشاركات والتقييم

- تقديم المشاركين لأعمالهم مع المرفقات.
- تقييم الحكام للمشاركات حسب معايير محددة.
- متابعة حالة التقييم وأسباب الرفض.
- نشر النتائج النهائية وإنشاء الترتيب.

### الجوائز والترتيب

- تحديد جوائز لكل مسابقة.
- ربط الجوائز بالمشاركات الفائزة.
- حساب وعرض الترتيب حسب النقاط.

### إدارة المستخدمين

- أدوار متعددة: Admin، Organizer، Judge، Participant.
- إعدادات الملف الشخصي والأمان.
- دعم رقم الهاتف وتعليق الحسابات.
- دعم Passkeys للمصادقة الحديثة.

### التقارير والإشعارات

- تقارير للإداريين والمنظمين.
- إشعارات فورية عبر FCM.
- إشعارات للنتائج والمشاركات ومواعيد المسابقات.

### التوطين والعملة

- واجهة عربية ورسائل عربية.
- تنسيق العملة بالدينار الليبي (LYD).
- دعم تخطيط من اليمين إلى اليسار (RTL).

---

## الهيكل التقني والتقنيات المستخدمة

| الطبقة | التقنية |
|--------|---------|
| إطار العمل للخلفية | Laravel 13 (PHP 8.5) |
| إطار العمل للواجهة الأمامية | React 19 |
| ربط SPA | Inertia.js 3 |
| التنسيق | Tailwind CSS 4 |
| مصادقة API | Laravel Sanctum 4 |
| مصادقة الويب | Laravel Fortify 1 |
| الاختبارات | Pest 4 / PHPUnit 12 |
| قاعدة البيانات | MySQL / PostgreSQL / SQLite (قابلة للإعداد) |
| قائمة الانتظار | Laravel Queues / Database driver |
| الذاكرة المؤقتة | Laravel Cache / Database driver |
| مسارات من نوع TypeScript | Laravel Wayfinder 0 |
| تطبيق الموبايل | داخل مجلد `mobile/` |

---

## هيكل المشروع

```
├── app/
│   ├── Http/
│   │   ├── Controllers/Admin/      # متحكمات لوحة الإدارة
│   │   ├── Controllers/Organizer/  # متحكمات لوحة المنظم
│   │   ├── Controllers/Judge/      # متحكمات لوحة الحكم
│   │   ├── Controllers/Settings/   # متحكمات الملف والأمان
│   │   └── Controllers/Api/        # متحكمات API
│   ├── Models/                     # نماذج Eloquent
│   └── ...
├── bootstrap/                      # ملفات بدء التشغيل
├── config/                         # ملفات الإعداد
├── database/
│   ├── factories/                  # المصانع
│   ├── migrations/                 # ملفات الهجرة
│   └── seeders/                    # ملفات التعبئة
├── mobile/                         # تطبيق الموبايل
├── public/                         # الأصول العامة
├── resources/
│   ├── js/                         # صفحات ومكونات React / Inertia
│   └── css/                        # أنماط Tailwind
├── routes/
│   ├── web.php                     # مسارات الويب / Inertia
│   └── api.php                     # مسارات API
├── storage/                        # السجلات والرفوعات والذاكرة المؤقتة
├── tests/                          # اختبارات Pest / PHPUnit
├── .env.example                    # نموذج بيئة العمل
├── composer.json
├── package.json
└── vite.config.js
```

---

## المتطلبات

- PHP 8.5 أو أعلى
- Composer 2.x
- Node.js 20.x أو أعلى
- npm أو Yarn
- MySQL 8.0+ / PostgreSQL 14+ / SQLite 3
- Git

---

## التثبيت

1. استنساخ المستودع:

   ```bash
   git clone https://github.com/aldernawi/compation-project.git
   cd compation-project
   ```

2. تثبيت اعتماديات PHP:

   ```bash
   composer install
   ```

3. تثبيت اعتماديات JavaScript:

   ```bash
   npm install
   ```

4. توليد مسارات Wayfinder:

   ```bash
   php artisan wayfinder:generate
   ```

---

## الإعدادات

1. نسخ ملف البيئة:

   ```bash
   cp .env.example .env
   ```

2. توليد مفتاح التطبيق:

   ```bash
   php artisan key:generate
   ```

3. تحديث ملف `.env` بإعدادات قاعدة البيانات والبريد و FCM وقائمة الانتظار:

   ```env
   APP_NAME="منصة إدارة المسابقات"
   APP_URL=http://localhost

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=competition
   DB_USERNAME=root
   DB_PASSWORD=

   FCM_SERVER_KEY=your_fcm_key
   ```

4. بناء الواجهة الأمامية:

   ```bash
   npm run build
   # أو للتطوير
   npm run dev
   ```

---

## قاعدة البيانات

تشغيل الهجرات والتعبئة:

```bash
php artisan migrate
php artisan db:seed
```

للتطوير مع حسابات تجريبية:

```bash
php artisan db:seed --class=DatabaseSeeder
```

### أهم الجداول

- `users`
- `competition_types`
- `competitions`
- `prizes`
- `submissions`
- `competition_judge` (جدول وسيط)
- `evaluations`
- `notifications`
- `media`
- `passkeys`
- `personal_access_tokens`

---

## طريقة الاستخدام

تشغيل خادم التطوير:

```bash
php artisan serve
```

تشغيل خادم Vite في نافذة منفصلة:

```bash
npm run dev
```

افتح التطبيق في المتصفح:

```
http://localhost:8000
```

---

## API

تقدم المنصة REST API للموبايل والتكاملات الخارجية، مع مصادقة عبر Laravel Sanctum.

### المصادقة

- `POST /api/register`
- `POST /api/login`
- `POST /api/logout`
- `POST /api/profile`

### المسابقات

- `GET /api/competitions`
- `GET /api/competitions/{competition}`
- `POST /api/competitions/{competition}/register`

### المشاركات

- `GET /api/submissions`
- `POST /api/submissions`
- `GET /api/submissions/{submission}`

### الإشعارات

- `GET /api/notifications`
- `POST /api/notifications/read`

لمعرفة جميع المسارات:

```bash
php artisan route:list
```

---

## أدوار المستخدمين والصلاحيات

| الدور | الصلاحيات |
|-------|-----------|
| **Admin** | إدارة المستخدمين والمسابقات والأنواع والجوائز والتقارير وإعدادات المنصة. |
| **Organizer** | إنشاء وإدارة المسابقات وتعيين الحكام وعرض المشاركات ونشر النتائج. |
| **Judge** | تقييم المشاركات المخصصة له وعرض الترتيب. |
| **Participant** | التسجيل في المسابقات وتقديم الأعمال وعرض النتائج والترتيب. |

---

## تطبيق الموبايل

يوجد تطبيق موبايل مخصص داخل مجلد `mobile/`. راجع `mobile/README.md` لتعليمات الإعداد والمتطلبات الخاصة بكل منصة.

---

## الاختبارات

يستخدم المشروع Pest للاختبارات الوحدوية والوظيفية.

تشغيل جميع الاختبارات:

```bash
php artisan test
```

عرض ملخص مختصر:

```bash
php artisan test --compact
```

تشغيل اختبار معين:

```bash
php artisan test --compact --filter=CompetitionTest
```

تحليل الكود الثابت باستخدام Larastan:

```bash
vendor/bin/phpstan analyse
```

تنسيق كود PHP باستخدام Pint:

```bash
vendor/bin/pint
```

---

## النشر

المنصة المقترحة للنشر هي [Laravel Cloud](https://cloud.laravel.com/).

للنشر التقليدي:

1. تثبيت الاعتماديات على الخادم:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   ```

2. ضبط متغيرات البيئة في ملف `.env` على الخادم.

3. تشغيل الهجرات:

   ```bash
   php artisan migrate --force
   ```

4. تحسين أداء التطبيق:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. إعداد خادم الويب (Nginx أو Apache) لخدمة مجلد `public/`.

---

## الأمان

- لا تُرفع ملفات `.env` أو مفاتيح API إلى مستودع Git.
- حدّث الاعتماديات باستمرار.
- استخدم كلمات مرور قوية وفعّل Passkeys عند الإمكان.
- تحقق من صلاحيات جميع طلبات API.
- احفظ الملفات المرفوعة خارج جذر الويب أو استخدم التخزين المخصص في Laravel.

---

## الترخيص

هذا المشروع مفتوح المصدر ومتاح بموجب [ترخيص MIT](LICENSE).
