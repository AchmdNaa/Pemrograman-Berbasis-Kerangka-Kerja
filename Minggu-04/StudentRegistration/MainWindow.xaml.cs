using System.Collections.ObjectModel;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Input;
using StudentRegistration.Models;

namespace StudentRegistration
{
    public partial class MainWindow : Window
    {
        // ObservableCollection otomatis memberi tahu ListBox setiap ada data ditambah/dihapus.
        private readonly ObservableCollection<Student> _students = new();

        // Data yang sedang dipilih untuk diedit (null = mode tambah data baru).
        private Student? _selected;

        public MainWindow()
        {
            InitializeComponent();
            lstMahasiswa.ItemsSource = _students;
            SeedData();
            RefreshJumlah();
        }

        private void SeedData()
        {
            _students.Add(new Student
            {
                Nim = "5025231265", Nama = "Achmad Najwa", ProgramStudi = "Teknik Informatika",
                JenisKelamin = "Laki-laki", TanggalLahir = new DateTime(2004, 5, 12),
                Email = "najwa@student.its.ac.id", Alamat = "Keputih, Sukolilo, Surabaya"
            });
            _students.Add(new Student
            {
                Nim = "5026231001", Nama = "Siti Rahmawati", ProgramStudi = "Sistem Informasi",
                JenisKelamin = "Perempuan", TanggalLahir = new DateTime(2005, 1, 20),
                Email = "siti@student.its.ac.id", Alamat = "Gebang Putih, Surabaya"
            });
        }

        // ===================== Tombol =====================

        private void BtnSimpan_Click(object sender, RoutedEventArgs e)
        {
            if (!TryReadForm(out Student? student)) return;

            if (_students.Any(s => s.Nim == student!.Nim))
            {
                ShowWarning($"NIM {student!.Nim} sudah terdaftar.");
                return;
            }

            _students.Add(student!);
            SetStatus($"Data {student!.Nama} berhasil disimpan.");
            MessageBox.Show("Data mahasiswa berhasil disimpan!", "Sukses",
                MessageBoxButton.OK, MessageBoxImage.Information);
            ResetForm();
        }

        private void BtnUpdate_Click(object sender, RoutedEventArgs e)
        {
            if (_selected is null) return;
            if (!TryReadForm(out Student? updated)) return;

            // NIM boleh diubah asal tidak bentrok dengan mahasiswa lain.
            if (_students.Any(s => s != _selected && s.Nim == updated!.Nim))
            {
                ShowWarning($"NIM {updated!.Nim} sudah dipakai mahasiswa lain.");
                return;
            }

            int index = _students.IndexOf(_selected);
            _students[index] = updated!; // mengganti item agar ListBox ikut diperbarui
            SetStatus($"Data {updated!.Nama} berhasil diperbarui.");
            MessageBox.Show("Data mahasiswa berhasil diperbarui!", "Sukses",
                MessageBoxButton.OK, MessageBoxImage.Information);
            ResetForm();
        }

        private void BtnHapus_Click(object sender, RoutedEventArgs e)
        {
            if (_selected is null) return;

            var jawab = MessageBox.Show($"Hapus data {_selected.Nama} ({_selected.Nim})?",
                "Konfirmasi Hapus", MessageBoxButton.YesNo, MessageBoxImage.Question);
            if (jawab != MessageBoxResult.Yes) return;

            string nama = _selected.Nama;
            _students.Remove(_selected);
            SetStatus($"Data {nama} telah dihapus.");
            ResetForm();
        }

        private void BtnReset_Click(object sender, RoutedEventArgs e)
        {
            ResetForm();
            SetStatus("Form dikosongkan.");
        }

        private void BtnCari_Click(object sender, RoutedEventArgs e) => Cari();

        private void TxtCari_KeyDown(object sender, KeyEventArgs e)
        {
            if (e.Key == Key.Enter) Cari();
        }

        private void BtnTampilSemua_Click(object sender, RoutedEventArgs e)
        {
            txtCari.Clear();
            lstMahasiswa.ItemsSource = _students;
            RefreshJumlah();
            SetStatus("Menampilkan semua data.");
        }

        private void LstMahasiswa_SelectionChanged(object sender, SelectionChangedEventArgs e)
        {
            if (lstMahasiswa.SelectedItem is not Student s) return;

            _selected = s;
            txtNim.Text = s.Nim;
            txtNama.Text = s.Nama;
            cmbProdi.SelectedItem = cmbProdi.Items.Cast<ComboBoxItem>()
                .FirstOrDefault(i => (string)i.Content == s.ProgramStudi);
            rbLaki.IsChecked = s.JenisKelamin == "Laki-laki";
            rbPerempuan.IsChecked = s.JenisKelamin == "Perempuan";
            dpTanggalLahir.SelectedDate = s.TanggalLahir;
            txtEmail.Text = s.Email;
            txtAlamat.Text = s.Alamat;
            chkSetuju.IsChecked = true;

            btnSimpan.IsEnabled = false;
            btnUpdate.IsEnabled = true;
            btnHapus.IsEnabled = true;
            SetStatus($"Mode edit: {s.Nama}");
        }

        // ===================== Helper =====================

        private void Cari()
        {
            string kata = txtCari.Text.Trim();
            if (kata.Length == 0)
            {
                lstMahasiswa.ItemsSource = _students;
                RefreshJumlah();
                return;
            }

            var hasil = _students.Where(s =>
                    s.Nim.Contains(kata, StringComparison.OrdinalIgnoreCase) ||
                    s.Nama.Contains(kata, StringComparison.OrdinalIgnoreCase) ||
                    s.ProgramStudi.Contains(kata, StringComparison.OrdinalIgnoreCase))
                .ToList();

            lstMahasiswa.ItemsSource = hasil;
            txtJumlah.Text = $"{hasil.Count} dari {_students.Count} mahasiswa";
            SetStatus(hasil.Count == 0
                ? $"Tidak ada data yang cocok dengan \"{kata}\"."
                : $"Ditemukan {hasil.Count} data untuk \"{kata}\".");
        }

        private bool TryReadForm(out Student? student)
        {
            student = null;
            string? prodi = (cmbProdi.SelectedItem as ComboBoxItem)?.Content as string;
            string? gender = rbLaki.IsChecked == true ? "Laki-laki"
                           : rbPerempuan.IsChecked == true ? "Perempuan" : null;

            string? error = StudentValidator.Validate(txtNim.Text, txtNama.Text, prodi, gender,
                dpTanggalLahir.SelectedDate, txtEmail.Text, txtAlamat.Text);

            if (error is null && chkSetuju.IsChecked != true)
                error = "Centang pernyataan bahwa data sudah benar.";

            if (error is not null)
            {
                ShowWarning(error);
                return false;
            }

            student = new Student
            {
                Nim = txtNim.Text.Trim(),
                Nama = txtNama.Text.Trim(),
                ProgramStudi = prodi!,
                JenisKelamin = gender!,
                TanggalLahir = dpTanggalLahir.SelectedDate!.Value.Date,
                Email = txtEmail.Text.Trim(),
                Alamat = txtAlamat.Text.Trim()
            };
            return true;
        }

        private void ResetForm()
        {
            _selected = null;
            txtNim.Clear();
            txtNama.Clear();
            cmbProdi.SelectedIndex = -1;
            rbLaki.IsChecked = false;
            rbPerempuan.IsChecked = false;
            dpTanggalLahir.SelectedDate = null;
            txtEmail.Clear();
            txtAlamat.Clear();
            chkSetuju.IsChecked = false;
            lstMahasiswa.SelectedItem = null;

            // Kembali ke daftar lengkap agar hasil pencarian lama tidak basi setelah update/hapus.
            if (!ReferenceEquals(lstMahasiswa.ItemsSource, _students))
            {
                txtCari.Clear();
                lstMahasiswa.ItemsSource = _students;
            }

            btnSimpan.IsEnabled = true;
            btnUpdate.IsEnabled = false;
            btnHapus.IsEnabled = false;
            RefreshJumlah();
            txtNim.Focus();
        }

        private void RefreshJumlah()
        {
            if (ReferenceEquals(lstMahasiswa.ItemsSource, _students))
                txtJumlah.Text = $"{_students.Count} mahasiswa";
        }

        private void SetStatus(string pesan) => txtStatus.Text = $"[{DateTime.Now:HH:mm:ss}] {pesan}";

        private void ShowWarning(string pesan)
        {
            MessageBox.Show(pesan, "Validasi", MessageBoxButton.OK, MessageBoxImage.Warning);
            SetStatus(pesan);
        }
    }
}
