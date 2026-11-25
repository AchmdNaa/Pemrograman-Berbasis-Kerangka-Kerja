using System.Text.RegularExpressions;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Input;
using Microsoft.Data.SqlClient;
using StudentRegistrationSql.Data;
using StudentRegistrationSql.Models;

namespace StudentRegistrationSql
{
    public partial class MainWindow : Window
    {
        private readonly StudentRepository _repository = new();
        private bool _loading;

        public MainWindow()
        {
            InitializeComponent();
        }

        private void Window_Loaded(object sender, RoutedEventArgs e)
        {
            RunDb(() =>
            {
                var programs = _repository.GetPrograms();
                cmbProgram.ItemsSource = programs;

                // Filter memakai item tambahan "Semua Program Studi" (ProgramId = 0)
                _loading = true;
                var filter = new List<StudyProgram> { new() { ProgramId = 0, ProgramName = "Semua Program Studi" } };
                filter.AddRange(programs);
                cmbFilterProgram.ItemsSource = filter;
                cmbFilterProgram.SelectedIndex = 0;
                _loading = false;

                LoadStudents();
            });
        }

        // ===================== Membaca data =====================

        private void LoadStudents()
        {
            int? programId = (cmbFilterProgram.SelectedItem as StudyProgram)?.ProgramId;
            if (programId == 0) programId = null;

            List<Student> students = _repository.Search(txtSearch.Text, programId);
            int total = _repository.GetCount();

            dgStudents.ItemsSource = students;
            txtTotal.Text = total.ToString();
            txtShown.Text = $"Menampilkan {students.Count} dari {total} mahasiswa";
            txtStatus.Text = $"[{DateTime.Now:HH:mm:ss}] Data dimuat dari SQL Server.";
        }

        private void BtnRefresh_Click(object sender, RoutedEventArgs e)
        {
            txtSearch.Clear();
            _loading = true;
            cmbFilterProgram.SelectedIndex = 0;
            _loading = false;
            RunDb(LoadStudents);
        }

        private void BtnCari_Click(object sender, RoutedEventArgs e) => RunDb(LoadStudents);

        private void TxtSearch_KeyDown(object sender, KeyEventArgs e)
        {
            if (e.Key == Key.Enter) RunDb(LoadStudents);
        }

        private void CmbFilterProgram_SelectionChanged(object sender, SelectionChangedEventArgs e)
        {
            if (!_loading && IsLoaded) RunDb(LoadStudents);
        }

        private void DgStudents_SelectionChanged(object sender, SelectionChangedEventArgs e)
        {
            btnHapus.IsEnabled = dgStudents.SelectedItem is Student;
        }

        // ===================== Menulis data =====================

        private void BtnSimpan_Click(object sender, RoutedEventArgs e)
        {
            string nim = txtNim.Text.Trim();
            string nama = txtNama.Text.Trim();
            string? gender = rbL.IsChecked == true ? "L" : rbP.IsChecked == true ? "P" : null;
            string email = txtEmail.Text.Trim();

            string? error =
                !Regex.IsMatch(nim, @"^\d{10}$") ? "NIM harus 10 digit angka." :
                nama.Length < 3 ? "Nama minimal 3 karakter." :
                cmbProgram.SelectedItem is not StudyProgram ? "Pilih program studi." :
                gender is null ? "Pilih jenis kelamin." :
                dpBirth.SelectedDate is null ? "Tanggal lahir harus diisi." :
                dpBirth.SelectedDate.Value > DateTime.Today.AddYears(-15) ? "Usia minimal 15 tahun." :
                email.Length > 0 && !Regex.IsMatch(email, @"^[^@\s]+@[^@\s]+\.[^@\s]+$") ? "Format email tidak valid." :
                null;

            if (error is not null)
            {
                MessageBox.Show(error, "Validasi", MessageBoxButton.OK, MessageBoxImage.Warning);
                return;
            }

            RunDb(() =>
            {
                if (_repository.NimExists(nim))
                {
                    MessageBox.Show($"NIM {nim} sudah terdaftar.", "Validasi", MessageBoxButton.OK, MessageBoxImage.Warning);
                    return;
                }

                int newId = _repository.Insert(new Student
                {
                    Nim = nim,
                    FullName = nama,
                    Gender = gender!,
                    BirthDate = dpBirth.SelectedDate!.Value,
                    Email = email,
                    Phone = txtPhone.Text,
                    Address = txtAddress.Text,
                    ProgramId = ((StudyProgram)cmbProgram.SelectedItem).ProgramId
                });

                ClearForm();
                LoadStudents();
                txtStatus.Text = $"[{DateTime.Now:HH:mm:ss}] Mahasiswa {nama} tersimpan dengan ID {newId}.";
            });
        }

        private void BtnHapus_Click(object sender, RoutedEventArgs e)
        {
            if (dgStudents.SelectedItem is not Student s) return;

            var confirm = MessageBox.Show($"Hapus {s.FullName} ({s.Nim}) dari database?", "Konfirmasi Hapus",
                MessageBoxButton.YesNo, MessageBoxImage.Question);
            if (confirm != MessageBoxResult.Yes) return;

            RunDb(() =>
            {
                _repository.Delete(s.StudentId);
                LoadStudents();
                txtStatus.Text = $"[{DateTime.Now:HH:mm:ss}] Data {s.FullName} dihapus.";
            });
        }

        private void BtnBersihkan_Click(object sender, RoutedEventArgs e) => ClearForm();

        private void ClearForm()
        {
            txtNim.Clear();
            txtNama.Clear();
            cmbProgram.SelectedIndex = -1;
            rbL.IsChecked = rbP.IsChecked = false;
            dpBirth.SelectedDate = null;
            txtEmail.Clear();
            txtPhone.Clear();
            txtAddress.Clear();
        }

        /// <summary>
        /// Menjalankan operasi database dan menampilkan error koneksi/query dengan rapi,
        /// misalnya bila SQL Server belum berjalan atau nama instance salah.
        /// </summary>
        private void RunDb(Action action)
        {
            try
            {
                Mouse.OverrideCursor = Cursors.Wait;
                action();
            }
            catch (SqlException ex)
            {
                txtStatus.Text = "Gagal mengakses database.";
                MessageBox.Show(
                    $"Tidak dapat mengakses SQL Server.\n\n{ex.Message}\n\n" +
                    "Pastikan service SQL Server (SQLEXPRESS) berjalan, script di folder Database sudah dijalankan, " +
                    "dan connection string di Data/DbConfig.cs sudah sesuai.",
                    "Database Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
            finally
            {
                Mouse.OverrideCursor = null;
            }
        }
    }
}
