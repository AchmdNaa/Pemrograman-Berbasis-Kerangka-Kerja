using System;
using System.Collections.Generic;

namespace DataMahasiswa
{
    class Mahasiswa
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

    class Program
    {
        static List<Mahasiswa> daftarMahasiswa = new List<Mahasiswa>();

        static void Main(string[] args)
        {
            int pilihan;
            do
            {
                TampilkanMenu();
                Console.Write("Pilihan: ");
                
                string input = Console.ReadLine() ?? string.Empty;
                if (!int.TryParse(input, out pilihan))
                {
                    pilihan = 0;
                }
                
                Console.WriteLine();
                
                switch (pilihan)
                {
                    case 1:
                        TambahMahasiswa();
                        break;
                    case 2:
                        TampilkanMahasiswa();
                        break;
                    case 3:
                        CariMahasiswa();
                        break;
                    case 4:
                        HapusMahasiswa();
                        break;
                    case 5:
                        Console.WriteLine("Terima kasih telah menggunakan program.");
                        break;
                    default:
                        Console.WriteLine("Pilihan tidak tersedia!");
                        break;
                }
                
                if (pilihan != 5)
                {
                    Console.WriteLine("\nTekan ENTER untuk melanjutkan...");
                    Console.ReadLine();
                }
            } while (pilihan != 5);
        }

        static void TampilkanMenu()
        {
            Console.Clear();
            Console.WriteLine("========================================");
            Console.WriteLine(" SISTEM DATA MAHASISWA");
            Console.WriteLine("========================================");
            Console.WriteLine("1. Tambah Mahasiswa");
            Console.WriteLine("2. Tampilkan Mahasiswa");
            Console.WriteLine("3. Cari Mahasiswa");
            Console.WriteLine("4. Hapus Mahasiswa");
            Console.WriteLine("5. Keluar");
            Console.WriteLine("========================================");
        }

        static void TambahMahasiswa()
        {
            Console.Clear();
            Console.WriteLine("========================================");
            Console.WriteLine(" TAMBAH MAHASISWA");
            Console.WriteLine("========================================");
            
            Console.Write("NIM : ");
            string nim = Console.ReadLine() ?? string.Empty;
            
            Console.Write("Nama : ");
            string nama = Console.ReadLine() ?? string.Empty;
            
            Console.Write("Program Studi: ");
            string prodi = Console.ReadLine() ?? string.Empty;
            
            double ipk;
            while (true)
            {
                Console.Write("IPK: ");
                if (double.TryParse(Console.ReadLine(), out ipk) && ipk >= 0 && ipk <= 4)
                {
                    break;
                }
                Console.WriteLine("IPK harus berupa angka 0-4.");
            }
            
            daftarMahasiswa.Add(new Mahasiswa(nim, nama, prodi, ipk));
            Console.WriteLine("\nData mahasiswa berhasil ditambahkan.");
        }

        static void TampilkanMahasiswa()
        {
            Console.Clear();
            Console.WriteLine("========================================");
            Console.WriteLine(" DAFTAR MAHASISWA");
            Console.WriteLine("========================================");
            
            if (daftarMahasiswa.Count == 0)
            {
                Console.WriteLine("Belum ada data mahasiswa.");
                return;
            }
            
            Console.WriteLine("{0,-12} {1,-20} {2,-20} {3,5}", "NIM", "Nama", "Prodi", "IPK");
            Console.WriteLine("------------------------------------------------------------");
            
            foreach (Mahasiswa m in daftarMahasiswa)
            {
                Console.WriteLine("{0,-12} {1,-20} {2,-20} {3,5:F2}", m.NIM, m.Nama, m.Prodi, m.IPK);
            }
        }

        static void CariMahasiswa()
        {
            Console.Clear();
            Console.WriteLine("========================================");
            Console.WriteLine(" CARI MAHASISWA");
            Console.WriteLine("========================================");
            
            Console.Write("Masukkan NIM: ");
            string nimCari = Console.ReadLine() ?? string.Empty;
            
            Mahasiswa? m = daftarMahasiswa.Find(x => x.NIM.Equals(nimCari, StringComparison.OrdinalIgnoreCase));
            
            Console.WriteLine();
            if (m != null)
            {
                Console.WriteLine("Data ditemukan!");
                Console.WriteLine($"NIM: {m.NIM}\nNama: {m.Nama}\nProdi: {m.Prodi}\nIPK: {m.IPK:F2}");
            }
            else
            {
                Console.WriteLine("Mahasiswa dengan NIM tersebut tidak ditemukan.");
            }
        }

        static void HapusMahasiswa()
        {
            Console.Clear();
            Console.WriteLine("========================================");
            Console.WriteLine(" HAPUS MAHASISWA");
            Console.WriteLine("========================================");
            
            Console.Write("Masukkan NIM: ");
            string nimHapus = Console.ReadLine() ?? string.Empty;
            
            Mahasiswa? m = daftarMahasiswa.Find(x => x.NIM.Equals(nimHapus, StringComparison.OrdinalIgnoreCase));
            
            Console.WriteLine();
            if (m != null)
            {
                daftarMahasiswa.Remove(m);
                Console.WriteLine("Data mahasiswa berhasil dihapus.");
            }
            else
            {
                Console.WriteLine("Data mahasiswa tidak ditemukan.");
            }
        }
    }
}