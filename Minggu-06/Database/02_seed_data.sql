/* =====================================================================
   Hands-On Lab 1 — Data awal
   ===================================================================== */
USE StudentRegistrationDB;
GO

INSERT INTO dbo.Programs (ProgramCode, ProgramName, Faculty) VALUES
    (N'IF',  N'Teknik Informatika',       N'Fakultas Teknologi Elektro dan Informatika Cerdas'),
    (N'SI',  N'Sistem Informasi',         N'Fakultas Teknologi Elektro dan Informatika Cerdas'),
    (N'TI',  N'Teknologi Informasi',      N'Fakultas Teknologi Elektro dan Informatika Cerdas'),
    (N'RPL', N'Rekayasa Perangkat Lunak', N'Fakultas Teknologi Elektro dan Informatika Cerdas'),
    (N'SD',  N'Sains Data',               N'Fakultas Sains dan Analitika Data');
GO

INSERT INTO dbo.Students (Nim, FullName, Gender, BirthDate, Email, Phone, Address, ProgramId) VALUES
    ('5025231265', N'Achmad Najwa Maruf',          'L', '2004-05-12', N'najwa@student.its.ac.id',   N'081234567801', N'Keputih, Sukolilo, Surabaya', 1),
    ('5025231288', N'Leyan Harits R W',            'L', '2004-08-03', N'leyan@student.its.ac.id',   N'081234567802', N'Gebang Lor, Surabaya',        1),
    ('5025241175', N'Derryell Josua Pandiangan',   'L', '2005-02-17', N'derryell@student.its.ac.id',N'081234567803', N'Mulyosari, Surabaya',         1),
    ('5026231010', N'Siti Rahmawati',              'P', '2005-01-20', N'siti@student.its.ac.id',    N'081234567804', N'Klampis Ngasem, Surabaya',    2),
    ('5026231022', N'Dewi Lestari',                'P', '2004-11-09', NULL,                         N'081234567805', N'Sidoarjo',                    2),
    ('5027241020', N'Budi Santoso',                'L', '2005-06-30', N'budi@student.its.ac.id',    NULL,            N'Gresik',                      3),
    ('5027241031', N'Rina Kartika',                'P', '2005-09-14', N'rina@student.its.ac.id',    N'081234567807', NULL,                           3),
    ('5028241005', N'Fajar Nugroho',               'L', '2005-03-21', N'fajar@student.its.ac.id',   N'081234567808', N'Rungkut, Surabaya',           4),
    ('5029241012', N'Nadia Putri',                 'P', '2005-12-01', N'nadia@student.its.ac.id',   N'081234567809', N'Wonokromo, Surabaya',         5),
    ('5029241019', N'Rizky Pratama',               'L', '2004-07-25', NULL,                         NULL,            N'Malang',                      5);
GO
