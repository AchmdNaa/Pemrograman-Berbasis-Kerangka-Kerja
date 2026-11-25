using System.Collections.ObjectModel;
using System.Globalization;
using System.Text.RegularExpressions;
using StudentManagementMVVM.Commands;
using StudentManagementMVVM.Models;
using StudentManagementMVVM.Services;

namespace StudentManagementMVVM.ViewModels
{
    /// <summary>
    /// ViewModel utama. Menyimpan state form, daftar mahasiswa, statistik dashboard,
    /// dan seluruh perintah (command). Tidak ada satu pun referensi ke kontrol WPF di sini.
    /// </summary>
    public class MainViewModel : ViewModelBase
    {
        private static readonly Regex NimPattern = new(@"^\d{10}$");
        private static readonly Regex EmailPattern = new(@"^[^@\s]+@[^@\s]+\.[^@\s]+$");

        private readonly IStudentRepository _repository;
        private readonly IDialogService _dialog;
        private readonly List<Student> _allStudents;

        public MainViewModel(IStudentRepository repository, IDialogService dialog)
        {
            _repository = repository;
            _dialog = dialog;
            _allStudents = repository.GetAll().ToList();

            AddCommand = new RelayCommand(Add);
            UpdateCommand = new RelayCommand(Update, () => SelectedStudent is not null);
            DeleteCommand = new RelayCommand(Delete, () => SelectedStudent is not null);
            ClearCommand = new RelayCommand(ClearForm);
            SearchCommand = new RelayCommand(ApplyFilter);
            ResetSearchCommand = new RelayCommand(() => SearchText = string.Empty);

            ApplyFilter();
        }

        // ===================== Data untuk View =====================

        public ObservableCollection<Student> Students { get; } = new();

        public ObservableCollection<ProdiSummary> RingkasanProdi { get; } = new();

        public IReadOnlyList<string> DaftarProdi { get; } = new[]
        {
            "Teknik Informatika", "Sistem Informasi", "Teknologi Informasi",
            "Rekayasa Perangkat Lunak", "Sains Data"
        };

        private Student? _selectedStudent;
        public Student? SelectedStudent
        {
            get => _selectedStudent;
            set
            {
                if (!SetProperty(ref _selectedStudent, value)) return;
                if (value is not null) LoadForm(value);
                OnPropertyChanged(nameof(IsEditMode));
                OnPropertyChanged(nameof(FormTitle));
                UpdateCommand.RaiseCanExecuteChanged();
                DeleteCommand.RaiseCanExecuteChanged();
            }
        }

        public bool IsEditMode => SelectedStudent is not null;
        public string FormTitle => IsEditMode ? "Edit Mahasiswa" : "Tambah Mahasiswa";

        // ===================== Field form =====================

        private string _nim = string.Empty;
        public string Nim { get => _nim; set => SetProperty(ref _nim, value); }

        private string _nama = string.Empty;
        public string Nama { get => _nama; set => SetProperty(ref _nama, value); }

        private string? _programStudi;
        public string? ProgramStudi { get => _programStudi; set => SetProperty(ref _programStudi, value); }

        private string _angkatan = DateTime.Today.Year.ToString();
        public string Angkatan { get => _angkatan; set => SetProperty(ref _angkatan, value); }

        private string _ipk = string.Empty;
        public string Ipk { get => _ipk; set => SetProperty(ref _ipk, value); }

        private string _email = string.Empty;
        public string Email { get => _email; set => SetProperty(ref _email, value); }

        private string _errorMessage = string.Empty;
        public string ErrorMessage
        {
            get => _errorMessage;
            private set { SetProperty(ref _errorMessage, value); OnPropertyChanged(nameof(HasError)); }
        }
        public bool HasError => !string.IsNullOrEmpty(ErrorMessage);

        private string _statusMessage = "Siap.";
        public string StatusMessage { get => _statusMessage; private set => SetProperty(ref _statusMessage, value); }

        private string _searchText = string.Empty;
        public string SearchText
        {
            get => _searchText;
            set { if (SetProperty(ref _searchText, value)) ApplyFilter(); } // pencarian langsung saat mengetik
        }

        // ===================== Statistik dashboard =====================

        public int TotalMahasiswa => _allStudents.Count;
        public string RataRataIpk => _allStudents.Count == 0 ? "-" : _allStudents.Average(s => s.Ipk).ToString("0.00", CultureInfo.InvariantCulture);
        public string IpkTertinggi => _allStudents.Count == 0 ? "-" : _allStudents.Max(s => s.Ipk).ToString("0.00", CultureInfo.InvariantCulture);
        public string MahasiswaTerbaik => _allStudents.OrderByDescending(s => s.Ipk).FirstOrDefault()?.Nama ?? "-";
        public int JumlahCumlaude => _allStudents.Count(s => s.Ipk >= 3.51);
        public string HasilPencarian => $"Menampilkan {Students.Count} dari {_allStudents.Count} mahasiswa";

        // ===================== Commands =====================

        public RelayCommand AddCommand { get; }
        public RelayCommand UpdateCommand { get; }
        public RelayCommand DeleteCommand { get; }
        public RelayCommand ClearCommand { get; }
        public RelayCommand SearchCommand { get; }
        public RelayCommand ResetSearchCommand { get; }

        private void Add()
        {
            var student = ValidateForm(excluding: null);
            if (student is null) return;

            _allStudents.Add(student);
            Persist();
            ApplyFilter();
            ClearForm();
            StatusMessage = $"{student.Nama} berhasil ditambahkan.";
        }

        private void Update()
        {
            if (SelectedStudent is null) return;
            var updated = ValidateForm(excluding: SelectedStudent);
            if (updated is null) return;

            int index = _allStudents.IndexOf(SelectedStudent);
            _allStudents[index] = updated;
            Persist();
            ApplyFilter();
            ClearForm();
            StatusMessage = $"Data {updated.Nama} berhasil diperbarui.";
        }

        private void Delete()
        {
            if (SelectedStudent is null) return;
            var target = SelectedStudent;
            if (!_dialog.Confirm($"Hapus data {target.Nama} ({target.Nim})?", "Konfirmasi Hapus")) return;

            _allStudents.Remove(target);
            Persist();
            ApplyFilter();
            ClearForm();
            StatusMessage = $"Data {target.Nama} telah dihapus.";
        }

        private void ClearForm()
        {
            SelectedStudent = null;
            Nim = Nama = Ipk = Email = string.Empty;
            ProgramStudi = null;
            Angkatan = DateTime.Today.Year.ToString();
            ErrorMessage = string.Empty;
        }

        // ===================== Logika pendukung =====================

        private void LoadForm(Student s)
        {
            Nim = s.Nim;
            Nama = s.Nama;
            ProgramStudi = s.ProgramStudi;
            Angkatan = s.Angkatan.ToString();
            Ipk = s.Ipk.ToString("0.00", CultureInfo.InvariantCulture);
            Email = s.Email;
            ErrorMessage = string.Empty;
        }

        /// <summary>Validasi form. Mengembalikan Student baru bila valid, atau null bila tidak.</summary>
        private Student? ValidateForm(Student? excluding)
        {
            string nim = Nim.Trim();
            string nama = Nama.Trim();
            string email = Email.Trim();

            string? error = null;
            double ipk = 0;
            int angkatan = 0;

            if (!NimPattern.IsMatch(nim))
                error = "NIM harus 10 digit angka.";
            else if (_allStudents.Any(s => s != excluding && s.Nim == nim))
                error = $"NIM {nim} sudah terdaftar.";
            else if (nama.Length < 3)
                error = "Nama minimal 3 karakter.";
            else if (string.IsNullOrWhiteSpace(ProgramStudi))
                error = "Pilih program studi.";
            else if (!int.TryParse(Angkatan, out angkatan) || angkatan < 2000 || angkatan > DateTime.Today.Year)
                error = $"Angkatan harus tahun antara 2000 dan {DateTime.Today.Year}.";
            else if (!double.TryParse(Ipk.Replace(',', '.'), NumberStyles.Float, CultureInfo.InvariantCulture, out ipk) || ipk < 0 || ipk > 4)
                error = "IPK harus angka 0.00 – 4.00.";
            else if (email.Length > 0 && !EmailPattern.IsMatch(email))
                error = "Format email tidak valid.";

            ErrorMessage = error ?? string.Empty;
            if (error is not null) return null;

            return new Student
            {
                Nim = nim,
                Nama = nama,
                ProgramStudi = ProgramStudi!,
                Angkatan = angkatan,
                Ipk = Math.Round(ipk, 2),
                Email = email
            };
        }

        private void ApplyFilter()
        {
            string q = SearchText.Trim();
            var filtered = _allStudents
                .Where(s => q.Length == 0
                    || s.Nim.Contains(q, StringComparison.OrdinalIgnoreCase)
                    || s.Nama.Contains(q, StringComparison.OrdinalIgnoreCase)
                    || s.ProgramStudi.Contains(q, StringComparison.OrdinalIgnoreCase))
                .OrderBy(s => s.Nim);

            Students.Clear();
            foreach (var s in filtered) Students.Add(s);

            RefreshStatistics();
        }

        private void RefreshStatistics()
        {
            OnPropertyChanged(nameof(TotalMahasiswa));
            OnPropertyChanged(nameof(RataRataIpk));
            OnPropertyChanged(nameof(IpkTertinggi));
            OnPropertyChanged(nameof(MahasiswaTerbaik));
            OnPropertyChanged(nameof(JumlahCumlaude));
            OnPropertyChanged(nameof(HasilPencarian));

            RingkasanProdi.Clear();
            int max = _allStudents.Count == 0 ? 1 : _allStudents.GroupBy(s => s.ProgramStudi).Max(g => g.Count());
            foreach (var g in _allStudents.GroupBy(s => s.ProgramStudi).OrderByDescending(g => g.Count()))
                RingkasanProdi.Add(new ProdiSummary(g.Key, g.Count(), g.Average(s => s.Ipk), (double)g.Count() / max));
        }

        private void Persist()
        {
            try
            {
                _repository.SaveAll(_allStudents);
            }
            catch (Exception ex)
            {
                _dialog.Info($"Data gagal disimpan ke file: {ex.Message}", "Penyimpanan");
            }
        }
    }

    /// <summary>Satu baris ringkasan per program studi untuk panel dashboard.</summary>
    public record ProdiSummary(string Prodi, int Jumlah, double RataIpk, double Proporsi)
    {
        public string RataIpkText => RataIpk.ToString("0.00", CultureInfo.InvariantCulture);
        /// <summary>Lebar bar (maks 160 px) untuk grafik batang sederhana.</summary>
        public double BarWidth => Math.Max(8, Proporsi * 160);
    }
}
