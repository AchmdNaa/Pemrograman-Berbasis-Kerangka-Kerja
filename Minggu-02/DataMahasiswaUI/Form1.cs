using System;
using System.Collections.Generic;
using System.Linq;
using System.Windows.Forms;

namespace DataMahasiswaUI
{
    public partial class Form1 : Form
    {
        public class Mahasiswa
        {
            public string NIM { get; set; }
            public string Nama { get; set; }
            public string Prodi { get; set; }
            public double IPK { get; set; }

            public Mahasiswa(string nim, string nama, string prodi, double ipk)
            {
                NIM = nim;
                Nama = nama;
                Prodi = prodi;
                IPK = ipk;
            }
        }

        private List<Mahasiswa> daftarMahasiswa = new List<Mahasiswa>();

        public Form1()
        {
            InitializeComponent();
        }

        private void RefreshTabel()
        {
            dgvMahasiswa.DataSource = null;
            dgvMahasiswa.DataSource = daftarMahasiswa;
        }

        private void btnTambah_Click(object sender, EventArgs e)
        {
            string nim = txtNIM.Text.Trim();
            string nama = txtNama.Text.Trim();
            string prodi = txtProdi.Text.Trim();

            if (string.IsNullOrEmpty(nim) || string.IsNullOrEmpty(nama) || string.IsNullOrEmpty(prodi))
            {
                MessageBox.Show("Semua kolom input harus diisi!", "Peringatan", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            if (daftarMahasiswa.Any(m => m.NIM.Equals(nim, StringComparison.OrdinalIgnoreCase)))
            {
                MessageBox.Show("Mahasiswa dengan NIM ini sudah terdaftar!", "Duplikasi", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            if (double.TryParse(txtIPK.Text, out double ipk) && ipk >= 0 && ipk <= 4)
            {
                daftarMahasiswa.Add(new Mahasiswa(nim, nama, prodi, ipk));
                RefreshTabel();
                MessageBox.Show("Data mahasiswa berhasil ditambahkan!", "Sukses", MessageBoxButtons.OK, MessageBoxIcon.Information);

                txtNIM.Clear();
                txtNama.Clear();
                txtProdi.Clear();
                txtIPK.Clear();
            }
            else
            {
                MessageBox.Show("IPK harus berupa angka antara 0.0 sampai 4.0!", "Error IPK", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private void btnCari_Click(object sender, EventArgs e)
        {
            string nimCari = txtCariNIM.Text.Trim();
            var mhs = daftarMahasiswa.FirstOrDefault(x => x.NIM.Equals(nimCari, StringComparison.OrdinalIgnoreCase));

            if (mhs != null)
            {
                MessageBox.Show($"Data Ditemukan!\n\nNIM: {mhs.NIM}\nNama: {mhs.Nama}\nProdi: {mhs.Prodi}\nIPK: {mhs.IPK:F2}", 
                                "Hasil Pencarian", MessageBoxButtons.OK, MessageBoxIcon.Information);
            }
            else
            {
                MessageBox.Show("Mahasiswa dengan NIM tersebut tidak ditemukan.", "Info", MessageBoxButtons.OK, MessageBoxIcon.Warning);
            }
        }

        private void btnHapus_Click(object sender, EventArgs e)
        {
            string nimHapus = txtCariNIM.Text.Trim();
            var mhs = daftarMahasiswa.FirstOrDefault(x => x.NIM.Equals(nimHapus, StringComparison.OrdinalIgnoreCase));

            if (mhs != null)
            {
                daftarMahasiswa.Remove(mhs);
                RefreshTabel();
                MessageBox.Show("Data mahasiswa berhasil dihapus!", "Sukses", MessageBoxButtons.OK, MessageBoxIcon.Information);
                txtCariNIM.Clear();
            }
            else
            {
                MessageBox.Show("Data tidak ditemukan!", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }
    }
}