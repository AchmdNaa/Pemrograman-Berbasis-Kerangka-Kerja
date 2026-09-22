# Tugas Pemrograman Berbasis Kerangka Kerja (PBKK) — Minggu 3

**Nama:** Achmad Najwa
**NIM:** 5025231265
**Departemen:** Teknik Informatika

---

## Daftar Isi

1. [Landasan Teori: Antarmuka Grafis Desktop & Custom Rendering](#1-landasan-teori-antarmuka-grafis-desktop--custom-rendering)
2. [Deskripsi Aplikasi Kalkulator (iOS Dark Theme)](#2-deskripsi-aplikasi-kalkulator-ios-dark-theme)
3. [Langkah Pengerjaan & Setup](#3-langkah-pengerjaan--setup)
4. [Implementasi Kode Program](#4-implementasi-kode-program)
   - [4.1 Desain Antarmuka & HD Rendering (Form1.Designer.cs)](#41-desain-antarmuka--hd-rendering-form1designercs)
   - [4.2 Logika Aritmetika & State Management (Form1.cs)](#42-logika-aritmetika--state-management-form1cs)
   - [4.3 Titik Masuk Aplikasi (Program.cs)](#43-titik-masuk-aplikasi-programcs)
5. [Dokumentasi Pengujian Aplikasi](#5-dokumentasi-pengujian-aplikasi)

---

## 1. Landasan Teori: Antarmuka Grafis Desktop & Custom Rendering

Pengembangan antarmuka pada kerangka kerja .NET Windows Forms (WinForms) melibatkan manipulasi grafis tingkat lanjut untuk menghasilkan elemen visual modern:

- **Vector-based Custom Rendering** — Kontrol bawaan Windows Forms memiliki keterbatasan dalam merender bentuk geometris melengkung (*rounded corners*) dengan resolusi tinggi. Pemanfaatan antialiasing melalui modul `System.Drawing.Drawing2D` (`SmoothingMode.AntiAlias` dan `PixelOffsetMode.HighQuality`) memungkinkan kurva tombol digambar secara presisi tanpa artefak piksel bergerigi (*jagged edges*).
- **Event-Driven Programming** — Pola komputasi kalkulator bekerja berbasis respons aksi tombol (*event handler*), di mana setiap ketukan angka, operator, maupun fungsi manipulasi tanda mengubah keadaan (*state*) variabel penampung secara dinamis.
- **State Management & Chained Operations** — Kalkulator modern memerlukan pencatatan riwayat ekspresi secara berurutan (*chained calculation*), membedakan antara angka input baru, operator tunda (*pending operator*), serta eksekusi evaluasi hasil akhir.

---

## 2. Deskripsi Aplikasi Kalkulator (iOS Dark Theme)

Aplikasi ini mengadopsi gaya visual iOS Dark Mode Calculator dengan spesifikasi teknis sebagai berikut:

- **Estetika iOS Elegan** — Latar belakang hitam pekat (`#000000`), tombol bulat sempurna dengan palet warna abu-abu gelap (`#333333`), tombol fungsi abu-abu terang (`#A5A5A5`), dan tombol aksi/operator oranye khas Apple (`#FF9F0A`).
- **Layar Ganda (Dual Display)** — Menampilkan baris riwayat ekspresi operasi di bagian atas serta angka utama berukuran besar dengan format pemisah ribuan (*thousand separator*).
- **Operasi Aritmetika Lengkap** — Penjumlahan (+), Pengurangan (−), Perkalian (×), Pembagian (÷), Persentase (%), Negasi/Inversi tanda (±), dan tombol Reset Total (AC).

---

## 3. Langkah Pengerjaan & Setup

Jalankan perintah berikut secara berurutan pada terminal PowerShell:

```powershell
# 1. Masuk ke direktori tugas Minggu-03
cd "D:\Semester 7\Pemrograman-Berbasis-Kerangka-Kerja\Minggu-03"

# 2. Buat proyek Windows Forms baru
dotnet new winforms -n kalkulator
cd kalkulator

# 3. Buka editor VS Code
code .
```

---

## 4. Implementasi Kode Program

### 4.1 Desain Antarmuka & HD Rendering (Form1.Designer.cs)

Berkas ini mengelola tata letak kontrol, pembuatan tombol bulat HD dengan teknik lukis manual (*custom paint*), dan palet warna antarmuka:

```csharp
using System;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Windows.Forms;

namespace Kalkulator
{
    // Komponen Tombol Kustom dengan Anti-Aliasing (Ultra Smooth / HD)
    public class RoundButton : Button
    {
        public bool IsZero { get; set; } = false;

        public RoundButton()
        {
            this.FlatStyle = FlatStyle.Flat;
            this.FlatAppearance.BorderSize = 0;
            this.Cursor = Cursors.Hand;
            this.SetStyle(ControlStyles.UserPaint | ControlStyles.AllPaintingInWmPaint | ControlStyles.OptimizedDoubleBuffer, true);
        }

        protected override void OnPaint(PaintEventArgs pevent)
        {
            Graphics g = pevent.Graphics;
            g.SmoothingMode = SmoothingMode.AntiAlias;
            g.PixelOffsetMode = PixelOffsetMode.HighQuality;

            Rectangle rect = new Rectangle(0, 0, this.Width, this.Height);

            using (GraphicsPath path = GetPillPath(rect))
            using (SolidBrush brush = new SolidBrush(this.BackColor))
            {
                g.FillPath(brush, path);
            }

            TextFormatFlags flags = TextFormatFlags.VerticalCenter | TextFormatFlags.SingleLine;
            if (IsZero)
            {
                flags |= TextFormatFlags.Left;
                rect.X += 24;
            }
            else
            {
                flags |= TextFormatFlags.HorizontalCenter;
            }

            TextRenderer.DrawText(g, this.Text, this.Font, rect, this.ForeColor, flags);
        }

        private GraphicsPath GetPillPath(Rectangle rect)
        {
            GraphicsPath path = new GraphicsPath();
            int radius = rect.Height;
            path.AddArc(rect.X, rect.Y, radius, radius, 90, 180);
            path.AddArc(rect.Right - radius, rect.Y, radius, radius, 270, 180);
            path.CloseFigure();
            return path;
        }
    }

    partial class Form1
    {
        private System.ComponentModel.IContainer components = null;
        private System.Windows.Forms.Label lblHistory;
        private System.Windows.Forms.Label lblDisplay;

        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        private void InitializeComponent()
        {
            this.lblHistory = new System.Windows.Forms.Label();
            this.lblDisplay = new System.Windows.Forms.Label();
            this.SuspendLayout();

            Font fontHistory = new Font("Segoe UI", 12F, FontStyle.Regular, GraphicsUnit.Point);
            Font fontDisplay = new Font("Segoe UI", 36F, FontStyle.Regular, GraphicsUnit.Point);
            Font fontButton = new Font("Segoe UI", 18F, FontStyle.Regular, GraphicsUnit.Point);

            // Layar Riwayat Hitungan
            this.lblHistory.Font = fontHistory;
            this.lblHistory.ForeColor = Color.FromArgb(142, 142, 147);
            this.lblHistory.Location = new Point(20, 25);
            this.lblHistory.Size = new Size(310, 25);
            this.lblHistory.TextAlign = ContentAlignment.MiddleRight;
            this.lblHistory.Text = "";

            // Layar Angka Utama
            this.lblDisplay.Font = fontDisplay;
            this.lblDisplay.ForeColor = Color.White;
            this.lblDisplay.Location = new Point(20, 55);
            this.lblDisplay.Size = new Size(310, 75);
            this.lblDisplay.TextAlign = ContentAlignment.MiddleRight;
            this.lblDisplay.Text = "0";

            // Palet Warna Asli iOS
            Color colDarkGray = Color.FromArgb(51, 51, 51);
            Color colOrange = Color.FromArgb(255, 159, 10);
            Color colLightGray = Color.FromArgb(165, 165, 165);
            Color colTextBlack = Color.Black;
            Color colTextWhite = Color.White;

            int size = 65, gap = 14, startX = 20, startY = 145;

            // Baris 1: AC, ±, %, ÷
            CreateHdBtn("AC", startX, startY, size, size, colOrange, colTextWhite, fontButton, BtnClear_Click);
            CreateHdBtn("±", startX + (size + gap), startY, size, size, colLightGray, colTextBlack, fontButton, BtnNegatif_Click);
            CreateHdBtn("%", startX + 2 * (size + gap), startY, size, size, colLightGray, colTextBlack, fontButton, BtnPersen_Click);
            CreateHdBtn("÷", startX + 3 * (size + gap), startY, size, size, colOrange, colTextWhite, fontButton, BtnOperator_Click);

            // Baris 2: 7, 8, 9, ×
            CreateHdBtn("7", startX, startY + (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("8", startX + (size + gap), startY + (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("9", startX + 2 * (size + gap), startY + (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("×", startX + 3 * (size + gap), startY + (size + gap), size, size, colOrange, colTextWhite, fontButton, BtnOperator_Click);

            // Baris 3: 4, 5, 6, -
            CreateHdBtn("4", startX, startY + 2 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("5", startX + (size + gap), startY + 2 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("6", startX + 2 * (size + gap), startY + 2 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("-", startX + 3 * (size + gap), startY + 2 * (size + gap), size, size, colOrange, colTextWhite, fontButton, BtnOperator_Click);

            // Baris 4: 1, 2, 3, +
            CreateHdBtn("1", startX, startY + 3 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("2", startX + (size + gap), startY + 3 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("3", startX + 2 * (size + gap), startY + 3 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("+", startX + 3 * (size + gap), startY + 3 * (size + gap), size, size, colOrange, colTextWhite, fontButton, BtnOperator_Click);

            // Baris 5: 0, ., =
            int zeroWidth = size * 2 + gap;
            CreateHdBtn("0", startX, startY + 4 * (size + gap), zeroWidth, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click, true);
            CreateHdBtn(".", startX + 2 * (size + gap), startY + 4 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnTitik_Click);
            CreateHdBtn("=", startX + 3 * (size + gap), startY + 4 * (size + gap), size, size, colOrange, colTextWhite, fontButton, BtnSamaDengan_Click);

            // Konfigurasi Form Utama
            this.ClientSize = new Size(350, 550);
            this.Controls.Add(this.lblHistory);
            this.Controls.Add(this.lblDisplay);
            this.Text = "Calculator";
            this.BackColor = Color.Black;
            this.FormBorderStyle = FormBorderStyle.FixedDialog;
            this.MaximizeBox = false;
            this.StartPosition = FormStartPosition.CenterScreen;
            this.ResumeLayout(false);
        }

        private void CreateHdBtn(string text, int x, int y, int w, int h, Color bg, Color fg, Font font, EventHandler onClick, bool isZero = false)
        {
            RoundButton btn = new RoundButton();
            btn.Text = text;
            btn.Location = new Point(x, y);
            btn.Size = new Size(w, h);
            btn.BackColor = bg;
            btn.ForeColor = fg;
            btn.Font = font;
            btn.IsZero = isZero;
            btn.Click += onClick;
            this.Controls.Add(btn);
        }
    }
}
```

### 4.2 Logika Aritmetika & State Management (Form1.cs)

Berkas ini mengelola seluruh logika komputasi aritmetika, pembaruan riwayat perhitungan, dan pemformatan angka:

```csharp
using System;
using System.Windows.Forms;

namespace Kalkulator
{
    public partial class Form1 : Form
    {
        private double totalHasil = 0;
        private string operasiPending = "";
        private bool sedangKetikAngkaBaru = true;
        private bool selesaiHitung = false;

        public Form1()
        {
            InitializeComponent();
        }

        private void FormatDisplay(double val)
        {
            lblDisplay.Text = val.ToString("#,##0.######");
        }

        private void BtnAngka_Click(object sender, EventArgs e)
        {
            Button btn = (Button)sender;
            string input = btn.Text;

            if (selesaiHitung)
            {
                lblHistory.Text = "";
                lblDisplay.Text = input;
                selesaiHitung = false;
                sedangKetikAngkaBaru = false;
                return;
            }

            if (sedangKetikAngkaBaru || lblDisplay.Text == "0")
            {
                lblDisplay.Text = input;
                sedangKetikAngkaBaru = false;
            }
            else
            {
                string raw = lblDisplay.Text.Replace(",", "");
                if (raw.Length < 9)
                {
                    lblDisplay.Text = raw + input;
                    if (double.TryParse(lblDisplay.Text, out double val))
                    {
                        FormatDisplay(val);
                    }
                }
            }
        }

        private void BtnTitik_Click(object sender, EventArgs e)
        {
            if (selesaiHitung)
            {
                lblHistory.Text = "";
                lblDisplay.Text = "0.";
                selesaiHitung = false;
                sedangKetikAngkaBaru = false;
                return;
            }

            if (sedangKetikAngkaBaru)
            {
                lblDisplay.Text = "0.";
                sedangKetikAngkaBaru = false;
            }
            else if (!lblDisplay.Text.Contains("."))
            {
                lblDisplay.Text += ".";
            }
        }

        private void BtnOperator_Click(object sender, EventArgs e)
        {
            Button btn = (Button)sender;
            string op = btn.Text;
            double angkaSekarang = double.TryParse(lblDisplay.Text.Replace(",", ""), out double val) ? val : 0;

            if (selesaiHitung)
            {
                lblHistory.Text = $"{totalHasil:#,##0.######} {op} ";
                operasiPending = op;
                selesaiHitung = false;
                sedangKetikAngkaBaru = true;
                return;
            }

            if (!sedangKetikAngkaBaru && !string.IsNullOrEmpty(operasiPending))
            {
                HitungHasil(angkaSekarang);
                FormatDisplay(totalHasil);
                lblHistory.Text += $"{angkaSekarang:#,##0.######} {op} ";
            }
            else
            {
                totalHasil = angkaSekarang;
                lblHistory.Text = $"{totalHasil:#,##0.######} {op} ";
            }

            operasiPending = op;
            sedangKetikAngkaBaru = true;
        }

        private void BtnSamaDengan_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrEmpty(operasiPending) || selesaiHitung) return;

            double angkaKedua = double.TryParse(lblDisplay.Text.Replace(",", ""), out double val) ? val : 0;
            lblHistory.Text += $"{angkaKedua:#,##0.######} =";

            HitungHasil(angkaKedua);
            FormatDisplay(totalHasil);

            operasiPending = "";
            sedangKetikAngkaBaru = true;
            selesaiHitung = true;
        }

        private void HitungHasil(double angka)
        {
            switch (operasiPending)
            {
                case "+": totalHasil += angka; break;
                case "-": totalHasil -= angka; break;
                case "×": totalHasil *= angka; break;
                case "÷":
                    if (angka == 0)
                    {
                        MessageBox.Show("Tidak dapat membagi dengan nol!", "Peringatan", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                        totalHasil = 0;
                    }
                    else
                    {
                        totalHasil /= angka;
                    }
                    break;
            }
        }

        private void BtnNegatif_Click(object sender, EventArgs e)
        {
            if (double.TryParse(lblDisplay.Text.Replace(",", ""), out double val))
            {
                val = -val;
                FormatDisplay(val);
            }
        }

        private void BtnPersen_Click(object sender, EventArgs e)
        {
            if (double.TryParse(lblDisplay.Text.Replace(",", ""), out double val))
            {
                val = val / 100.0;
                FormatDisplay(val);
                sedangKetikAngkaBaru = true;
            }
        }

        private void BtnClear_Click(object sender, EventArgs e)
        {
            lblDisplay.Text = "0";
            lblHistory.Text = "";
            totalHasil = 0;
            operasiPending = "";
            sedangKetikAngkaBaru = true;
            selesaiHitung = false;
        }
    }
}
```

### 4.3 Titik Masuk Aplikasi (Program.cs)

```csharp
using System;
using System.Windows.Forms;

namespace Kalkulator
{
    internal static class Program
    {
        [STAThread]
        static void Main()
        {
            ApplicationConfiguration.Initialize();
            Application.Run(new Form1());
        }
    }
}
```

---

## 5. Dokumentasi Pengujian Aplikasi

### Tampilan Awal Kalkulator

<!-- TODO: sisipkan screenshot tampilan awal, contoh:
![Tampilan awal kalkulator](kalkulator/images/TampilanAwal.png)
-->

### Pengujian Operasi Hitung Berantai & Riwayat Ekspresi

<!-- TODO: sisipkan screenshot pengujian operasi berantai, contoh:
![Pengujian operasi hitung berantai](kalkulator/images/OperasiBerantai.png)
-->
