<?php

namespace Database\Seeders;

use App\Enums\CompetitionStatus;
use App\Enums\SubmissionKind;
use App\Enums\SubmissionStatus;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\Prize;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = User::where('email', 'organizer@example.com')->first();
        $organizer2 = User::where('email', 'organizer2@example.com')->first();
        $participants = User::where('role', 'participant')->get();

        // ── Competition Types ──
        $types = [
            [
                'name' => 'مسابقة التصوير الفوتوغرافي',
                'slug' => 'photography',
                'description' => 'مسابقات التصوير الفوتوغرافي بإرسال صور',
                'submission_kind' => SubmissionKind::Image,
            ],
            [
                'name' => 'مسابقة المقالات',
                'slug' => 'articles',
                'description' => 'مسابقات كتابة المقالات والنصوص',
                'submission_kind' => SubmissionKind::Text,
            ],
            [
                'name' => 'مسابقة التصميم',
                'slug' => 'design',
                'description' => 'مسابقات التصميم بإرسال ملفات PDF',
                'submission_kind' => SubmissionKind::Pdf,
            ],
            [
                'name' => 'مسابقة البرمجة',
                'slug' => 'programming',
                'description' => 'مسابقات البرمجة بإرسال روابط المشاريع',
                'submission_kind' => SubmissionKind::Link,
            ],
            [
                'name' => 'مسابقة الفيديو',
                'slug' => 'video',
                'description' => 'مسابقات الفيديو الإبداعي',
                'submission_kind' => SubmissionKind::Video,
            ],
            [
                'name' => 'مسابقة الحضور والمشاركة',
                'slug' => 'registration',
                'description' => 'مسابقات على أرض الواقع تتطلب التسجيل فقط',
                'submission_kind' => SubmissionKind::None,
            ],
        ];

        $typeModels = [];
        foreach ($types as $type) {
            $typeModels[$type['slug']] = CompetitionType::create($type);
        }

        // ── 1. Open Photography Competition ──
        $photoComp = Competition::create([
            'organizer_id' => $organizer->id,
            'competition_type_id' => $typeModels['photography']->id,
            'title' => 'جمال الطبيعة الليبية',
            'description' => 'مسابقة تصوير فوتوغرافي تهدف إلى إبراز جمال الطبيعة في ليبيا. شارك بصورك التي تعكس جمال الجبال والبحار والصحاري.',
            'terms' => 'يجب أن تكون الصورة أصلية وغير معدلة. الحد الأقصى لحجم الصورة 10 ميجابايت. يسمح بمشاركة صورة واحدة فقط لكل مشارك.',
            'starts_at' => now()->subDays(5),
            'ends_at' => now()->addDays(20),
            'status' => CompetitionStatus::Open,
            'requires_approval' => true,
            'evaluation_method' => 'average_score',
        ]);
        $this->createPrizes($photoComp, [
            ['title' => 'المركز الأول', 'description' => 'كاميرا احترافية + شهادة تقدير', 'winners_count' => 1, 'rank' => 1],
            ['title' => 'المركز الثاني', 'description' => 'عدسة تصوير + شهادة تقدير', 'winners_count' => 1, 'rank' => 2],
            ['title' => 'المركز الثالث', 'description' => 'حامل ثلاثي + شهادة تقدير', 'winners_count' => 1, 'rank' => 3],
        ]);
        $this->createSubmissions($photoComp, $participants->take(4), SubmissionKind::Image);

        // ── 2. Open Articles Competition ──
        $articleComp = Competition::create([
            'organizer_id' => $organizer->id,
            'competition_type_id' => $typeModels['articles']->id,
            'title' => 'كتاب المستقبل',
            'description' => 'مسابقة لكتابة مقال حول رؤيتك لمستقبل ليبيا. أفضل مقال سي يتم نشره في مجلة وطنية.',
            'terms' => 'يجب ألا يقل المقال عن 500 كلمة وألا يزيد عن 2000 كلمة. يجب أن يكون المقال أصلياً وغير منشور سابقاً.',
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(15),
            'status' => CompetitionStatus::Open,
            'requires_approval' => false,
            'evaluation_method' => 'average_score',
        ]);
        $this->createPrizes($articleComp, [
            ['title' => 'المركز الأول', 'description' => '5000 دينار ليبي + نشر المقال', 'winners_count' => 1, 'rank' => 1],
            ['title' => 'المركز الثاني', 'description' => '3000 دينار ليبي + نشر المقال', 'winners_count' => 1, 'rank' => 2],
            ['title' => 'المركز الثالث', 'description' => '1500 دينار ليبي', 'winners_count' => 1, 'rank' => 3],
        ]);
        $this->createSubmissions($articleComp, $participants->take(3), SubmissionKind::Text);

        // ── 3. Upcoming Design Competition ──
        $designComp = Competition::create([
            'organizer_id' => $organizer2->id,
            'competition_type_id' => $typeModels['design']->id,
            'title' => 'هوية وطنية',
            'description' => 'صمم هوية بصرية تعكس الهوية الوطنية الليبية بأسلوب عصري. أرسل تصميمك في ملف PDF يحتوي على اللوحة وملخص الفكرة.',
            'terms' => 'يجب أن يكون الملف بصيغة PDF وبحد أقصى 20 ميجابايت. يجب أن يحتوي على شعار ولوحة ألوان وخطوط.',
            'starts_at' => now()->addDays(7),
            'ends_at' => now()->addDays(37),
            'status' => CompetitionStatus::Upcoming,
            'requires_approval' => true,
            'evaluation_method' => 'average_score',
        ]);
        $this->createPrizes($designComp, [
            ['title' => 'المركز الأول', 'description' => '10000 دينار ليبي + تنفيذ الهوية', 'winners_count' => 1, 'rank' => 1],
            ['title' => 'المركز الثاني', 'description' => '5000 دينار ليبي', 'winners_count' => 1, 'rank' => 2],
        ]);

        // ── 4. Closed Programming Competition ──
        $progComp = Competition::create([
            'organizer_id' => $organizer->id,
            'competition_type_id' => $typeModels['programming']->id,
            'title' => 'هاكاثون الذكاء الاصطناعي',
            'description' => 'مسابقة برمجة ل تطوير حلول بالذكاء الاصطناعي لخدمة المجتمع. أرسل رابط مشروعك على GitHub.',
            'terms' => 'يجب أن يكون المشروع منشوراً على GitHub مع ملف README. يجب أن يستخدم تقنيات AI/ML.',
            'starts_at' => now()->subDays(30),
            'ends_at' => now()->subDays(2),
            'status' => CompetitionStatus::Closed,
            'requires_approval' => true,
            'evaluation_method' => 'average_score',
        ]);
        $this->createPrizes($progComp, [
            ['title' => 'المركز الأول', 'description' => '15000 دينار ليبي + تدريب في شركة تقنية', 'winners_count' => 1, 'rank' => 1],
            ['title' => 'المركز الثاني', 'description' => '8000 دينار ليبي', 'winners_count' => 1, 'rank' => 2],
            ['title' => 'المركز الثالث', 'description' => '4000 دينار ليبي', 'winners_count' => 1, 'rank' => 3],
        ]);
        $this->createSubmissions($progComp, $participants->take(5), SubmissionKind::Link);

        // ── 5. Under Evaluation Video Competition ──
        $videoComp = Competition::create([
            'organizer_id' => $organizer2->id,
            'competition_type_id' => $typeModels['video']->id,
            'title' => 'قصتي مع القراءة',
            'description' => 'مسابقة فيديو قصير (لا يتجاوز 3 دقائق) تحكي قصتك مع القراءة وكيف أثرت في حياتك.',
            'terms' => 'الفيديو بحد أقصى 3 دقائق وبصيغة MP4. يجب أن يكون المحتوى أصلياً.',
            'starts_at' => now()->subDays(40),
            'ends_at' => now()->subDays(10),
            'status' => CompetitionStatus::UnderEvaluation,
            'requires_approval' => true,
            'evaluation_method' => 'average_score',
        ]);
        $this->createPrizes($videoComp, [
            ['title' => 'المركز الأول', 'description' => 'كاميرا فيديو + شهادة', 'winners_count' => 1, 'rank' => 1],
            ['title' => 'المركز الثاني', 'description' => 'مايكروفون احترافي', 'winners_count' => 1, 'rank' => 2],
        ]);
        $this->createSubmissions($videoComp, $participants->take(3), SubmissionKind::Video);

        // ── 6. Finished Competition with Results ──
        $finishedComp = Competition::create([
            'organizer_id' => $organizer->id,
            'competition_type_id' => $typeModels['articles']->id,
            'title' => 'أجمل قصة قصيرة',
            'description' => 'مسابقة كتابة القصة القصيرة بموضوع "الوطن". تم إعلان النتائج وتوزيع الجوائز.',
            'terms' => 'القصة بين 1000 و 3000 كلمة. موضوع القصة: الوطن.',
            'starts_at' => now()->subDays(60),
            'ends_at' => now()->subDays(30),
            'status' => CompetitionStatus::Finished,
            'requires_approval' => true,
            'evaluation_method' => 'average_score',
            'results_published_at' => now()->subDays(5),
        ]);
        $prizes = $this->createPrizes($finishedComp, [
            ['title' => 'المركز الأول', 'description' => '7000 دينار ليبي + نشر القصة', 'winners_count' => 1, 'rank' => 1],
            ['title' => 'المركز الثاني', 'description' => '4000 دينار ليبي', 'winners_count' => 1, 'rank' => 2],
            ['title' => 'المركز الثالث', 'description' => '2000 دينار ليبي', 'winners_count' => 1, 'rank' => 3],
        ]);
        $finishedSubs = $this->createSubmissions($finishedComp, $participants->take(6), SubmissionKind::Text, true);
        // Assign winners
        if (count($finishedSubs) >= 3) {
            $finishedSubs[0]->update(['prize_id' => $prizes[0]->id, 'status' => SubmissionStatus::Evaluated]);
            $finishedSubs[1]->update(['prize_id' => $prizes[1]->id, 'status' => SubmissionStatus::Evaluated]);
            $finishedSubs[2]->update(['prize_id' => $prizes[2]->id, 'status' => SubmissionStatus::Evaluated]);
        }

        // ── 7. Another Open Competition (Text) ──
        $openComp2 = Competition::create([
            'organizer_id' => $organizer->id,
            'competition_type_id' => $typeModels['articles']->id,
            'title' => 'خواطر رمضانية',
            'description' => 'شاركنا أجمل خواطرك الرمضانية في 200 كلمة. أفضل الخواطر سيتم نشرها في حسابات التواصل الاجتماعي.',
            'terms' => 'الخاطرة بحد أقصى 200 كلمة. يجب أن تكون أصيلة وغير منشورة سابقاً.',
            'starts_at' => now()->subDays(3),
            'ends_at' => now()->addDays(25),
            'status' => CompetitionStatus::Open,
            'requires_approval' => false,
            'evaluation_method' => 'average_score',
        ]);
        $this->createPrizes($openComp2, [
            ['title' => 'المركز الأول', 'description' => '2000 دينار ليبي', 'winners_count' => 1, 'rank' => 1],
            ['title' => 'المركز الثاني', 'description' => '1000 دينار ليبي', 'winners_count' => 1, 'rank' => 2],
        ]);
        $this->createSubmissions($openComp2, $participants->take(2), SubmissionKind::Text);

        // ── 8. Upcoming Photography Competition ──
        Competition::create([
            'organizer_id' => $organizer2->id,
            'competition_type_id' => $typeModels['photography']->id,
            'title' => 'لقطات من ليبيا',
            'description' => 'مسابقة تصوير فوتوغرافي ل توثيق لحظات ليبيا المؤثرة.',
            'terms' => 'صورة واحدة لكل مشارك. الحد الأقصى 10 ميجابايت.',
            'starts_at' => now()->addDays(15),
            'ends_at' => now()->addDays(45),
            'status' => CompetitionStatus::Upcoming,
            'requires_approval' => true,
            'evaluation_method' => 'average_score',
        ]);
        $this->createPrizes(Competition::latest()->first(), [
            ['title' => 'المركز الأول', 'description' => 'كاميرا + شهادة', 'winners_count' => 1, 'rank' => 1],
            ['title' => 'المركز الثاني', 'description' => 'عدسة + شهادة', 'winners_count' => 1, 'rank' => 2],
            ['title' => 'المركز الثالث', 'description' => 'شهادة تقدير', 'winners_count' => 1, 'rank' => 3],
        ]);

        // ── 9. Open Registration-Only Competition ──
        $regComp = Competition::create([
            'organizer_id' => $organizer->id,
            'competition_type_id' => $typeModels['registration']->id,
            'title' => 'ملتقى الإبداع الليبي',
            'description' => 'ملتقى على أرض الواقع للمبدعين الليبيين. سجل حضورك للمشاركة في الفعاليات والورش.',
            'terms' => 'التسجيل مفتوح للجميع. يكفي التسجيل عبر التطبيق للحصول على بطاقة المشاركة.',
            'starts_at' => now()->subDays(2),
            'ends_at' => now()->addDays(20),
            'status' => CompetitionStatus::Open,
            'requires_approval' => false,
            'evaluation_method' => 'average_score',
        ]);
        $this->createPrizes($regComp, [
            ['title' => 'المركز الأول', 'description' => '3000 دينار ليبي + شهادة', 'winners_count' => 1, 'rank' => 1],
            ['title' => 'المركز الثاني', 'description' => '1500 دينار ليبي + شهادة', 'winners_count' => 1, 'rank' => 2],
        ]);
        $this->createSubmissions($regComp, $participants->take(3), SubmissionKind::None);
    }

    private function createPrizes(Competition $competition, array $prizes): array
    {
        $created = [];
        foreach ($prizes as $prize) {
            $created[] = Prize::create([
                'competition_id' => $competition->id,
                'title' => $prize['title'],
                'description' => $prize['description'],
                'winners_count' => $prize['winners_count'],
                'rank' => $prize['rank'],
            ]);
        }

        return $created;
    }

    private function createSubmissions(Competition $competition, $participants, SubmissionKind $kind, bool $evaluated = false): array
    {
        $submissions = [];
        $statuses = $evaluated
            ? [SubmissionStatus::Accepted, SubmissionStatus::Accepted, SubmissionStatus::Accepted, SubmissionStatus::Rejected, SubmissionStatus::Evaluated, SubmissionStatus::Evaluated]
            : [SubmissionStatus::Submitted, SubmissionStatus::UnderReview, SubmissionStatus::Accepted, SubmissionStatus::Submitted, SubmissionStatus::UnderReview];

        $i = 0;
        foreach ($participants as $participant) {
            $data = [
                'competition_id' => $competition->id,
                'participant_id' => $participant->id,
                'status' => $statuses[$i % count($statuses)],
            ];

            $data = match ($kind) {
                SubmissionKind::Text => array_merge($data, [
                    'text_content' => fake()->realText(500),
                ]),
                SubmissionKind::Link => array_merge($data, [
                    'link_url' => 'https://github.com/'.fake()->userName().'/project-'.fake()->numberBetween(1, 100),
                ]),
                SubmissionKind::Image => array_merge($data, [
                    'text_content' => fake()->sentence(),
                ]),
                SubmissionKind::Pdf => array_merge($data, [
                    'text_content' => fake()->sentence(),
                ]),
                SubmissionKind::Video => array_merge($data, [
                    'text_content' => fake()->sentence(),
                ]),
                SubmissionKind::None => $data,
            };

            if ($data['status'] === SubmissionStatus::Rejected) {
                $data['rejection_reason'] = 'المشاركة لا تستوفي شروط المسابقة';
            }

            $submissions[] = Submission::create($data);
            $i++;
        }

        return $submissions;
    }
}
