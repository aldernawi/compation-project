# توثيق لوحة التحكم (Dashboard Documentation)

يصف هذا الملف باللغة العربية جميع الوظائف والعمليات المتاحة في لوحة التحكم الخاصة بمنصة المسابقات. التطبيق مبني باستخدام Laravel 13 + Inertia v3 + React 19 ويتضمن ثلاث واجهات للمستخدمين حسب الدور: **Admin** (مسؤول)، **Organizer** (منظم)، **Judge** (محكم).

## 1. نظرة عامة

بعد تسجيل الدخول، يتم توجيه كل مستخدم إلى لوحة التحكم الخاصة بدوره بناءً على `auth.user.role`. الأدوار في النظام هي:

- `admin` — مسؤول النظام، يصلح لإدارة جميع الموارد.
- `organizer` — منظم المسابقات، يصلح لإدارة المسابقات التي أنشأها فقط.
- `judge` — محكم، يصلح لمعاينة وتقييم المشاركات المخصصة له.
- `participant` — مشارك، لا يملك لوحة تحكم ويب (يتفاعل مع التطبيق عبر API).

### روابط الدخول

- `/login` — تسجيل الدخول.
- `/register` — إنشاء حساب جديد.
- `/dashboard` — لوحة المستخدم العامة بعد تسجيل الدخول.
- `/admin` — لوحة الإدارة.
- `/organizer` — لوحة المنظم.
- `/judge` — لوحة المحكم.

### الملفات الرئيسية

- `routes/admin.php` — مسارات لوحة الإدارة.
- `routes/organizer.php` — مسارات لوحة المنظم.
- `routes/judge.php` — مسارات لوحة المحكم.
- `app/Http/Controllers/Admin/*` — متحكمات الإدارة.
- `app/Http/Controllers/Organizer/*` — متحكمات المنظم.
- `app/Http/Controllers/Judge/*` — متحكمات المحكم.
- `resources/js/pages/admin/**` — صفحات React للإدارة.
- `resources/js/pages/organizer/**` — صفحات React للمنظم.
- `resources/js/pages/judge/**` — صفحات React للمحكم.
- `resources/js/components/app-sidebar.tsx` — القائمة الجانبية المتغيرة حسب الدور.
- `resources/js/components/data-table.tsx` — مكون الجدول المشترك مع الترقيم.

## 2. الواجهة المشتركة

### 2.1 القائمة الجانبية (AppSidebar)

يقوم المكون `AppSidebar` بقراءة `auth.user.role` وعرض عناصر تنقل مختلفة لكل دور:

- **Admin:** Dashboard, Users, Competition Types, Competitions, Submissions, Reports.
- **Organizer:** Dashboard, My Competitions.
- **Judge:** Dashboard, Assigned Competitions.

### 2.2 شريط التنقل (Breadcrumbs)

كل صفحة تحدد `layout.breadcrumbs` لعرض مسار الصفحة الحالية أسفل شريط العنوان.

### 2.3 جدول البيانات (DataTable)

- يعرض 15 صفًا في الصفحة الواحدة.
- يدعم الترقيم عبر روابط `paginator.links`.
- يعرض حالة "No results" عند عدم وجود بيانات.
- يستخدم في معظم صفحات القوائم (`index.tsx`).

### 2.4 نماذج الإدخال

تستخدم الصفحات مكونات `Form` من Inertia مع حقول `Input`, `select`, `Checkbox`, `Button` وإظهار أخطاء التحقق عبر `InputError`.

## 3. الصلاحيات والوصول

- جميع مسارات لوحة التحكم محمية بـ `auth` middleware.
- كل لوحة محمية بـ `role:<role>` middleware.
- المنظم يستطيع تحديث مسابقة فقط إذا كان `organizer_id` يساوي معرفه (`CompetitionPolicy::update`).
- المحكم يستطيع الوصول إلى مسابقة فقط إذا كان مرتبطًا بها في جدول `competition_judge`.

## 4. لوحة الإدارة (Admin)

### 4.1 الصفحة الرئيسية

- **الرابط:** `GET /admin` (`admin.dashboard`)
- **المتحكم:** `App\Http\Controllers\Admin\DashboardController::index`
- **الصفحة:** `resources/js/pages/admin/index.tsx`
- **الوصف:** صفحة ترحيب تعرض رسالة "Welcome, Admin".

### 4.2 إدارة المستخدمين (Users)

- **الرابط:** `GET /admin/users` (`admin.users.index`)
- **المتحكم:** `App\Http\Controllers\Admin\UserController`
- **الصفحات:**
  - `resources/js/pages/admin/users/index.tsx`
  - `resources/js/pages/admin/users/create.tsx`
  - `resources/js/pages/admin/users/edit.tsx`

#### العمليات المتاحة

1. **عرض المستخدمين:**
   - جدول يعرض الاسم، البريد، الدور، وحالة الحساب (Active/Suspended).
   - الترقيم 15 مستخدمًا في الصفحة.

2. **إنشاء مستخدم جديد (New User):**
   - الضغط على زر **New User**.
   - ملء الحقول التالية:
     - `name` (مطلوب) — الاسم.
     - `email` (مطلوب) — البريد الإلكتروني (فريد).
     - `password` (مطلوب) — كلمة المرور (تأكيدها في `password_confirmation`).
     - `role` (مطلوب) — الدور (`admin`, `organizer`, `judge`, `participant`).
     - `can_manage_judges` — خيار إضافي للمنظمين لإدارة المحكمين.
   - الضغط على **Create**.

3. **تعديل مستخدم (Edit):**
   - الضغط على رابط **Edit**.
   - يمكن تعديل الاسم، البريد، الدور، وخيار `can_manage_judges`.
   - لا يتم تحديث كلمة المرور من هذه الصفحة.

4. **تعليق/تفعيل الحساب:**
   - **Suspend:** يعين `suspended_at` إلى التاريخ الحالي.
   - **Activate:** يعيد `suspended_at` إلى `null`.

5. **حذف مستخدم:**
   - الضغط على **Delete** ثم تأكيد الحذف.

### 4.3 إدارة أنواع المسابقات (Competition Types)

- **الرابط:** `GET /admin/competition-types`
- **المتحكم:** `App\Http\Controllers\Admin\CompetitionTypeController`
- **الصفحات:** `index.tsx`, `create.tsx`, `edit.tsx`

#### العمليات

1. **عرض الأنواع:**
   - جدول يعرض الاسم، الـ `slug`، ونوع التقديم (`submission_kind`).

2. **إنشاء نوع جديد:**
   - `name` (مطلوب) — اسم النوع.
   - `description` (اختياري) — الوصف.
   - `submission_kind` (مطلوب) — نوع الملف/المحتوى المطلوب تقديمه:
     - `image` (صورة)
     - `pdf` (PDF)
     - `video` (فيديو)
     - `text` (نص)
     - `link` (رابط)
   - يتم إنشاء `slug` تلقائيًا من الاسم.

3. **تعديل وحذف:**
   - **Edit:** يعدل الاسم والوصف ونوع التقديم.
   - **Delete:** يحذف النوع.

### 4.4 إدارة المسابقات (Competitions)

- **الرابط:** `GET /admin/competitions`
- **المتحكم:** `App\Http\Controllers\Admin\CompetitionController`
- **الصفحات:** `index.tsx`, `create.tsx`, `edit.tsx`

#### العمليات

1. **عرض المسابقات:**
   - جدول يعرض العنوان، المنظم، النوع، والحالة.

2. **إنشاء مسابقة:**
   - `organizer_id` (مطلوب) — اختيار منظم من قائمة المنظمين.
   - `competition_type_id` (مطلوب) — اختيار نوع المسابقة.
   - `title` (مطلوب) — عنوان المسابقة.
   - `description` (اختياري) — الوصف.
   - `terms` (اختياري) — الشروط والأحكام.
   - `starts_at` (مطلوب) — تاريخ/وقت البداية (`datetime-local`).
   - `ends_at` (مطلوب) — تاريخ/وقت النهاية (يجب أن يكون بعد البداية).
   - `status` (مطلوب) — حالة المسابقة:
     - `upcoming` — قادمة
     - `open` — مفتوحة
     - `closed` — مغلقة
     - `under_evaluation` — تحت التقييم
     - `finished` — منتهية
   - `evaluation_method` (مطلوب) — طريقة التقييم (القيمة الافتراضية `average_score`).
   - `requires_approval` — إذا تم تحديده، يتطلب موافقة المنظم قبل قبول المشاركات.

3. **تعديل مسابقة:**
   - نفس حقول الإنشاء.
   - يسمح بتغيير المنظم.

4. **حذف مسابقة:**
   - الضغط على **Delete** وتأكيد الحذف.

5. **إدارة الجوائز:**
   - من جدول المسابقات، الضغط على **Prizes**.
   - يتم الانتقال إلى صفحة `admin/competitions/{competition}/prizes`.

6. **إدارة النتائج:**
   - من جدول المسابقات، الضغط على **Results**.
   - يتم الانتقال إلى صفحة `admin/competitions/{competition}/results`.

### 4.5 إدارة الجوائز (Prizes)

- **الرابط:** `GET /admin/competitions/{competition}/prizes`
- **المتحكم:** `App\Http\Controllers\Admin\PrizeController`
- **الصفحات:** `index.tsx`, `create.tsx`, `edit.tsx`

#### العمليات

1. **عرض الجوائز:**
   - جدول يعرض الترتيب (`rank`)، العنوان، وعدد الفائزين.

2. **إنشاء جائزة:**
   - `title` (مطلوب) — اسم الجائزة.
   - `description` (اختياري) — الوصف.
   - `rank` (مطلوب) — الترتيب (عدد صحيح ≥ 1).
   - `winners_count` (مطلوب) — عدد الفائزين (≥ 1).

3. **تعديل وحذف:**
   - **Edit** لتعديل البيانات.
   - **Delete** لحذف الجائزة.

### 4.6 إدارة التقديمات (Submissions)

- **الرابط:** `GET /admin/submissions`
- **المتحكم:** `App\Http\Controllers\Admin\SubmissionController`
- **الصفحة:** `resources/js/pages/admin/submissions/index.tsx`

#### العمليات

1. **عرض التقديمات:**
   - جدول يعرض المسابقة، المشارك، والحالة.

2. **قبول/رفض تقديم:**
   - **Accept:** يضبط الحالة إلى `accepted`.
   - **Reject:** يضبط الحالة إلى `rejected`.

3. **حذف تقديم:**
   - الضغط على **Delete** وتأكيد الحذف.

### 4.7 النتائج (Results)

- **الرابط:** `GET /admin/competitions/{competition}/results`
- **المتحكم:** `App\Http\Controllers\Admin\ResultsController`
- **الصفحة:** `resources/js/pages/admin/results/show.tsx`

#### العمليات

1. **عرض النتائج:**
   - جدول يعرض المشاركين، حالة التقديم، متوسط الدرجات، والجائزة المخصصة.

2. **تخصيص جائزة:**
   - من قائمة `Prize` في آخر عمود، اختيار الجائزة المناسبة أو `No prize`.
   - يتم حفظ الاختيار تلقائيًا عند التغيير.

3. **نشر النتائج (Publish Results):**
   - الضغط على زر **Publish Results**.
   - يعين `results_published_at` إلى التاريخ الحالي.
   - يرسل إشعارًا لجميع الفائزين (`SubmissionStatusChanged`).
   - بعد النشر، تظهر حالة "Published".

### 4.8 التقارير (Reports)

- **الرابط:** `GET /admin/reports`
- **المتحكم:** `App\Http\Controllers\Admin\ReportController`
- **الصفحة:** `resources/js/pages/admin/reports/index.tsx`

#### العمليات

1. **تصفية حسب التاريخ:**
   - `from` — تاريخ البداية.
   - `to` — تاريخ النهاية.
   - الضغط على **Filter**.

2. **عرض الإحصائيات:**
   - عدد المسابقات.
   - عدد المشاركين.
   - عدد التقديمات.
   - عدد الفائزين.

3. **عرض أكثر المسابقات مشاركة:**
   - قائمة بأعلى 5 مسابقات من حيث عدد التقديمات.

4. **عرض التقديمات حسب نوع المسابقة:**
   - جدول يعرض نوع المسابقة وعدد التقديمات لكل نوع.

## 5. لوحة المنظم (Organizer)

### 5.1 الصفحة الرئيسية

- **الرابط:** `GET /organizer` (`organizer.dashboard`)
- **المتحكم:** `App\Http\Controllers\Organizer\DashboardController::index`
- **الصفحة:** `resources/js/pages/organizer/index.tsx`

### 5.2 مسابقاتي (My Competitions)

- **الرابط:** `GET /organizer/competitions`
- **المتحكم:** `App\Http\Controllers\Organizer\CompetitionController`
- **الصفحات:** `index.tsx`, `create.tsx`, `edit.tsx`

#### العمليات

1. **عرض المسابقات:**
   - قائمة بالمسابقات التي أنشأها المنظم.

2. **إنشاء مسابقة:**
   - `competition_type_id` (مطلوب) — نوع المسابقة.
   - `title` (مطلوب) — العنوان.
   - `description` (اختياري) — الوصف.
   - `terms` (اختياري) — الشروط.
   - `starts_at` (مطلوب) — البداية.
   - `ends_at` (مطلوب) — النهاية.
   - `evaluation_method` (مطلوب) — طريقة التقييم.
   - `requires_approval` — يتطلب موافقة المنظم قبل قبول المشاركات.
   - ملاحظة: المنظم لا يستطيع ضبط `status` أو `organizer_id`؛ يتم إنشاؤها تلقائيًا باسمه.

3. **تعديل مسابقة:**
   - نفس حقول الإنشاء.
   - يتحقق النظام من صلاحية `can('update', $competition)`.

4. **العمليات الفرعية من جدول المسابقات:**
   - **Submissions** — عرض وإدارة تقديمات المسابقة.
   - **Judges** — إضافة/إزالة محكمين.
   - **Participants** — عرض المشاركين.
   - **Rankings** — عرض الترتيب.
   - **Notify** — إرسال إشعار للمشاركين.

### 5.3 تقديمات المسابقة

- **الرابط:** `GET /organizer/competitions/{competition}/submissions`
- **المتحكم:** `App\Http\Controllers\Organizer\SubmissionController`
- **الصفحة:** `resources/js/pages/organizer/submissions/index.tsx`

#### العمليات

1. **تصفية حسب الحالة:**
   - قائمة منسدلة لاختيار الحالة: `submitted`, `under_review`, `accepted`, `rejected`, `under_evaluation`, `evaluated`.

2. **قبول تقديم:**
   - الضغط على **Accept**.
   - يتم ضبط `status = accepted` وإزالة `rejection_reason`.

3. **رفض تقديم:**
   - الضغط على **Reject**.
   - يطلب النظام إدخال سبب الرفض (`reason`).
   - يتم ضبط `status = rejected` وحفظ السبب.

### 5.4 إدارة المحكمين

- **الرابط:** `GET /organizer/competitions/{competition}/judges`
- **المتحكم:** `App\Http\Controllers\Organizer\JudgeController`
- **الصفحة:** `resources/js/pages/organizer/judges/index.tsx`

#### العمليات

1. **عرض المحكمين:**
   - قائمة بأسماء وبريد المحكمين المخصصين للمسابقة.

2. **إضافة محكم:**
   - اختيار محكم من قائمة `availableJudges`.
   - الضغط على **Assign Judge**.
   - يتم الربط عبر `competition_judge`.

3. **إزالة محكم:**
   - الضغط على **Remove**.

### 5.5 المشاركين

- **الرابط:** `GET /organizer/competitions/{competition}/participants`
- **المتحكم:** `App\Http\Controllers\Organizer\ParticipantController`
- **الصفحة:** `resources/js/pages/organizer/participants/index.tsx`

#### العمليات

1. **عرض المشاركين:**
   - جدول يعرض الاسم، البريد، وحالة تقديمه.

2. **البحث:**
   - إدخال الاسم أو البريد في حقل `search` والضغط على **Search**.

### 5.6 الترتيب (Rankings)

- **الرابط:** `GET /organizer/competitions/{competition}/rankings`
- **المتحكم:** `App\Http\Controllers\Organizer\RankingController`
- **الصفحة:** `resources/js/pages/organizer/rankings/index.tsx`

#### العمليات

1. **عرض الترتيب:**
   - جدول يعرض الترتيب، المشارك، متوسط الدرجات، ودرجات كل محكم.
   - الترتيب يعتمد على `average_score` من الأعلى إلى الأقل.

### 5.7 الإشعارات

- **الرابط:** `GET /organizer/competitions/{competition}/notifications/create`
- **المتحكم:** `App\Http\Controllers\Organizer\NotificationController`
- **الصفحة:** `resources/js/pages/organizer/notifications/create.tsx`

#### العمليات

1. **إرسال إشعار:**
   - كتابة الرسالة في حقل `message` (مطلوب، أقصى 1000 حرف).
   - الضغط على **Send**.
   - يرسل النظام إشعار `CompetitionAnnouncement` إلى جميع المشاركين الذين قدموا للمسابقة.

## 6. لوحة المحكم (Judge)

### 6.1 الصفحة الرئيسية

- **الرابط:** `GET /judge` (`judge.dashboard`)
- **المتحكم:** `App\Http\Controllers\Judge\DashboardController::index`
- **الصفحة:** `resources/js/pages/judge/index.tsx`

### 6.2 المسابقات المخصصة

- **الرابط:** `GET /judge/competitions`
- **المتحكم:** `App\Http\Controllers\Judge\CompetitionController`
- **الصفحة:** `resources/js/pages/judge/competitions/index.tsx`

#### العمليات

1. **عرض المسابقات:**
   - جدول يعرض عنوان المسابقة ونوعها.

2. **تقييم المشاركات:**
   - الضغط على **Evaluate Submissions**.
   - الانتقال إلى `/judge/competitions/{competition}/submissions`.

### 6.3 تقييم المشاركات

- **الرابط:** `GET /judge/competitions/{competition}/submissions`
- **المتحكم:** `App\Http\Controllers\Judge\SubmissionController`
- **الصفحة:** `resources/js/pages/judge/submissions/index.tsx`

#### العمليات

1. **عرض المشاركات:**
   - جدول يعرض المشارك وحالة التقييم:
     - `Evaluated` — تم التقييم.
     - `Needs Review` — يحتاج مراجعة.
     - `Not yet evaluated` — لم يتم التقييم.

2. **فتح صفحة التقييم:**
   - الضغط على **Evaluate** أو **Review Evaluation**.
   - الانتقال إلى `/judge/submissions/{submission}/evaluate`.

### 6.4 صفحة التقييم

- **الرابط:** `GET /judge/submissions/{submission}/evaluate`
- **المتحكم:** `App\Http\Controllers\Judge\SubmissionController::evaluate`
- **الصفحة:** `resources/js/pages/judge/submissions/evaluate.tsx`

#### العمليات

1. **معاينة المشاركة:**
   - عرض `text_content` إن وجد.
   - عرض `link_url` كرابط قابل للفتح.
   - عرض `media_url`:
     - إذا كان امتداد صورة (`jpg`, `jpeg`, `png`, `gif`, `webp`) يتم عرضها.
     - خلاف ذلك يظهر رابط "View attached file".

2. **حفظ التقييم (Save Evaluation):**
   - `score` (مطلوب) — درجة من 0 إلى 100.
   - `notes` (اختياري) — ملاحظات (أقصى 2000 حرف).
   - الضغط على **Save Evaluation**.
   - يتم حفظ/تحديث التقييم عبر `updateOrCreate` باستخدام `judge_id` الحالي.
   - الحالة تصبح `evaluated`.

3. **وضع علامة يحتاج مراجعة (Mark as Needs Review):**
   - الضغط على **Mark as Needs Review**.
   - ينسخ قيمة حقل `notes` تلقائيًا.
   - الحالة تصبح `needs_review`.

## 7. حالات التقديم والمسابقات

### حالات التقديم (SubmissionStatus)

- `submitted` — تم التقديم.
- `under_review` — تحت المراجعة.
- `accepted` — مقبول.
- `rejected` — مرفوض.
- `under_evaluation` — تحت التقييم.
- `evaluated` — تم التقييم.

### حالات المسابقة (CompetitionStatus)

- `upcoming` — قادمة.
- `open` — مفتوحة للتقديم.
- `closed` — مغلقة.
- `under_evaluation` — تحت التقييم.
- `finished` — منتهية.

## 8. مرجع التوصيف التقني

### 8.1 توزيع المسارات والمتحكمات

| الدور | ملف المسارات | المتحكمات | الصفحات |
|---|---|---|---|
| Admin | `routes/admin.php` | `app/Http/Controllers/Admin/*` | `resources/js/pages/admin/**` |
| Organizer | `routes/organizer.php` | `app/Http/Controllers/Organizer/*` | `resources/js/pages/organizer/**` |
| Judge | `routes/judge.php` | `app/Http/Controllers/Judge/*` | `resources/js/pages/judge/**` |

### 8.2 المسارات الرئيسية (Route Summary)

**Admin (`routes/admin.php`):**

| HTTP | URI | Name | Controller |
|---|---|---|---|
| GET | `/admin` | `admin.dashboard` | `DashboardController@index` |
| GET/POST | `/admin/users` | `admin.users.*` | `UserController` |
| PATCH | `/admin/users/{user}/suspend` | `admin.users.suspend` | `UserController@suspend` |
| PATCH | `/admin/users/{user}/activate` | `admin.users.activate` | `UserController@activate` |
| GET/POST | `/admin/competition-types` | `admin.competition-types.*` | `CompetitionTypeController` |
| GET/POST | `/admin/competitions` | `admin.competitions.*` | `CompetitionController` |
| GET/POST | `/admin/competitions/{competition}/prizes` | `admin.competitions.prizes.*` | `PrizeController` |
| GET | `/admin/submissions` | `admin.submissions.index` | `SubmissionController@index` |
| PATCH | `/admin/submissions/{submission}/accept` | `admin.submissions.accept` | `SubmissionController@accept` |
| PATCH | `/admin/submissions/{submission}/reject` | `admin.submissions.reject` | `SubmissionController@reject` |
| GET | `/admin/competitions/{competition}/results` | `admin.competitions.results.show` | `ResultsController@show` |
| PATCH | `/admin/competitions/{competition}/results/submissions/{submission}` | `admin.competitions.results.assign-prize` | `ResultsController@assignPrize` |
| POST | `/admin/competitions/{competition}/results/publish` | `admin.competitions.results.publish` | `ResultsController@publish` |
| GET | `/admin/reports` | `admin.reports.index` | `ReportController@index` |

**Organizer (`routes/organizer.php`):**

| HTTP | URI | Name | Controller |
|---|---|---|---|
| GET | `/organizer` | `organizer.dashboard` | `DashboardController@index` |
| GET/POST/PUT | `/organizer/competitions` | `organizer.competitions.*` | `CompetitionController` |
| GET | `/organizer/competitions/{competition}/submissions` | `organizer.competitions.submissions.index` | `SubmissionController@index` |
| PATCH | `/organizer/competitions/{competition}/submissions/{submission}/accept` | `organizer.competitions.submissions.accept` | `SubmissionController@accept` |
| PATCH | `/organizer/competitions/{competition}/submissions/{submission}/reject` | `organizer.competitions.submissions.reject` | `SubmissionController@reject` |
| GET/POST/DELETE | `/organizer/competitions/{competition}/judges` | `organizer.competitions.judges.*` | `JudgeController` |
| GET | `/organizer/competitions/{competition}/participants` | `organizer.competitions.participants.index` | `ParticipantController@index` |
| GET | `/organizer/competitions/{competition}/rankings` | `organizer.competitions.rankings.index` | `RankingController@index` |
| GET/POST | `/organizer/competitions/{competition}/notifications` | `organizer.competitions.notifications.*` | `NotificationController` |

**Judge (`routes/judge.php`):**

| HTTP | URI | Name | Controller |
|---|---|---|---|
| GET | `/judge` | `judge.dashboard` | `DashboardController@index` |
| GET | `/judge/competitions` | `judge.competitions.index` | `CompetitionController@index` |
| GET | `/judge/competitions/{competition}/submissions` | `judge.competitions.submissions.index` | `SubmissionController@index` |
| GET/POST | `/judge/submissions/{submission}/evaluate` | `judge.submissions.evaluate` | `SubmissionController` |
| PATCH | `/judge/submissions/{submission}/needs-review` | `judge.submissions.needs-review` | `SubmissionController@markNeedsReview` |

### 8.3 مكونات الواجهة الأساسية

- `resources/js/components/app-sidebar.tsx` — القائمة الجانبية.
- `resources/js/components/app-header.tsx` — شريط العنوان العلوي.
- `resources/js/components/data-table.tsx` — جدول البيانات.
- `resources/js/components/ui/table.tsx` — مكون `Table` الأساسي.
- `resources/js/layouts/app-layout.tsx` — التخطيط العام.

### 8.4 طلبات التحقق (Form Requests)

- `App\Http\Requests\Admin\StoreUserRequest`
- `App\Http\Requests\Admin\UpdateUserRequest`
- `App\Http\Requests\Admin\StoreCompetitionTypeRequest`
- `App\Http\Requests\Admin\UpdateCompetitionTypeRequest`
- `App\Http\Requests\Admin\StoreCompetitionRequest`
- `App\Http\Requests\Admin\UpdateCompetitionRequest`
- `App\Http\Requests\Admin\StorePrizeRequest`
- `App\Http\Requests\Admin\UpdatePrizeRequest`
- `App\Http\Requests\Organizer\StoreCompetitionRequest`
- `App\Http\Requests\Organizer\UpdateCompetitionRequest`
- `App\Http\Requests\Organizer\StoreNotificationRequest`
- `App\Http\Requests\Judge\StoreEvaluationRequest`

### 8.5 السياسات والصلاحيات

- `App\Policies\CompetitionPolicy` — التحقق من أن المنظم هو مالك المسابقة.
- `role:` middleware — التحقق من `auth.user.role`.

## 9. كيفية إضافة صفحة/عملية جديدة

لإضافة صفحة جديدة في أي لوحة:

1. أنشئ المتحكم في `app/Http/Controllers/<Role>/NewController.php`.
2. أضف المسارات في `routes/<role>.php` داخل مجموعة `Route::middleware(['auth', 'role:<role>'])`.
3. أنشئ صفحة Inertia في `resources/js/pages/<role>/new-page.tsx`.
4. شغّل `php artisan wayfinder:generate --with-form --no-interaction` لإنشاء دالات TypeScript للمسارات.
5. أضف رابط القائمة الجانبية في `resources/js/components/app-sidebar.tsx` إن لزم الأمر.
6. أنشئ اختبار Feature في `tests/Feature/<Role>/...` للتحقق من الصلاحيات.

---

**ملاحظة:** هذا التوثيق يعكس حالة الكود الحالية. عند تعديل أي مسار أو متحكم أو صفحة، يجب تحديث هذا الملف للحفاظ على دقة المعلومات.
