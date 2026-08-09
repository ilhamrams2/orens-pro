import os
import sys
from reportlab.lib.pagesizes import letter, A4
from reportlab.lib import colors
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    """
    Two-pass canvas to dynamically compute and display total page numbers, headers, and footers.
    """
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            super().showPage()
        super().save()

    def draw_page_decorations(self, page_count):
        self.saveState()
        
        # A4 dimensions: 595.27 x 841.89 points
        page_width, page_height = A4
        margin = 40
        
        # Draw Header (Only on pages after page 1)
        if self._pageNumber > 1:
            self.setFont("Helvetica-Bold", 8)
            self.setFillColor(colors.HexColor("#1E3A8A"))
            self.drawString(margin, page_height - 30, "PANDUAN & CONTOH LIST ALUR SISTEM UNTUK ACTIVITY DIAGRAM (UML)")
            
            self.setFont("Helvetica", 8)
            self.setFillColor(colors.HexColor("#64748B"))
            self.drawRightString(page_width - margin, page_height - 30, "Dokumen Referensi Software Design")
            
            # Header line
            self.setStrokeColor(colors.HexColor("#CBD5E1"))
            self.setLineWidth(0.75)
            self.line(margin, page_height - 35, page_width - margin, page_height - 35)

        # Draw Footer (All pages)
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748B"))
        self.drawString(margin, 25, "OrensPro - Panduan Desain Perangkat Lunak & Diagram Aktivitas")
        
        page_text = f"Halaman {self._pageNumber} dari {page_count}"
        self.drawRightString(page_width - margin, 25, page_text)
        
        # Footer line
        self.setStrokeColor(colors.HexColor("#CBD5E1"))
        self.setLineWidth(0.75)
        self.line(margin, 35, page_width - margin, 35)
        
        self.restoreState()


def create_activity_diagram_pdf(filename="Contoh_List_Activity_Diagram.pdf"):
    doc = SimpleDocTemplate(
        filename,
        pagesize=A4,
        leftMargin=40,
        rightMargin=40,
        topMargin=45,
        bottomMargin=45
    )

    styles = getSampleStyleSheet()
    
    # Custom Palette
    COLOR_PRIMARY = colors.HexColor("#1E3A8A")     # Navy Blue
    COLOR_SECONDARY = colors.HexColor("#0D9488")   # Teal
    COLOR_DARK = colors.HexColor("#1F2937")        # Dark Charcoal Text
    COLOR_MUTED = colors.HexColor("#4B5563")       # Medium Gray
    COLOR_BG_BOX = colors.HexColor("#F8FAFC")      # Slate 50
    COLOR_ACCENT = colors.HexColor("#D97706")      # Amber/Gold

    # Custom Paragraph Styles
    style_title = ParagraphStyle(
        'DocTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=20,
        leading=24,
        textColor=COLOR_PRIMARY,
        spaceAfter=6
    )
    
    style_subtitle = ParagraphStyle(
        'DocSubtitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=11,
        leading=15,
        textColor=COLOR_MUTED,
        spaceAfter=15
    )

    style_h1 = ParagraphStyle(
        'SectionH1',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=13,
        leading=17,
        textColor=COLOR_PRIMARY,
        spaceBefore=14,
        spaceAfter=8,
        keepWithNext=True
    )

    style_h2 = ParagraphStyle(
        'SectionH2',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=11,
        leading=15,
        textColor=COLOR_SECONDARY,
        spaceBefore=10,
        spaceAfter=4,
        keepWithNext=True
    )

    style_body = ParagraphStyle(
        'BodyDark',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9.5,
        leading=13.5,
        textColor=COLOR_DARK,
        spaceAfter=6
    )

    style_meta = ParagraphStyle(
        'MetaInfo',
        parent=styles['Normal'],
        fontName='Helvetica-Oblique',
        fontSize=8.5,
        leading=12,
        textColor=COLOR_MUTED,
        spaceAfter=6
    )

    style_step = ParagraphStyle(
        'StepText',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9,
        leading=13,
        textColor=COLOR_DARK
    )

    style_step_actor = ParagraphStyle(
        'StepActor',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=9,
        leading=13,
        textColor=COLOR_PRIMARY
    )

    style_table_header = ParagraphStyle(
        'TableHeader',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=9,
        leading=12,
        textColor=colors.white,
        alignment=0
    )

    style_table_cell = ParagraphStyle(
        'TableCell',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=11.5,
        textColor=COLOR_DARK
    )

    story = []

    # ---------------------------------------------------------
    # HEADER / TITLE BANNER
    # ---------------------------------------------------------
    story.append(Paragraph("PANDUAN & CONTOH LIST ALUR UNTUK ACTIVITY DIAGRAM", style_title))
    story.append(Paragraph("Dokumen Acuan Struktur Langkah-demi-Langkah (Step-by-Step) Perancangan UML Activity Diagram", style_subtitle))
    story.append(HRFlowable(width="100%", thickness=2, color=COLOR_PRIMARY, spaceBefore=0, spaceAfter=12))

    # ---------------------------------------------------------
    # SECTION 1: PENDAHULUAN & KONSEP DASAR
    # ---------------------------------------------------------
    story.append(Paragraph("1. Pendahuluan & Konsep Dasar Activity Diagram", style_h1))
    
    intro_p1 = (
        "<b>Activity Diagram</b> adalah salah satu diagram standar dalam Unified Modeling Language (UML) yang "
        "digunakan untuk memetakan alur kerja (<i>workflow</i>) bisnis atau alur eksekusi sistem perangkat lunak secara visual. "
        "Sebelum menggambar diagram visual di perangkat lunak perancangan (seperti StarUML, Draw.io, atau PlantUML), "
        "sangat direkomendasikan untuk menyusun <b>Daftar Alur Langkah-demi-Langkah (List Steps)</b> terlebih dahulu."
    )
    story.append(Paragraph(intro_p1, style_body))

    intro_box_content = [
        [Paragraph("<b>Mengapa Menyusun List Alur Terlebih Dahulu Sangat Penting?</b>", style_h2)],
        [Paragraph(
            "1. <b>Kejelasan Swimlanes (Aktor vs Sistem):</b> Memisahkan mana tindakan yang dilakukan oleh Pengguna (User Action) dan mana proses otomatis yang ditangani Sistem (System Response).<br/>"
            "2. <b>Pemetaan Titik Keputusan (Decision Points):</b> Mengidentifikasi lokasi percabangan kondisi validasi (misalnya: input valid vs tidak valid, password cocok vs salah).<br/>"
            "3. <b>Penanganan Kondisi Gagal (Edge Cases):</b> Memastikan skenario kegagalan (seperti akun terblokir, stok habis, token kadaluarsa) terdokumentasi dengan jelas.",
            style_body
        )]
    ]
    t_intro = Table(intro_box_content, colWidths=[515])
    t_intro.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), COLOR_BG_BOX),
        ('BOX', (0, 0), (-1, -1), 0.75, colors.HexColor("#CBD5E1")),
        ('PADDING', (0, 0), (-1, -1), 8),
        ('BOTTOMPADDING', (0, 0), (-1, 0), 2),
    ]))
    story.append(t_intro)
    story.append(Spacer(1, 12))

    # Helper function to create standard workflow table boxes
    def build_example_card(num_str, title_str, initial_state, steps_list, final_state):
        card_content = []
        
        # Header title
        head_text = f"<b>Contoh {num_str}: {title_str}</b>"
        card_content.append([Paragraph(head_text, style_h2)])
        
        # Initial & Final State info
        meta_html = f"<b>Kondisi Awal (Initial State):</b> {initial_state}<br/><b>Kondisi Akhir (Final State):</b> {final_state}"
        card_content.append([Paragraph(meta_html, style_meta)])
        
        # Step table inside
        step_rows = [
            [Paragraph("<b>No</b>", style_table_header), 
             Paragraph("<b>Pelaku / Swimlane</b>", style_table_header), 
             Paragraph("<b>Deskripsi Aktivitas & Logic Percabangan</b>", style_table_header)]
        ]
        
        for idx, (actor, desc) in enumerate(steps_list, 1):
            actor_fmt = f"<b>[{actor}]</b>" if actor in ["Pengguna", "User", "Sistem", "Payment Gateway"] else actor
            step_rows.append([
                Paragraph(str(idx), style_table_cell),
                Paragraph(actor_fmt, style_step_actor if actor != "Sistem" else style_step),
                Paragraph(desc, style_step)
            ])
            
        t_steps = Table(step_rows, colWidths=[25, 110, 360])
        t_steps.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, 0), COLOR_PRIMARY),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
            ('ALIGN', (0, 0), (0, -1), 'CENTER'),
            ('VALIGN', (0, 0), (-1, -1), 'TOP'),
            ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor("#E2E8F0")),
            ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, colors.HexColor("#F8FAFC")]),
            ('TOPPADDING', (0, 0), (-1, -1), 4),
            ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
        ]))
        
        card_content.append([t_steps])
        
        card_table = Table(card_content, colWidths=[515])
        card_table.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, -1), colors.white),
            ('BOX', (0, 0), (-1, -1), 1, colors.HexColor("#CBD5E1")),
            ('PADDING', (0, 0), (-1, -1), 8),
            ('BOTTOMPADDING', (0, 0), (-1, 0), 2),
            ('BOTTOMPADDING', (0, 1), (-1, 1), 6),
        ]))
        return KeepTogether([card_table, Spacer(1, 12)])

    # ---------------------------------------------------------
    # SECTION 2: DAFTAR CONTOH ALUR ACTIVITY DIAGRAM
    # ---------------------------------------------------------
    story.append(Paragraph("2. Daftar Contoh Alur Activity Diagram (Step-by-Step List)", style_h1))
    story.append(Paragraph(
        "Berikut adalah 8 kumpulan contoh penulisan alur list aktivitas untuk berbagai use case standar pada aplikasi web/mobile. "
        "Format ini dirancang agar mudah ditransformasikan secara langsung menjadi Swimlanes, Decision Nodes, dan Activity States.",
        style_body
    ))
    story.append(Spacer(1, 4))

    # --- CONTOH 1: LOGIN ---
    ex1_steps = [
        ("Pengguna", "Membuka halaman web login aplikasi."),
        ("Sistem", "Menampilkan form login yang berisi inputan <i>Email/Username</i> dan <i>Password</i> serta tombol <b>Login</b>."),
        ("Pengguna", "Memasukkan email dan password, kemudian mengeklik tombol <b>Login</b>."),
        ("Sistem", "Menerima data inputan dan melakukan <b>Validasi Kelengkapan Data</b>:<br/>"
                   "• <i>Jika data kosong / format salah:</i> Tampilkan pesan kesalahan &quot;Input wajib diisi&quot; &rarr; <i>(Kembali ke Langkah 2)</i>.<br/>"
                   "• <i>Jika data lengkap:</i> Lanjutkan ke pencocokan kredensial."),
        ("Sistem", "Memeriksa kecocokan data kredensial ke Database:<br/>"
                   "• <b>[Decision: Kredensial Salah]</b><br/>"
                   "&nbsp;&nbsp;- Sistem menambah hitungan gagal login (+1).<br/>"
                   "&nbsp;&nbsp;- Sistem menampilkan notifikasi error &quot;Email atau password salah&quot;.<br/>"
                   "&nbsp;&nbsp;- <i>(Jika gagal &ge; 3 kali):</i> Sistem memblokir akun sementara selama 15 menit.<br/>"
                   "&nbsp;&nbsp;- <i>(Kembali ke Langkah 2)</i>.<br/>"
                   "• <b>[Decision: Kredensial Benar]</b><br/>"
                   "&nbsp;&nbsp;- Sistem membuatkan sesi login aktif (Session / Access Token JWT).<br/>"
                   "&nbsp;&nbsp;- Sistem mengarahkan (redirect) pengguna ke halaman <b>Dashboard Utama</b>."),
        ("Pengguna", "Pengguna berhasil masuk dan melihat halaman Dashboard Utama.")
    ]
    story.append(build_example_card(
        "1", "Autentikasi Login Pengguna",
        "Pengguna berada di halaman publik dan mengeklik tombol Login.",
        ex1_steps,
        "Pengguna berhasil terautentikasi dan berada di halaman Dashboard."
    ))

    # --- CONTOH 2: REGISTRASI ---
    ex2_steps = [
        ("Pengguna", "Membuka halaman Registrasi / Pendaftaran Akun Baru."),
        ("Sistem", "Menampilkan formulir pendaftaran (Nama Lengkap, Email, Password, Konfirmasi Password)."),
        ("Pengguna", "Mengisi seluruh field data pendaftaran lalu mengeklik tombol <b>Daftar Akun</b>."),
        ("Sistem", "Memvalidasi inputan formulir:<br/>"
                   "• <i>Cek kesesuaian Password & Konfirmasi Password.</i><br/>"
                   "• <i>Cek keunikan email di database.</i><br/>"
                   "• <b>[Decision: Invalid / Email Terdaftar]</b> Tampilkan pesan error spesifik &rarr; <i>(Kembali ke Langkah 2)</i>.<br/>"
                   "• <b>[Decision: Valid]</b> Lanjutkan penyimpanan."),
        ("Sistem", "Melakukan hash enkripsi password (BCrypt/Argon2) dan menyimpan data user baru ke Database dengan status <i>'Pending Verification'</i>."),
        ("Sistem", "Mengirimkan email berisi Kode OTP / Link Verifikasi ke email pengguna."),
        ("Pengguna", "Membuka email dan memasukkan Kode OTP pada formulir verifikasi."),
        ("Sistem", "Memeriksa keabsahan Kode OTP:<br/>"
                   "• <i>Jika OTP Salah / Expired:</i> Tampilkan pesan error &rarr; Opsi kirim ulang OTP.<br/>"
                   "• <i>Jika OTP Valid:</i> Ubah status akun menjadi <i>'Active'</i>."),
        ("Sistem", "Menampilkan notifikasi &quot;Registrasi Berhasil&quot; dan mengarahkan ke halaman Login.")
    ]
    story.append(build_example_card(
        "2", "Registrasi Akun Baru & Verifikasi OTP",
        "Pengguna belum memiliki akun dan membuka form registrasi.",
        ex2_steps,
        "Akun baru terbuat, terverifikasi, dan siap digunakan untuk login."
    ))

    # --- CONTOH 3: RESET PASSWORD ---
    ex3_steps = [
        ("Pengguna", "Mengeklik link <b>Lupa Password?</b> pada halaman login."),
        ("Sistem", "Menampilkan form input Email Pemulihan."),
        ("Pengguna", "Memasukkan email terdaftar dan mengeklik <b>Kirim Link Reset</b>."),
        ("Sistem", "Memeriksa keberadaan email pada database:<br/>"
                   "• <i>Jika Email Tidak Ditemukan:</i> Tampilkan notifikasi &quot;Email tidak terdaftar&quot;.<br/>"
                   "• <i>Jika Email Ditemukan:</i> Generate Secure Token dengan batas waktu (misal: 60 menit) dan kirimkan link reset via email."),
        ("Pengguna", "Membuka inbox email dan mengeklik link reset password yang dikirimkan."),
        ("Sistem", "Memvalidasi token pada link:<br/>"
                   "• <i>Jika Token Kadaluarsa / Tidak Valid:</i> Tampilkan pesan error &quot;Link sudah kadaluarsa&quot;.<br/>"
                   "• <i>Jika Token Valid:</i> Menampilkan formulir pembuatan Password Baru."),
        ("Pengguna", "Memasukkan Password Baru & Konfirmasi Password Baru, lalu klik <b>Simpan Password</b>."),
        ("Sistem", "Memperbarui password terenkripsi di database dan menghapus token reset."),
        ("Sistem", "Menampilkan notifikasi &quot;Password Berhasil Diubah&quot; dan redirect ke halaman Login.")
    ]
    story.append(build_example_card(
        "3", "Lupa Password & Reset Password",
        "Pengguna tidak dapat login karena lupa kata sandi.",
        ex3_steps,
        "Kata sandi pengguna telah diperbarui di database."
    ))

    # --- CONTOH 4: TAMBAH DATA (CREATE/INPUT) ---
    ex4_steps = [
        ("Pengguna", "Mengeklik tombol <b>+ Tambah Data Baru</b> pada halaman kelola data."),
        ("Sistem", "Menampilkan Form / Modal Tambah Data."),
        ("Pengguna", "Mengisi data yang diperlukan (misal: Nama, Kategori, Deskripsi, Upload Gambar) dan mengeklik <b>Simpan</b>."),
        ("Sistem", "Memvalidasi data inputan:<br/>"
                   "• <i>Jika ada kolom wajib yang kosong atau ukuran file melebihi batas:</i> Tampilkan pesan error validasi di formulir &rarr; <i>(Kembali ke Langkah 2)</i>.<br/>"
                   "• <i>Jika validasi sukses:</i> Lanjutkan proses simpan."),
        ("Sistem", "Mengunggah berkas gambar ke Server Storage dan menyimpan data baru ke Database."),
        ("Sistem", "Menampilkan notifikasi toast &quot;Data Berhasil Ditambahkan&quot;, menutup modal, dan memperbarui (refresh) tabel data.")
    ]
    story.append(build_example_card(
        "4", "Tambah Data Baru (Input Form / Create)",
        "Pengguna berada pada halaman daftar tabel data.",
        ex4_steps,
        "Data baru tersimpan di database dan muncul pada tabel."
    ))

    # --- CONTOH 5: EDIT DATA (UPDATE) ---
    ex5_steps = [
        ("Pengguna", "Mengeklik tombol ikon <b>Edit</b> pada baris data tertentu di tabel."),
        ("Sistem", "Mengambil data detail berdasarkan ID dari database."),
        ("Sistem", "Menampilkan Form Edit yang sudah terisi otomatis (pre-filled) dengan data lama."),
        ("Pengguna", "Mengubah informasi data yang ingin diperbarui lalu mengeklik <b>Simpan Perubahan</b>."),
        ("Sistem", "Memeriksa perubahan dan memvalidasi inputan:<br/>"
                   "• <i>Jika data invalid:</i> Menampilkan pesan peringatan.<br/>"
                   "• <i>Jika data valid:</i> Menjalankan query UPDATE pada database."),
        ("Sistem", "Menyimpan perubahan ke database dan mencatat log aktivitas (Audit Log)."),
        ("Sistem", "Menampilkan pesan &quot;Data Berhasil Diperbarui&quot; dan merefresh data tabel.")
    ]
    story.append(build_example_card(
        "5", "Ubah / Edit Data (Update Form)",
        "Pengguna memilih salah satu item data untuk diubah.",
        ex5_steps,
        "Perubahan data tersimpan di database dan tampilan terbarui."
    ))

    # --- CONTOH 6: HAPUS DATA (DELETE) ---
    ex6_steps = [
        ("Pengguna", "Mengeklik tombol <b>Hapus</b> pada item data yang dipilih."),
        ("Sistem", "Menampilkan modal pop-up konfirmasi: <i>&quot;Apakah Anda yakin ingin menghapus data ini? Data yang dihapus tidak dapat dikembalikan.&quot;</i>"),
        ("Pengguna", "Memilih salah satu opsi tindakan:<br/>"
                   "• <b>[Pilih Batal]:</b> Modal tertutup, proses dibatalkan.<br/>"
                   "• <b>[Pilih Ya, Hapus]:</b> Lanjutkan proses penghapusan."),
        ("Sistem", "Melakukan eksekusi penghapusan di database (Soft Delete `deleted_at` atau Hard Delete `DELETE`)."),
        ("Sistem", "Menampilkan notifikasi &quot;Data Berhasil Dihapus&quot; dan menghilangkan baris data tersebut dari tabel secara dinamis.")
    ]
    story.append(build_example_card(
        "6", "Hapus Data dengan Konfirmasi Modal",
        "Pengguna ingin menghapus data yang tidak diperlukan.",
        ex6_steps,
        "Data terhapus dari sistem dan tidak tampil di tabel."
    ))

    # --- CONTOH 7: TRANSAKSI & PARALEL (FORK-JOIN) ---
    ex7_steps = [
        ("Pengguna", "Mengeklik tombol <b>Bayar Sekarang / Checkout</b> pada keranjang belanja."),
        ("Sistem", "Memeriksa ketersediaan stok barang:<br/>"
                   "• <i>Jika Stok Habis:</i> Tampilkan notifikasi &quot;Stok barang tidak mencukupi&quot; &rarr; <i>(Batal)</i>.<br/>"
                   "• <i>Jika Stok Tersedia:</i> Memulai proses paralel <b>(Fork Node)</b>."),
        ("Sistem", "<b>[Proses Paralel A]:</b> Mengunci (lock) stok barang sementara agar tidak diambil pembeli lain.<br/>"
                   "<b>[Proses Paralel B]:</b> Meng-generate Kode Pembayaran Virtual Account / QRIS via Payment Gateway."),
        ("Sistem", "Menggabungkan kedua hasil proses <b>(Join Node)</b> dan menampilkan Halaman Instruksi Pembayaran beserta hitung mundur batas waktu (Timer)."),
        ("Pengguna", "Melakukan transfer pembayaran melalui m-Banking / e-Wallet."),
        ("Payment Gateway", "Mengirimkan notifikasi callback / webhook status pembayaran ke Sistem."),
        ("Sistem", "Menerima notifikasi callback dan mengecek status transaksi:<br/>"
                   "• <b>[Status Expired/Gagal]:</b> Lepas kunci stok & ubah status pesanan menjadi <i>'Dibatalkan'</i>.<br/>"
                   "• <b>[Status Lunas]:</b> Ubah status pesanan menjadi <i>'Diproses'</i> & kirim struk belanja ke email."),
        ("Sistem", "Menampilkan halaman konfirmasi &quot;Pembayaran Berhasil&quot; kepada pengguna.")
    ]
    story.append(build_example_card(
        "7", "Checkout Pembayaran (Skenario Paralel Fork/Join & Webhook)",
        "Pengguna memiliki barang di keranjang dan siap membayar.",
        ex7_steps,
        "Pembayaran terverifikasi dan pesanan masuk ke antrean pengiriman."
    ))

    # --- CONTOH 8: LOGOUT ---
    ex8_steps = [
        ("Pengguna", "Mengeklik menu akun dan memilih opsi <b>Logout / Keluar</b>."),
        ("Sistem", "Menampilkan pop-up dialog konfirmasi keluar."),
        ("Pengguna", "Mengeklik tombol <b>Ya, Keluar</b>."),
        ("Sistem", "Menghapus data Session / menginvalidsasi Token Auth JWT pada server dan menghapus Cookie pada browser pengguna."),
        ("Sistem", "Mengarahkan (redirect) pengguna ke halaman Halaman Utama / Login dengan pesan notifikasi &quot;Anda telah berhasil keluar&quot;.")
    ]
    story.append(build_example_card(
        "8", "Logout / Keluar Sesi Aplikasi",
        "Pengguna dalam posisi terautentikasi (login).",
        ex8_steps,
        "Sesi pengguna hancur dan kembali ke status tamu (guest)."
    ))

    # ---------------------------------------------------------
    # SECTION 3: CHEAT-SHEET PEMETAAN SIMBOL UML
    # ---------------------------------------------------------
    story.append(Paragraph("3. Cheat-Sheet: Pemetaan Teks List ke Simbol Activity Diagram", style_h1))
    story.append(Paragraph(
        "Tabel berikut mempermudah konversi dari tulisan teks list alur di atas menjadi simbol-simbol standar pada diagram UML:",
        style_body
    ))
    story.append(Spacer(1, 4))

    mapping_data = [
        [Paragraph("<b>Notasi / Element UML</b>", style_table_header),
         Paragraph("<b>Simbol Visual</b>", style_table_header),
         Paragraph("<b>Pemicu Kata Kunci dalam List</b>", style_table_header),
         Paragraph("<b>Fungsi & Penjelasan</b>", style_table_header)],
        
        [Paragraph("<b>Initial Node</b>", style_table_cell),
         Paragraph("Lingkaran Hitam Solid (●)", style_table_cell),
         Paragraph("<i>&quot;Kondisi Awal / Start&quot;</i>", style_table_cell),
         Paragraph("Menandai titik awal dimulainya suatu aktivitas atau workflow.", style_table_cell)],

        [Paragraph("<b>Swimlanes / Partition</b>", style_table_cell),
         Paragraph("Kolom Vertikal / Horizontal", style_table_cell),
         Paragraph("<b>[Pengguna]</b>, <b>[Sistem]</b>, <b>[Admin]</b>", style_table_cell),
         Paragraph("Memisahkan tanggung jawab siapa yang melakukan aksi tertentu.", style_table_cell)],

        [Paragraph("<b>Action / Activity State</b>", style_table_cell),
         Paragraph("Kotak Ujung Tumpul (Oval)", style_table_cell),
         Paragraph("<i>&quot;Memasukkan email&quot;, &quot;Validasi input&quot;</i>", style_table_cell),
         Paragraph("Merepresentasikan satu unit pekerjaan atau tindakan spesifik.", style_table_cell)],

        [Paragraph("<b>Decision Node</b>", style_table_cell),
         Paragraph("Belah Ketupat / Diamond (◇)", style_table_cell),
         Paragraph("<i>&quot;Jika ... Maka ...&quot;, &quot;Decision: Valid / Invalid&quot;</i>", style_table_cell),
         Paragraph("Menunjukkan titik evaluasi kondisi yang memecah alur menjadi 2 arah atau lebih.", style_table_cell)],

        [Paragraph("<b>Fork Node</b>", style_table_cell),
         Paragraph("Garis Tebal Solid (1 Input &rarr; Multi Output)", style_table_cell),
         Paragraph("<i>&quot;Proses Paralel / Bersamaan&quot;</i>", style_table_cell),
         Paragraph("Memecah alur tunggal menjadi beberapa cabang yang berjalan secara paralel.", style_table_cell)],

        [Paragraph("<b>Join Node</b>", style_table_cell),
         Paragraph("Garis Tebal Solid (Multi Input &rarr; 1 Output)", style_table_cell),
         Paragraph("<i>&quot;Menggabungkan hasil proses&quot;</i>", style_table_cell),
         Paragraph("Menggabungkan kembali beberapa cabang paralel sebelum lanjut ke langkah berikutnya.", style_table_cell)],

        [Paragraph("<b>Activity Final Node</b>", style_table_cell),
         Paragraph("Lingkaran Hitam Berbingkai (◉)", style_table_cell),
         Paragraph("<i>&quot;Kondisi Akhir / Selesai&quot;</i>", style_table_cell),
         Paragraph("Menandai akhir dari seluruh rangkaian alur aktivitas dalam diagram.", style_table_cell)]
    ]

    t_map = Table(mapping_data, colWidths=[90, 110, 130, 185])
    t_map.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), COLOR_PRIMARY),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, colors.HexColor("#F8FAFC")]),
        ('TOPPADDING', (0, 0), (-1, -1), 5),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 5),
    ]))

    story.append(KeepTogether([t_map, Spacer(1, 14)]))

    # ---------------------------------------------------------
    # SECTION 4: TIPS & TRICKS
    # ---------------------------------------------------------
    story.append(Paragraph("4. Tips Tambahan dalam Menyusun Activity Diagram", style_h1))
    tips_html = (
        "• <b>Gunakan Kata Kerja Aktif:</b> Tuliskan aksi dengan jelas, contoh: <i>'Mengisi form'</i>, <i>'Mengirim data'</i>, <i>'Memvalidasi password'</i>.<br/>"
        "• <b>Pastikan Setiap Decision Punya Guard Condition:</b> Labeli setiap panah yang keluar dari belah ketupat dengan kondisi jelas seperti <code>[Valid]</code> atau <code>[Tidak Valid]</code>.<br/>"
        "• <b>Hindari Kerumitan Berlebihan:</b> Jika alur terlalu panjang (lebih dari 15 langkah), pecah diagram menjadi sub-diagram aktivitas tersendiri.<br/>"
        "• <b>Konsistensi Swimlane:</b> Pastikan garis panah antar swimlane mengalir dengan logis dari kiri ke kanan atau dari atas ke bawah."
    )
    story.append(Paragraph(tips_html, style_body))

    # Build Document
    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"PDF berhasil dibuat: {os.path.abspath(filename)}")

if __name__ == "__main__":
    create_activity_diagram_pdf()
