<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\User;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email','admin@example.com')->first();

        $banks = [
            'Mathematics' => [
                'Easy' => [
                    ['q'=>'2 + 3 = ?','o'=>['4','5','6','7'],'c'=>'B'],
                    ['q'=>'10 − 4 = ?','o'=>['5','6','7','8'],'c'=>'B'],
                    ['q'=>'5 × 3 = ?','o'=>['15','10','20','25'],'c'=>'A'],
                    ['q'=>'24 ÷ 6 = ?','o'=>['3','4','6','8'],'c'=>'B'],
                    ['q'=>'Bilangan genap dari 1–10?','o'=>['1,3,5,7','2,4,6,8','3,6,9,12','2,5,7,9'],'c'=>'B'],
                ],
                'Medium' => [
                    ['q'=>'12 × 7 = ?','o'=>['72','84','96','78'],'c'=>'B'],
                    ['q'=>'(15 − 3) × 2 = ?','o'=>['20','24','26','30'],'c'=>'B'],
                    ['q'=>'60% dari 250 = ?','o'=>['125','150','180','200'],'c'=>'B'],
                    ['q'=>'Akar kuadrat dari 225 = ?','o'=>['14','15','16','17'],'c'=>'B'],
                    ['q'=>'Nilai x: 3x + 9 = 24','o'=>['4','5','6','7'],'c'=>'C'],
                ],
                'Hard' => [
                    ['q'=>'Turunan d/dx (x^3 − 5x^2 + 7) = ?','o'=>['3x^2 − 10x','3x^2 − 10x + 7','x^3 − 10x','3x − 10'],'c'=>'A'],
                    ['q'=>'Limit x→∞ (3x^2 + x)/(x^2 − 1) = ?','o'=>['3','1','0','∞'],'c'=>'A'],
                    ['q'=>'Luas segitiga sisi 13,14,15 (Heron) = ?','o'=>['84','90','91','92'],'c'=>'C'],
                    ['q'=>'Integral ∫(2x) dx dari 0 ke 5 = ?','o'=>['25','50','10','5'],'c'=>'A'],
                    ['q'=>'Jumlah sudut dalam segi-12 = ?','o'=>['180°','1440°','1620°','1080°'],'c'=>'D'],
                ],
            ],
            'Physics' => [
                'Easy' => [
                    ['q'=>'Satuan SI untuk gaya?','o'=>['Joule','Newton','Watt','Pascal'],'c'=>'B'],
                    ['q'=>'Kecepatan adalah?','o'=>['Perubahan energi','Perubahan posisi terhadap waktu','Gaya per massa','Tekanan per luas'],'c'=>'B'],
                    ['q'=>'cahaya bergerak paling cepat di?','o'=>['Udara','Air','Ruang hampa','Kaca'],'c'=>'C'],
                    ['q'=>'Alat ukur arus listrik?','o'=>['Voltmeter','Ammeter','Ohmmeter','Thermometer'],'c'=>'B'],
                    ['q'=>'Gravitasi bumi ~?','o'=>['9.8 m/s²','1 m/s²','5 m/s²','3.7 m/s²'],'c'=>'A'],
                ],
                'Medium' => [
                    ['q'=>'Hukum II Newton: F = ?','o'=>['m/a','m×a','a/m','m−a'],'c'=>'B'],
                    ['q'=>'Energi kinetik = ?','o'=>['mgh','½mv²','qV','IVt'],'c'=>'B'],
                    ['q'=>'Frekuensi 50 Hz, periode = ?','o'=>['0.02 s','0.1 s','0.5 s','2 s'],'c'=>'A'],
                    ['q'=>'Muatan elektron = ?','o'=>['+1.6×10^-19 C','−1.6×10^-19 C','0','+1.6×10^19 C'],'c'=>'B'],
                    ['q'=>'Hukum Ohm: V = ?','o'=>['IR','I/R','R/I','VI'],'c'=>'A'],
                ],
                'Hard' => [
                    ['q'=>'Momentum sudut L untuk partikel: L = ?','o'=>['r×p','p×v','m×a','q×E'],'c'=>'A'],
                    ['q'=>'Medan magnet sekitar kawat lurus: B ∝ ?','o'=>['I/r','1/Ir','r/I','I×r'],'c'=>'A'],
                    ['q'=>'Energi foton: E = ?','o'=>['hc/λ','hλ/c','λ/hc','h/λ'],'c'=>'A'],
                    ['q'=>'Relativitas: p = ?','o'=>['mv','γmv','m/v','v/γ'],'c'=>'B'],
                    ['q'=>'Induksi Faraday: ε = ?','o'=>['dΦ/dt','−dΦ/dt','Φ/dt','IΦ'],'c'=>'B'],
                ],
            ],
            'Chemistry' => [
                'Easy' => [
                    ['q'=>'Rumus kimia air?','o'=>['H2O','CO2','O2','NaCl'],'c'=>'A'],
                    ['q'=>'pH netral pada 25°C?','o'=>['6','7','8','14'],'c'=>'B'],
                    ['q'=>'Gas mulia pertama?','o'=>['Helium','Neon','Argon','Krypton'],'c'=>'A'],
                    ['q'=>'Ikatan NaCl adalah?','o'=>['Ionik','Kovalen','Metalik','Hidrogen'],'c'=>'A'],
                    ['q'=>'Unit konsentrasi larutan?','o'=>['m/s','mol/L','kg/m³','J/mol'],'c'=>'B'],
                ],
                'Medium' => [
                    ['q'=>'Bilangan oksidasi O dalam H2O?','o'=>['−1','−2','+1','0'],'c'=>'B'],
                    ['q'=>'Volume 1 mol gas ideal (STP) ~?','o'=>['22.4 L','24 L','1 L','2.24 L'],'c'=>'A'],
                    ['q'=>'Rumus etana?','o'=>['CH4','C2H6','C2H4','C3H8'],'c'=>'B'],
                    ['q'=>'Tipe ikatan dalam H2?','o'=>['Ionik','Kovalen tunggal','Metalik','Hidrogen'],'c'=>'B'],
                    ['q'=>'Asam kuat di bawah ini?','o'=>['CH3COOH','HCl','NH3','H2O'],'c'=>'B'],
                ],
                'Hard' => [
                    ['q'=>'Konstanta Avogadro ~?','o'=>['6.02×10^23','6.02×10^22','6.02×10^24','6.02×10^21'],'c'=>'A'],
                    ['q'=>'pKa kecil artinya?','o'=>['Asam lebih lemah','Asam lebih kuat','Basa lebih kuat','Netral'],'c'=>'B'],
                    ['q'=>'Hukum Le Chatelier berlaku untuk?','o'=>['Kesetimbangan','Larutan jenuh','Koloid','Elektrolit lemah'],'c'=>'A'],
                    ['q'=>'Rumus glukosa?','o'=>['C6H12O6','C12H22O11','C6H6','CH3OH'],'c'=>'A'],
                    ['q'=>'Hybridisasi CH4?','o'=>['sp','sp2','sp3','sp3d'],'c'=>'C'],
                ],
            ],
            'Geography' => [
                'Easy' => [
                    ['q'=>'Ibu kota Jepang?','o'=>['Osaka','Kyoto','Tokyo','Nagoya'],'c'=>'C'],
                    ['q'=>'Benua terbesar?','o'=>['Afrika','Asia','Eropa','Amerika'],'c'=>'B'],
                    ['q'=>'Samudra terdalam?','o'=>['Atlantik','Pasifik','Hindia','Arktik'],'c'=>'B'],
                    ['q'=>'Gunung tertinggi di dunia?','o'=>['K2','Everest','Kilimanjaro','Elbrus'],'c'=>'B'],
                    ['q'=>'Ibu kota Australia?','o'=>['Sydney','Melbourne','Canberra','Perth'],'c'=>'C'],
                ],
                'Medium' => [
                    ['q'=>'Sungai terpanjang?','o'=>['Nil','Amazon','Yangtze','Mississippi'],'c'=>'B'],
                    ['q'=>'Gurun terbesar?','o'=>['Sahara','Gobi','Arab','Kalahari'],'c'=>'A'],
                    ['q'=>'Danau terbesar?','o'=>['Superior','Victoria','Kaspia','Baikal'],'c'=>'C'],
                    ['q'=>'Iklim Indonesia didominasi oleh?','o'=>['Tropis','Subtropis','Gurun','Temperate'],'c'=>'A'],
                    ['q'=>'Negara beribu kota Ankara?','o'=>['Iran','Turki','Yunani','Mesir'],'c'=>'B'],
                ],
                'Hard' => [
                    ['q'=>'Letak Palung Mariana?','o'=>['Atlantik','Pasifik barat','Hindia','Arktik'],'c'=>'B'],
                    ['q'=>'Puncak Andes tertinggi?','o'=>['Aconcagua','Ojos del Salado','Huascarán','Chimborazo'],'c'=>'A'],
                    ['q'=>'Delta terbesar?','o'=>['Nil','Gangga-Brahmaputra','Mekong','Mississippi'],'c'=>'B'],
                    ['q'=>'Cincin Api Pasifik terkait?','o'=>['Subduksi','Erosi','Delta','Karst'],'c'=>'A'],
                    ['q'=>'Arus Gulf Stream mengalir ke?','o'=>['Laut Utara','Laut Tengah','Pasifik','Hindia'],'c'=>'A'],
                ],
            ],
            'History' => [
                'Easy' => [
                    ['q'=>'WWII berakhir tahun?','o'=>['1945','1939','1918','1950'],'c'=>'A'],
                    ['q'=>'Proklamasi RI tahun?','o'=>['1950','1945','1965','1930'],'c'=>'B'],
                    ['q'=>'Tokoh teori relativitas?','o'=>['Newton','Einstein','Galileo','Planck'],'c'=>'B'],
                    ['q'=>'Kerajaan Majapahit berdiri di?','o'=>['Sumatra','Jawa Timur','Kalimantan','Bali'],'c'=>'B'],
                    ['q'=>'Runtuhnya Romawi Barat?','o'=>['476 M','1453 M','1066 M','800 M'],'c'=>'A'],
                ],
                'Medium' => [
                    ['q'=>'Perang Dunia I berakhir?','o'=>['1918','1939','1945','1925'],'c'=>'A'],
                    ['q'=>'Revolusi Prancis dimulai?','o'=>['1776','1789','1804','1815'],'c'=>'B'],
                    ['q'=>'Tokoh kemerdekaan India?','o'=>['Nehru','Gandhi','Patel','Bose'],'c'=>'B'],
                    ['q'=>'Perang Dingin berakhir sekitar?','o'=>['1989–1991','1970–1972','1960–1965','2001–2003'],'c'=>'A'],
                    ['q'=>'Konferensi Asia Afrika di Bandung tahun?','o'=>['1950','1955','1960','1965'],'c'=>'B'],
                ],
                'Hard' => [
                    ['q'=>'Dynasti Ming berkuasa?','o'=>['1368–1644','618–907','206–220','960–1279'],'c'=>'A'],
                    ['q'=>'Perang Seratus Tahun terjadi antara?','o'=>['Prancis–Inggris','Prancis–Spanyol','Inggris–Belanda','Jerman–Italia'],'c'=>'A'],
                    ['q'=>'Deklarasi Magna Carta tahun?','o'=>['1066','1215','1492','1648'],'c'=>'B'],
                    ['q'=>'Perang Peloponnesia antara?','o'=>['Sparta–Athena','Roma–Kartago','Persia–Yunani','Macedonia–Yunani'],'c'=>'A'],
                    ['q'=>'Kerajaan Sriwijaya berpusat di?','o'=>['Palembang','Medan','Jakarta','Makassar'],'c'=>'A'],
                ],
            ],
            'General Knowledge' => [
                'Easy' => [
                    ['q'=>'Planet terdekat ke Matahari?','o'=>['Venus','Merkurius','Bumi','Mars'],'c'=>'B'],
                    ['q'=>'Bahasa resmi PBB?','o'=>['Latin','Arab','Ibrani','Sanskerta'],'c'=>'B'],
                    ['q'=>'Simbol kimia emas?','o'=>['Ag','Au','Fe','Pb'],'c'=>'B'],
                    ['q'=>'Ibukota Inggris?','o'=>['London','Paris','Berlin','Dublin'],'c'=>'A'],
                    ['q'=>'Benua dengan jumlah negara terbanyak?','o'=>['Afrika','Asia','Eropa','Amerika'],'c'=>'A'],
                ],
                'Medium' => [
                    ['q'=>'Penemu telepon?','o'=>['Edison','Bell','Tesla','Marconi'],'c'=>'B'],
                    ['q'=>'Lukisan Mona Lisa oleh?','o'=>['Michelangelo','Da Vinci','Raphael','Van Gogh'],'c'=>'B'],
                    ['q'=>'Gunung Fuji ada di?','o'=>['Korea','Jepang','Tiongkok','Taiwan'],'c'=>'B'],
                    ['q'=>'Bahasa paling banyak penutur asli?','o'=>['Inggris','Mandarin','Spanyol','Hindi'],'c'=>'B'],
                    ['q'=>'Kota dengan penduduk terbanyak?','o'=>['Tokyo','Delhi','Shanghai','São Paulo'],'c'=>'A'],
                ],
                'Hard' => [
                    ['q'=>'Hadiah Nobel didirikan oleh?','o'=>['Einstein','Nobel','Curie','Planck'],'c'=>'B'],
                    ['q'=>'Kepler merumuskan hukum?','o'=>['Gerak planet','Gravitasi','Elektromagnetik','Termodinamika'],'c'=>'A'],
                    ['q'=>'Sastrawan “War and Peace”?','o'=>['Tolstoy','Dostoevsky','Pushkin','Gorky'],'c'=>'A'],
                    ['q'=>'Ibukota Ethiopia?','o'=>['Addis Ababa','Asmara','Nairobi','Kampala'],'c'=>'A'],
                    ['q'=>'Jumlah negara ASEAN saat ini?','o'=>['8','10','11','12'],'c'=>'C'],
                ],
            ],
        ];

        $orderCats = ['Mathematics','Physics','Chemistry','Geography','History','General Knowledge'];
        $orderDiffs = ['Easy','Medium','Hard'];
        $questions = [];
        foreach ($orderCats as $cat) {
            foreach ($orderDiffs as $dif) {
                foreach ($banks[$cat][$dif] as $t) {
                    $options = [
                        ['key'=>'A','label'=>$t['o'][0]],
                        ['key'=>'B','label'=>$t['o'][1]],
                        ['key'=>'C','label'=>$t['o'][2]],
                        ['key'=>'D','label'=>$t['o'][3]],
                    ];
                    $questions[] = [
                        'question' => $t['q'],
                        'category' => $cat,
                        'difficulty' => $dif,
                        'options' => $options,
                        'correct' => $t['c'],
                    ];
                }
            }
        }
        if (count($questions) < 100) {
            $need = 100 - count($questions);
            for ($i = 1; $i <= $need; $i++) {
                $a = ($i % 9) + 2;
                $b = (($i * 3) % 9) + 2;
                $dif = $orderDiffs[$i % count($orderDiffs)];
                $sum = $a + $b;
                $options = [
                    ['key'=>'A','label'=> (string)($sum - 1)],
                    ['key'=>'B','label'=> (string)$sum],
                    ['key'=>'C','label'=> (string)($sum + 1)],
                    ['key'=>'D','label'=> (string)($sum + 2)],
                ];
                $questions[] = [
                    'question' => "{$a} + {$b} = ?",
                    'category' => 'Mathematics',
                    'difficulty' => $dif,
                    'options' => $options,
                    'correct' => 'B',
                ];
            }
        }

        foreach ($questions as $q) {
            Question::create($q + ['created_by' => optional($admin)->id]);
        }
    }
}
