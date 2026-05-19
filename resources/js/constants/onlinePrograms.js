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
    textColor: '#3b1f00',
    boxShadow: '0 8px 32px rgba(180,140,30,0.45), 0 2px 8px rgba(0,0,0,0.18), inset 0 1px 0 rgba(255,255,255,0.4)',
    backgroundColor: [
      'radial-gradient(ellipse at 20% 18%, rgba(255,255,255,0.55) 0%, rgba(255,255,255,0.15) 25%, transparent 52%)',
      'radial-gradient(ellipse at 78% 88%, rgba(100,55,0,0.38) 0%, transparent 45%)',
      'linear-gradient(155deg, rgba(255,255,255,0.25) 0%, transparent 32%)',
      'linear-gradient(245deg, rgba(255,255,255,0.14) 0%, transparent 28%)',
      'linear-gradient(335deg, rgba(0,0,0,0.1) 0%, transparent 40%)',
      'linear-gradient(105deg, #bf953f 0%, #fcf6ba 30%, #b38728 50%, #fbf5b7 70%, #aa771c 100%)',
    ].join(', '),
  },
  {
    value: 'regular',
    name: 'Kelas Reguler ( Online + Offline )',
    mode: 'Reguler',
    summary: 'Program online fleksibel dengan kurikulum inti untuk persiapan seleksi.',
    points: ['Kelas terjadwal', 'Latihan CBT periodik', 'Diskusi materi dan evaluasi'],
    textColor: 'rgba(1, 6, 36, 1)',
    boxShadow: '0 8px 32px rgba(80,100,220,0.45), 0 2px 8px rgba(0,0,0,0.18), inset 0 1px 0 rgba(255,255,255,0.25)',
    backgroundColor: [
      'radial-gradient(ellipse at 20% 18%, rgba(255,255,255,0.48) 0%, rgba(255,255,255,0.12) 25%, transparent 52%)',
      'radial-gradient(ellipse at 78% 88%, rgba(0,10,80,0.38) 0%, transparent 45%)',
      'linear-gradient(155deg, rgba(255,255,255,0.22) 0%, transparent 32%)',
      'linear-gradient(245deg, rgba(255,255,255,0.12) 0%, transparent 28%)',
      'linear-gradient(335deg, rgba(0,0,0,0.12) 0%, transparent 40%)',
      'linear-gradient(105deg, #667eea 0%, #c9d0fb 30%, #5166d8 50%, #bec7fa 70%, #3d54c4 100%)',
    ].join(', '),
  },
  {
    value: 'bimbingan_online',
    name: 'Kelas Online',
    mode: 'Full Online',
    summary: 'Fokus pada penguatan kompetensi akademik, psikologi, dan latihan soal.',
    points: ['Akses materi kelas', 'Kuis dan tugas', 'Rekap progres pembelajaran'],
    textColor: 'rgb(255, 255, 255)',
    boxShadow: '0 8px 32px rgba(20,40,130,0.55), 0 2px 8px rgba(0,0,0,0.22), inset 0 1px 0 rgba(255,255,255,0.2)',
    backgroundColor: [
      'radial-gradient(ellipse at 20% 18%, rgba(255,255,255,0.42) 0%, rgba(255,255,255,0.1) 25%, transparent 52%)',
      'radial-gradient(ellipse at 78% 88%, rgba(0,5,40,0.42) 0%, transparent 45%)',
      'linear-gradient(155deg, rgba(255,255,255,0.18) 0%, transparent 32%)',
      'linear-gradient(245deg, rgba(255,255,255,0.08) 0%, transparent 28%)',
      'linear-gradient(335deg, rgba(0,0,0,0.16) 0%, transparent 40%)',
      'linear-gradient(105deg, #1e3a8a 0%, #6b84c8 30%, #152d7a 50%, #5a73bc 70%, #0e2060 100%)',
    ].join(', '),
  },
  {
    value: 'try_out',
    name: 'Kelas Ujian',
    mode: 'Ujian',
    summary: 'Program simulasi ujian dengan pembahasan jawaban untuk pemetaan kemampuan.',
    points: ['Simulasi tes', 'Analisis skor', 'Pembahasan hasil'],
    textColor: '#f5e8ff',
    boxShadow: '0 8px 32px rgba(64,4,76,0.6), 0 2px 8px rgba(0,0,0,0.25), inset 0 1px 0 rgba(255,255,255,0.2)',
    backgroundColor: [
      'radial-gradient(ellipse at 20% 18%, rgba(255,255,255,0.45) 0%, rgba(255,255,255,0.12) 25%, transparent 52%)',
      'radial-gradient(ellipse at 78% 88%, rgba(20,0,25,0.5) 0%, transparent 45%)',
      'linear-gradient(155deg, rgba(255,255,255,0.2) 0%, transparent 32%)',
      'linear-gradient(245deg, rgba(255,255,255,0.1) 0%, transparent 28%)',
      'linear-gradient(335deg, rgba(0,0,0,0.18) 0%, transparent 40%)',
      'linear-gradient(105deg, #40044c 0%, #9a40aa 30%, #350040 50%, #8a2a9a 70%, #280030 100%)',
    ].join(', '),
  },
]

export function programSignupOptionLabel(program) {
  return `${program.name} — ${program.mode}`
}
