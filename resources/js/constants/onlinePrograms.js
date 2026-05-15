/**
 * Katalog program online — selaras dengan tampilan beranda (section pilihan kelas).
 * Nilai `value` dipakai backend/API (program_category / program_type).
 */
export const ONLINE_PROGRAMS = [
  {
    value: 'vip',
    name: 'Kelas Karantina ( Online + Offline + Karantina )',
    mode: 'Premium',
    summary: 'Pendampingan intensif dengan kontrol progres harian dan strategi personal.',
    points: ['Mentoring prioritas', 'Monitoring ketat', 'Pendalaman psikologi & akademik'],
  },
  {
    value: 'regular',
    name: 'Kelas Reguler ( Online + Offline )',
    mode: 'Reguler',
    summary: 'Program online fleksibel dengan kurikulum inti untuk persiapan seleksi.',
    points: ['Kelas terjadwal', 'Latihan CBT periodik', 'Diskusi materi dan evaluasi'],
  },
  {
    value: 'bimbingan_online',
    name: 'Kelas Online',
    mode: 'Full Online',
    summary: 'Fokus pada penguatan kompetensi akademik, psikologi, dan latihan soal.',
    points: ['Akses materi kelas', 'Kuis dan tugas', 'Rekap progres pembelajaran'],
  },
  {
    value: 'try_out',
    name: 'Kelas Ujian',
    mode: 'Ujian',
    summary: 'Program simulasi ujian dengan pembahasan jawaban untuk pemetaan kemampuan.',
    points: ['Simulasi tes', 'Analisis skor', 'Pembahasan hasil'],
  },
]

export function programSignupOptionLabel(program) {
  return `${program.name} — ${program.mode}`
}
