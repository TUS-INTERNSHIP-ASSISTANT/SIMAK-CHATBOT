# Analisis Konfigurasi RAG: System Prompt, Knowledge Base, dan Temperature

Berdasarkan analisis pada file `resources/views/dashboard/knowledge-base.blade.php`, berikut adalah penjelasan mengenai perbedaan antara **System Prompt** dan **Knowledge Base**, serta bagaimana fitur **Temperature** bekerja pada sistem chatbot SIMAK.

## 1. Perbedaan System Prompt dan Knowledge Base Prompt

Pada antarmuka konfigurasi RAG (Retrieval-Augmented Generation) chatbot, terdapat dua jenis kolom instruksi yang sekilas mirip namun memiliki fungsi yang berbeda:

### A. System Prompt (Instruksi Chatbot)
- **Fungsi Utama:** Mendikte "persona", nada bicara, gaya bahasa, dan batasan utama chatbot.
- **Tujuan:** Menentukan *bagaimana* chatbot harus merespons pengguna. Misalnya, apakah chatbot harus bersikap formal, ramah, layaknya staf akademik, serta instruksi tegas untuk tidak menjawab pertanyaan yang melanggar etika.
- **Deskripsi pada UI:** *"Instruksi utama yang mendikte nada, gaya bahasa, dan batasan chatbot dalam menjawab pertanyaan mahasiswa."*

### B. Knowledge Base Prompt (Tuning Domain Context)
- **Fungsi Utama:** Memberikan konteks domain spesifik (Tuning Domain Context) sebagai panduan tambahan bagi chatbot ketika memproses dokumen.
- **Tujuan:** Menentukan *apa* yang harus ditekankan atau dipahami oleh chatbot di luar teks mentah dokumen. Ini berguna untuk memberikan latar belakang atau aturan interpretasi. Misalnya, memberi tahu chatbot bahwa "Kerja Praktik" dan "Magang" memiliki prosedur yang serupa di fakultas ini, atau memberikan singkatan-singkatan khusus yang sering dipakai di dokumen akademik.
- **Deskripsi pada UI:** *"Tuning konteks domain untuk chatbot di luar dokumen mentah."*

### Ringkasan Perbedaan:
| Aspek | System Prompt | Knowledge Base Prompt |
| :--- | :--- | :--- |
| **Fokus** | Karakter, aturan interaksi, dan persona (Bagaimana cara menjawab). | Pemahaman konteks spesifik dari domain data/dokumen (Apa yang dibahas). |
| **Contoh Isi** | "Kamu adalah Asisten SIMAK yang ramah. Jawablah menggunakan bahasa Indonesia formal." | "Istilah 'KP' selalu merujuk pada 'Kerja Praktik'. Fakultas yang dimaksud adalah Fakultas Teknik." |

---

## 2. Bagaimana Fitur Temperature (Kreativitas) Bekerja

Fitur **Temperature** pada konfigurasi ini mengatur tingkat "kreativitas" dan "kebebasan" dari model AI (LLM) saat menghasilkan teks jawaban.

Pada file `knowledge-base.blade.php`, parameter ini direpresentasikan sebagai slider dengan rentang (range) nilai dari **0.0 hingga 1.0** dengan interval (step) sebesar **0.1**.

Berikut adalah analisis bagaimana fitur ini bekerja berdasarkan panduan nilai yang ada di sistem:

* **Nilai 0.0 (Faktual & Kaku)**
  * **Cara Kerja:** Model AI akan menjadi sangat deterministik. Chatbot akan selalu memilih kata dengan probabilitas tertinggi berikutnya. 
  * **Hasil:** Jawaban akan sangat konsisten, berbasis fakta secara ketat berdasarkan dokumen RAG, dan terkesan kaku (seperti membaca buku manual). Risiko halusinasi (mengarang informasi) paling rendah di titik ini.

* **Nilai 0.5 (Seimbang)**
  * **Cara Kerja:** Memberikan sedikit kelonggaran pada probabilitas pemilihan kata.
  * **Hasil:** Menghasilkan jawaban yang tetap faktual sesuai dokumen, namun disampaikan dengan variasi kalimat yang lebih luwes sehingga terdengar lebih natural dan mengalir layaknya percakapan.

* **Nilai 1.0 (Kreatif & Bebas)**
  * **Cara Kerja:** Model AI diberikan kebebasan penuh untuk memilih kata-kata yang probabilitasnya lebih rendah.
  * **Hasil:** Jawaban akan menjadi sangat bervariasi dan kreatif. Namun, **sangat rentan terhadap halusinasi**, di mana chatbot mungkin menambahkan informasi yang sebenarnya tidak ada di dalam dokumen.

### Analisis & Rekomendasi untuk SIMAK
Karena chatbot SIMAK difungsikan untuk menjawab pertanyaan akademik yang bersifat prosedural (seperti syarat magang atau kerja praktik), disarankan untuk mengatur nilai **Temperature pada kisaran rendah hingga seimbang (0.0 hingga 0.5)**. Nilai yang terlalu tinggi berisiko memberikan informasi akademik yang salah atau mengarang prosedur yang dapat menyesatkan mahasiswa.
