namespace DataMahasiswaUI
{
    partial class Form1
    {
        private System.ComponentModel.IContainer components = null;
        private System.Windows.Forms.Panel panelHeader;
        private System.Windows.Forms.Label lblTitle;
        private System.Windows.Forms.Label lblNIM;
        private System.Windows.Forms.TextBox txtNIM;
        private System.Windows.Forms.Label lblNama;
        private System.Windows.Forms.TextBox txtNama;
        private System.Windows.Forms.Label lblProdi;
        private System.Windows.Forms.TextBox txtProdi;
        private System.Windows.Forms.Label lblIPK;
        private System.Windows.Forms.TextBox txtIPK;
        private System.Windows.Forms.Button btnTambah;
        private System.Windows.Forms.Label lblCari;
        private System.Windows.Forms.TextBox txtCariNIM;
        private System.Windows.Forms.Button btnCari;
        private System.Windows.Forms.Button btnHapus;
        private System.Windows.Forms.DataGridView dgvMahasiswa;

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
            System.Windows.Forms.DataGridViewCellStyle dgvHeaderStyle = new System.Windows.Forms.DataGridViewCellStyle();
            System.Windows.Forms.DataGridViewCellStyle dgvCellStyle = new System.Windows.Forms.DataGridViewCellStyle();

            this.panelHeader = new System.Windows.Forms.Panel();
            this.lblTitle = new System.Windows.Forms.Label();
            this.lblNIM = new System.Windows.Forms.Label();
            this.lblNama = new System.Windows.Forms.Label();
            this.lblProdi = new System.Windows.Forms.Label();
            this.lblIPK = new System.Windows.Forms.Label();
            this.lblCari = new System.Windows.Forms.Label();
            this.txtNIM = new System.Windows.Forms.TextBox();
            this.txtNama = new System.Windows.Forms.TextBox();
            this.txtProdi = new System.Windows.Forms.TextBox();
            this.txtIPK = new System.Windows.Forms.TextBox();
            this.txtCariNIM = new System.Windows.Forms.TextBox();
            this.btnTambah = new System.Windows.Forms.Button();
            this.btnCari = new System.Windows.Forms.Button();
            this.btnHapus = new System.Windows.Forms.Button();
            this.dgvMahasiswa = new System.Windows.Forms.DataGridView();

            this.panelHeader.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.dgvMahasiswa)).BeginInit();
            this.SuspendLayout();

            System.Drawing.Font fontLabel = new System.Drawing.Font("Segoe UI", 9.5F, System.Drawing.FontStyle.Bold);
            System.Drawing.Font fontInput = new System.Drawing.Font("Segoe UI", 9.5F);

            this.panelHeader.BackColor = System.Drawing.Color.FromArgb(30, 41, 59);
            this.panelHeader.Controls.Add(this.lblTitle);
            this.panelHeader.Dock = System.Windows.Forms.DockStyle.Top;
            this.panelHeader.Location = new System.Drawing.Point(0, 0);
            this.panelHeader.Size = new System.Drawing.Size(620, 50);

            this.lblTitle.AutoSize = true;
            this.lblTitle.Font = new System.Drawing.Font("Segoe UI", 14F, System.Drawing.FontStyle.Bold);
            this.lblTitle.ForeColor = System.Drawing.Color.White;
            this.lblTitle.Location = new System.Drawing.Point(15, 12);
            this.lblTitle.Text = "Sistem Data Mahasiswa";

            this.lblNIM.Text = "NIM";
            this.lblNIM.Font = fontLabel;
            this.lblNIM.Location = new System.Drawing.Point(20, 70);
            this.lblNIM.Size = new System.Drawing.Size(80, 20);

            this.txtNIM.Font = fontInput;
            this.txtNIM.Location = new System.Drawing.Point(100, 68);
            this.txtNIM.Size = new System.Drawing.Size(160, 25);

            this.lblNama.Text = "Nama";
            this.lblNama.Font = fontLabel;
            this.lblNama.Location = new System.Drawing.Point(20, 105);
            this.lblNama.Size = new System.Drawing.Size(80, 20);

            this.txtNama.Font = fontInput;
            this.txtNama.Location = new System.Drawing.Point(100, 103);
            this.txtNama.Size = new System.Drawing.Size(160, 25);

            this.lblProdi.Text = "Prodi";
            this.lblProdi.Font = fontLabel;
            this.lblProdi.Location = new System.Drawing.Point(20, 140);
            this.lblProdi.Size = new System.Drawing.Size(80, 20);

            this.txtProdi.Font = fontInput;
            this.txtProdi.Location = new System.Drawing.Point(100, 138);
            this.txtProdi.Size = new System.Drawing.Size(160, 25);

            this.lblIPK.Text = "IPK";
            this.lblIPK.Font = fontLabel;
            this.lblIPK.Location = new System.Drawing.Point(20, 175);
            this.lblIPK.Size = new System.Drawing.Size(80, 20);

            this.txtIPK.Font = fontInput;
            this.txtIPK.Location = new System.Drawing.Point(100, 173);
            this.txtIPK.Size = new System.Drawing.Size(160, 25);

            this.btnTambah.Text = "+ Tambah Data";
            this.btnTambah.Font = fontLabel;
            this.btnTambah.BackColor = System.Drawing.Color.FromArgb(16, 185, 129);
            this.btnTambah.ForeColor = System.Drawing.Color.White;
            this.btnTambah.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnTambah.FlatAppearance.BorderSize = 0;
            this.btnTambah.Location = new System.Drawing.Point(100, 210);
            this.btnTambah.Size = new System.Drawing.Size(160, 32);
            this.btnTambah.Click += new System.EventHandler(this.btnTambah_Click);

            this.lblCari.Text = "Cari/Hapus NIM:";
            this.lblCari.Font = fontLabel;
            this.lblCari.Location = new System.Drawing.Point(310, 70);
            this.lblCari.Size = new System.Drawing.Size(130, 20);

            this.txtCariNIM.Font = fontInput;
            this.txtCariNIM.Location = new System.Drawing.Point(310, 95);
            this.txtCariNIM.Size = new System.Drawing.Size(280, 25);

            this.btnCari.Text = "Cari Data";
            this.btnCari.Font = fontLabel;
            this.btnCari.BackColor = System.Drawing.Color.FromArgb(37, 99, 235);
            this.btnCari.ForeColor = System.Drawing.Color.White;
            this.btnCari.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnCari.FlatAppearance.BorderSize = 0;
            this.btnCari.Location = new System.Drawing.Point(310, 130);
            this.btnCari.Size = new System.Drawing.Size(135, 32);
            this.btnCari.Click += new System.EventHandler(this.btnCari_Click);

            this.btnHapus.Text = "Hapus Data";
            this.btnHapus.Font = fontLabel;
            this.btnHapus.BackColor = System.Drawing.Color.FromArgb(225, 29, 72);
            this.btnHapus.ForeColor = System.Drawing.Color.White;
            this.btnHapus.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnHapus.FlatAppearance.BorderSize = 0;
            this.btnHapus.Location = new System.Drawing.Point(455, 130);
            this.btnHapus.Size = new System.Drawing.Size(135, 32);
            this.btnHapus.Click += new System.EventHandler(this.btnHapus_Click);

            this.dgvMahasiswa.BackgroundColor = System.Drawing.Color.White;
            this.dgvMahasiswa.BorderStyle = System.Windows.Forms.BorderStyle.None;
            this.dgvMahasiswa.EnableHeadersVisualStyles = false;
            this.dgvMahasiswa.AutoSizeColumnsMode = System.Windows.Forms.DataGridViewAutoSizeColumnsMode.Fill;

            dgvHeaderStyle.BackColor = System.Drawing.Color.FromArgb(71, 85, 105);
            dgvHeaderStyle.ForeColor = System.Drawing.Color.White;
            dgvHeaderStyle.Font = new System.Drawing.Font("Segoe UI", 9.5F, System.Drawing.FontStyle.Bold);
            this.dgvMahasiswa.ColumnHeadersDefaultCellStyle = dgvHeaderStyle;
            this.dgvMahasiswa.ColumnHeadersHeight = 30;

            dgvCellStyle.Font = new System.Drawing.Font("Segoe UI", 9F);
            dgvCellStyle.SelectionBackColor = System.Drawing.Color.FromArgb(148, 163, 184);
            dgvCellStyle.SelectionForeColor = System.Drawing.Color.Black;
            this.dgvMahasiswa.DefaultCellStyle = dgvCellStyle;

            this.dgvMahasiswa.Location = new System.Drawing.Point(20, 260);
            this.dgvMahasiswa.Size = new System.Drawing.Size(570, 180);

            this.ClientSize = new System.Drawing.Size(610, 460);
            this.BackColor = System.Drawing.Color.FromArgb(241, 245, 249);
            this.Controls.Add(this.panelHeader);
            this.Controls.Add(this.lblNIM);
            this.Controls.Add(this.txtNIM);
            this.Controls.Add(this.lblNama);
            this.Controls.Add(this.txtNama);
            this.Controls.Add(this.lblProdi);
            this.Controls.Add(this.txtProdi);
            this.Controls.Add(this.lblIPK);
            this.Controls.Add(this.txtIPK);
            this.Controls.Add(this.btnTambah);
            this.Controls.Add(this.lblCari);
            this.Controls.Add(this.txtCariNIM);
            this.Controls.Add(this.btnCari);
            this.Controls.Add(this.btnHapus);
            this.Controls.Add(this.dgvMahasiswa);
            this.Text = "Sistem Data Mahasiswa (Modern UI)";

            this.panelHeader.ResumeLayout(false);
            this.panelHeader.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.dgvMahasiswa)).EndInit();
            this.ResumeLayout(false);
            this.PerformLayout();
        }
    }
}