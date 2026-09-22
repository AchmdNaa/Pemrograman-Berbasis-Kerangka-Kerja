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
            g.SmoothingMode = SmoothingMode.AntiAlias; // Kunci gambar HD tidak pecah
            g.PixelOffsetMode = PixelOffsetMode.HighQuality;

            Rectangle rect = new Rectangle(0, 0, this.Width, this.Height);

            using (GraphicsPath path = GetPillPath(rect))
            using (SolidBrush brush = new SolidBrush(this.BackColor))
            {
                g.FillPath(brush, path);
            }

            // Render Teks Presisi
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

            // Baris 1
            CreateHdBtn("AC", startX, startY, size, size, colOrange, colTextWhite, fontButton, BtnClear_Click);
            CreateHdBtn("±", startX + (size + gap), startY, size, size, colLightGray, colTextBlack, fontButton, BtnNegatif_Click);
            CreateHdBtn("%", startX + 2 * (size + gap), startY, size, size, colLightGray, colTextBlack, fontButton, BtnPersen_Click);
            CreateHdBtn("÷", startX + 3 * (size + gap), startY, size, size, colOrange, colTextWhite, fontButton, BtnOperator_Click);

            // Baris 2
            CreateHdBtn("7", startX, startY + (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("8", startX + (size + gap), startY + (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("9", startX + 2 * (size + gap), startY + (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("×", startX + 3 * (size + gap), startY + (size + gap), size, size, colOrange, colTextWhite, fontButton, BtnOperator_Click);

            // Baris 3
            CreateHdBtn("4", startX, startY + 2 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("5", startX + (size + gap), startY + 2 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("6", startX + 2 * (size + gap), startY + 2 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("-", startX + 3 * (size + gap), startY + 2 * (size + gap), size, size, colOrange, colTextWhite, fontButton, BtnOperator_Click);

            // Baris 4
            CreateHdBtn("1", startX, startY + 3 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("2", startX + (size + gap), startY + 3 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("3", startX + 2 * (size + gap), startY + 3 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click);
            CreateHdBtn("+", startX + 3 * (size + gap), startY + 3 * (size + gap), size, size, colOrange, colTextWhite, fontButton, BtnOperator_Click);

            // Baris 5
            int zeroWidth = size * 2 + gap;
            CreateHdBtn("0", startX, startY + 4 * (size + gap), zeroWidth, size, colDarkGray, colTextWhite, fontButton, BtnAngka_Click, true);
            CreateHdBtn(".", startX + 2 * (size + gap), startY + 4 * (size + gap), size, size, colDarkGray, colTextWhite, fontButton, BtnTitik_Click);
            CreateHdBtn("=", startX + 3 * (size + gap), startY + 4 * (size + gap), size, size, colOrange, colTextWhite, fontButton, BtnSamaDengan_Click);

            // Form Utama
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