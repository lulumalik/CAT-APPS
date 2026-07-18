<?php

namespace Database\Seeders;

use App\Models\ExamTrack;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * 10 soal pilihan ganda per track ujian (JLPT, JFT-Basic, IELTS, TOEFL, SNMPTN, SBMPTN).
     * Format: q = pertanyaan, o = 4 opsi (A-D), c = kunci, d = tingkat kesulitan.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $banks = [
            'jlpt' => [
                ['q' => 'わたしは まいあさ コーヒー（　）のみます。', 'o' => ['を', 'が', 'に', 'へ'], 'c' => 'A', 'd' => 'Easy'],
                ['q' => '「食べます」の じしょけい（bentuk kamus）は どれですか。', 'o' => ['たべた', 'たべる', 'たべて', 'たべない'], 'c' => 'B', 'd' => 'Easy'],
                ['q' => 'きのう ともだち（　）えいがを みました。', 'o' => ['を', 'が', 'と', 'の'], 'c' => 'C', 'd' => 'Easy'],
                ['q' => '漢字「学生」の よみかたは どれですか。', 'o' => ['がっこう', 'せんせい', 'がくせん', 'がくせい'], 'c' => 'D', 'd' => 'Easy'],
                ['q' => 'でんしゃ（　）かいしゃへ いきます。', 'o' => ['で', 'に', 'を', 'が'], 'c' => 'A', 'd' => 'Medium'],
                ['q' => 'そらが くらいですね。あめが（　）そうです。', 'o' => ['ふる', 'ふり', 'ふって', 'ふった'], 'c' => 'B', 'd' => 'Medium'],
                ['q' => 'Arti kata 「おいしい」 adalah ...', 'o' => ['mahal', 'dingin', 'enak', 'lambat'], 'c' => 'C', 'd' => 'Easy'],
                ['q' => 'にほんへ いった（　）が あります。', 'o' => ['もの', 'とき', 'ところ', 'こと'], 'c' => 'D', 'd' => 'Medium'],
                ['q' => 'やすみのひは そうじを したり、せんたくを（　）します。', 'o' => ['したり', 'して', 'する', 'した'], 'c' => 'A', 'd' => 'Medium'],
                ['q' => 'しゅくだいを わすれたので、せんせいに（　）。', 'o' => ['しかりました', 'しかられました', 'しかっています', 'しかります'], 'c' => 'B', 'd' => 'Hard'],
            ],
            'jft' => [
                ['q' => 'Salam yang diucapkan saat tiba di tempat kerja pada pagi hari adalah ...', 'o' => ['おはようございます', 'こんばんは', 'おやすみなさい', 'さようなら'], 'c' => 'A', 'd' => 'Easy'],
                ['q' => 'スーパーで「いらっしゃいませ」と いわれました。なんと いいますか。', 'o' => ['なにも いわなくても いい', 'ありがとう', 'すみません', 'おねがいします'], 'c' => 'A', 'd' => 'Easy'],
                ['q' => 'びょういんへ いくとき、なにを もっていきますか。', 'o' => ['パスポートだけ', 'ほけんしょう', 'ざっし', 'おべんとう'], 'c' => 'B', 'd' => 'Easy'],
                ['q' => '「もえるごみ」は どれですか。', 'o' => ['びん', 'かん', 'なまごみ', 'でんち'], 'c' => 'C', 'd' => 'Easy'],
                ['q' => 'でんしゃの なかで してはいけない ことは どれですか。', 'o' => ['ほんを よむ', 'ねる', 'おんがくを きく（イヤホンで）', 'おおきい こえで でんわを する'], 'c' => 'D', 'd' => 'Easy'],
                ['q' => 'しごとを やすむとき、かいしゃに なんと れんらくしますか。', 'o' => ['「きょうは やすみます。すみません。」', 'なにも いわない', '「あした きます」だけ いう', 'ともだちに いってもらう'], 'c' => 'A', 'd' => 'Medium'],
                ['q' => '「この くすりは しょくご に のんでください」の いみは？', 'o' => ['ごはんの まえに のむ', 'ごはんの あとに のむ', 'ねる まえに のむ', 'あさだけ のむ'], 'c' => 'B', 'd' => 'Medium'],
                ['q' => 'じしんが おきたとき、まず なにを しますか。', 'o' => ['エレベーターに のる', 'そとへ はしる', 'つくえの したに はいる', 'でんわを かける'], 'c' => 'C', 'd' => 'Medium'],
                ['q' => 'ATMで おかねを おろす。「おろす」の いみは？', 'o' => ['menabung', 'meminjam', 'transfer', 'menarik uang'], 'c' => 'D', 'd' => 'Medium'],
                ['q' => 'せんぱいに しりょうを もらいました。なんと いいますか。', 'o' => ['ありがとうございます', 'どういたしまして', 'おまたせしました', 'しつれいします'], 'c' => 'A', 'd' => 'Hard'],
            ],
            'ielts' => [
                ['q' => 'Choose the correct sentence.', 'o' => ['She have been living here for two years.', 'She has been living here for two years.', 'She living here for two years.', 'She is live here for two years.'], 'c' => 'B', 'd' => 'Easy'],
                ['q' => 'The graph ______ a sharp increase in urban population between 2000 and 2010.', 'o' => ['tells', 'says', 'shows', 'speaks'], 'c' => 'C', 'd' => 'Easy'],
                ['q' => 'Which word is closest in meaning to "significant"?', 'o' => ['minor', 'temporary', 'accidental', 'considerable'], 'c' => 'D', 'd' => 'Easy'],
                ['q' => 'If the government ______ more in public transport, traffic congestion would decrease.', 'o' => ['invested', 'invests', 'will invest', 'is investing'], 'c' => 'A', 'd' => 'Medium'],
                ['q' => 'Choose the best linking word: "The experiment failed. ______, the researchers learned a great deal."', 'o' => ['Therefore', 'Nevertheless', 'Because', 'Similarly'], 'c' => 'B', 'd' => 'Medium'],
                ['q' => 'The number of students enrolling in online courses ______ steadily since 2015.', 'o' => ['is rising', 'rose', 'has risen', 'rises'], 'c' => 'C', 'd' => 'Medium'],
                ['q' => 'Which is an example of a formal (academic) expression?', 'o' => ['a lot of problems', 'kids these days', 'stuff like that', 'a considerable number of issues'], 'c' => 'D', 'd' => 'Medium'],
                ['q' => '"The proliferation of smartphones has transformed communication." The word "proliferation" means ...', 'o' => ['rapid increase', 'sudden decline', 'strict regulation', 'complete absence'], 'c' => 'A', 'd' => 'Hard'],
                ['q' => 'Choose the correct passive form: "Researchers conducted the survey in 2020."', 'o' => ['The survey conducted in 2020.', 'The survey was conducted in 2020.', 'The survey is conducting in 2020.', 'The survey has conducted in 2020.'], 'c' => 'B', 'd' => 'Medium'],
                ['q' => '"Hardly ______ the lecture when the fire alarm rang."', 'o' => ['the professor had begun', 'the professor began', 'had the professor begun', 'did the professor begun'], 'c' => 'C', 'd' => 'Hard'],
            ],
            'toefl' => [
                ['q' => 'The committee ______ its final decision next Monday.', 'o' => ['will announce', 'announce', 'announcing', 'announced yesterday'], 'c' => 'A', 'd' => 'Easy'],
                ['q' => 'Choose the correct word: "Neither the students nor the teacher ______ in the classroom."', 'o' => ['are', 'was', 'were', 'have been'], 'c' => 'B', 'd' => 'Medium'],
                ['q' => '"Photosynthesis, ______ occurs in green plants, converts sunlight into energy."', 'o' => ['it', 'that', 'which', 'who'], 'c' => 'C', 'd' => 'Easy'],
                ['q' => 'Identify the synonym of "abundant".', 'o' => ['scarce', 'moderate', 'expensive', 'plentiful'], 'c' => 'D', 'd' => 'Easy'],
                ['q' => '______ the invention of the printing press, books were copied by hand.', 'o' => ['Before', 'Since', 'While', 'Whether'], 'c' => 'A', 'd' => 'Easy'],
                ['q' => 'The professor insisted that every student ______ the assignment on time.', 'o' => ['submits', 'submit', 'submitted', 'is submitting'], 'c' => 'B', 'd' => 'Hard'],
                ['q' => 'Choose the correct comparative: "This method is ______ than the previous one."', 'o' => ['most efficient', 'efficient', 'far more efficient', 'the more efficient'], 'c' => 'C', 'd' => 'Medium'],
                ['q' => '"The data ______ that global temperatures have risen significantly."', 'o' => ['indicating', 'is indicate', 'indicates of', 'indicate'], 'c' => 'D', 'd' => 'Medium'],
                ['q' => 'Not until the 20th century ______ the right to vote in many countries.', 'o' => ['did women gain', 'women gained', 'women did gain', 'gained women'], 'c' => 'A', 'd' => 'Hard'],
                ['q' => '"Despite ______ hard, he failed the examination." Choose the correct option.', 'o' => ['he studied', 'studying', 'to study', 'he was studying'], 'c' => 'B', 'd' => 'Medium'],
            ],
            'snmptn' => [
                ['q' => 'Semua siswa berprestasi rajin belajar. Andi adalah siswa berprestasi. Kesimpulan yang tepat adalah ...', 'o' => ['Andi rajin belajar', 'Andi tidak rajin belajar', 'Semua yang rajin belajar berprestasi', 'Andi belum tentu rajin belajar'], 'c' => 'A', 'd' => 'Easy'],
                ['q' => 'Deret: 2, 6, 12, 20, 30, ... Bilangan berikutnya adalah ...', 'o' => ['40', '42', '44', '46'], 'c' => 'B', 'd' => 'Medium'],
                ['q' => 'Jika harga sebuah buku naik 20% menjadi Rp36.000, harga awalnya adalah ...', 'o' => ['Rp28.000', 'Rp28.800', 'Rp30.000', 'Rp32.000'], 'c' => 'C', 'd' => 'Medium'],
                ['q' => 'Sinonim kata "efisien" adalah ...', 'o' => ['boros', 'lambat', 'rumit', 'tepat guna'], 'c' => 'D', 'd' => 'Easy'],
                ['q' => 'Rata-rata nilai 5 siswa adalah 80. Jika satu siswa bernilai 90 keluar, rata-rata 4 siswa sisanya adalah ...', 'o' => ['77,5', '78', '78,5', '79'], 'c' => 'A', 'd' => 'Medium'],
                ['q' => 'KUDA : ISTAL = AYAM : ...', 'o' => ['sawah', 'kandang', 'sangkar', 'padang'], 'c' => 'B', 'd' => 'Easy'],
                ['q' => 'Jika 3x − 7 = 14, maka nilai 2x + 1 adalah ...', 'o' => ['13', '14', '15', '16'], 'c' => 'C', 'd' => 'Easy'],
                ['q' => 'Sebagian dokter adalah penulis. Semua penulis suka membaca. Kesimpulan yang tepat adalah ...', 'o' => ['Semua dokter suka membaca', 'Sebagian penulis adalah dokter yang tidak suka membaca', 'Semua yang suka membaca adalah dokter', 'Sebagian dokter suka membaca'], 'c' => 'D', 'd' => 'Hard'],
                ['q' => 'Sebuah mobil menempuh 240 km dalam 3 jam. Dengan kecepatan sama, jarak yang ditempuh dalam 5 jam adalah ...', 'o' => ['400 km', '380 km', '420 km', '360 km'], 'c' => 'A', 'd' => 'Easy'],
                ['q' => 'Antonim kata "temporer" adalah ...', 'o' => ['sementara', 'permanen', 'darurat', 'singkat'], 'c' => 'B', 'd' => 'Easy'],
            ],
            'sbmptn' => [
                ['q' => 'Nilai x yang memenuhi persamaan x² − 5x + 6 = 0 adalah ...', 'o' => ['x = 2 atau x = 3', 'x = −2 atau x = −3', 'x = 1 atau x = 6', 'x = −1 atau x = −6'], 'c' => 'A', 'd' => 'Medium'],
                ['q' => 'Turunan pertama dari f(x) = 3x² + 4x − 5 adalah ...', 'o' => ['3x + 4', '6x + 4', '6x − 5', '3x² + 4'], 'c' => 'B', 'd' => 'Medium'],
                ['q' => 'Gagasan utama sebuah paragraf biasanya terdapat pada ...', 'o' => ['kalimat penjelas', 'kata penghubung', 'kalimat utama', 'catatan kaki'], 'c' => 'C', 'd' => 'Easy'],
                ['q' => 'Jika log 2 = 0,301 dan log 3 = 0,477, maka log 6 = ...', 'o' => ['0,602', '0,699', '0,301', '0,778'], 'c' => 'D', 'd' => 'Medium'],
                ['q' => 'Himpunan penyelesaian dari 2x − 4 < 6 adalah ...', 'o' => ['x < 5', 'x > 5', 'x < 1', 'x > 1'], 'c' => 'A', 'd' => 'Easy'],
                ['q' => 'Peluang munculnya mata dadu berjumlah 7 pada pelemparan dua dadu adalah ...', 'o' => ['1/12', '1/6', '1/9', '1/4'], 'c' => 'B', 'd' => 'Medium'],
                ['q' => 'Deret geometri: 3, 6, 12, 24, ... Suku ke-7 adalah ...', 'o' => ['96', '128', '192', '384'], 'c' => 'C', 'd' => 'Medium'],
                ['q' => 'Fungsi f(x) = 2x + 3 dan g(x) = x². Nilai (g∘f)(1) adalah ...', 'o' => ['5', '11', '13', '25'], 'c' => 'D', 'd' => 'Hard'],
                ['q' => 'Kalimat berikut yang menggunakan ejaan baku adalah ...', 'o' => ['Kualitas pendidikan harus ditingkatkan.', 'Kwalitas pendidikan harus ditingkatkan.', 'Kualitas pendidikan harus di tingkatkan.', 'Kwalitas pendidikan harus di tingkatkan.'], 'c' => 'A', 'd' => 'Easy'],
                ['q' => 'Integral ∫(4x + 2) dx adalah ...', 'o' => ['4x² + 2x + C', '2x² + 2x + C', 'x² + 2x + C', '4x² + x + C'], 'c' => 'B', 'd' => 'Hard'],
            ],
        ];

        $tracks = ExamTrack::whereIn('slug', array_keys($banks))->get()->keyBy('slug');

        foreach ($banks as $slug => $items) {
            $track = $tracks->get($slug);
            if (! $track) {
                continue;
            }

            foreach ($items as $item) {
                Question::create([
                    'question' => $item['q'],
                    'category' => $track->name,
                    'exam_track_id' => $track->id,
                    'difficulty' => $item['d'],
                    'type' => 'multiple_choice',
                    'options' => [
                        ['key' => 'A', 'label' => $item['o'][0]],
                        ['key' => 'B', 'label' => $item['o'][1]],
                        ['key' => 'C', 'label' => $item['o'][2]],
                        ['key' => 'D', 'label' => $item['o'][3]],
                    ],
                    'correct' => $item['c'],
                    'created_by' => optional($admin)->id,
                ]);
            }
        }
    }
}
