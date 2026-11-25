/* =====================================================================
   Hands-On Lab 1 — Query latihan, VIEW, dan STORED PROCEDURE
   ===================================================================== */
USE StudentRegistrationDB;
GO

-- 1. Semua mahasiswa beserta nama program studinya (JOIN)
SELECT s.StudentId, s.Nim, s.FullName, p.ProgramName, s.Gender, s.BirthDate
FROM dbo.Students AS s
INNER JOIN dbo.Programs AS p ON p.ProgramId = s.ProgramId
ORDER BY s.Nim;

-- 2. Jumlah mahasiswa per program studi (termasuk prodi tanpa mahasiswa)
SELECT p.ProgramCode, p.ProgramName, COUNT(s.StudentId) AS JumlahMahasiswa
FROM dbo.Programs AS p
LEFT JOIN dbo.Students AS s ON s.ProgramId = p.ProgramId
GROUP BY p.ProgramCode, p.ProgramName
ORDER BY JumlahMahasiswa DESC;

-- 3. Mahasiswa yang belum melengkapi email atau nomor telepon
SELECT Nim, FullName, Email, Phone
FROM dbo.Students
WHERE Email IS NULL OR Phone IS NULL;

-- 4. Usia setiap mahasiswa
SELECT Nim, FullName,
       DATEDIFF(YEAR, BirthDate, GETDATE())
         - CASE WHEN DATEADD(YEAR, DATEDIFF(YEAR, BirthDate, GETDATE()), BirthDate) > GETDATE() THEN 1 ELSE 0 END AS Usia
FROM dbo.Students;

-- 5. Pencarian nama (LIKE)
SELECT Nim, FullName FROM dbo.Students WHERE FullName LIKE N'%an%';

-- 6. Contoh UPDATE dan DELETE di dalam transaksi (di-ROLLBACK agar data contoh tetap utuh)
BEGIN TRANSACTION;
    UPDATE dbo.Students SET Phone = N'089999999999' WHERE Nim = '5029241019';
    DELETE FROM dbo.Students WHERE Nim = '5027241031';
    SELECT COUNT(*) AS JumlahSetelahDelete FROM dbo.Students;
ROLLBACK TRANSACTION;
GO

-- 7. VIEW gabungan untuk dipakai aplikasi
CREATE OR ALTER VIEW dbo.vw_StudentDetails AS
SELECT s.StudentId, s.Nim, s.FullName, s.Gender, s.BirthDate, s.Email, s.Phone, s.Address,
       s.CreatedAt, p.ProgramId, p.ProgramCode, p.ProgramName
FROM dbo.Students AS s
INNER JOIN dbo.Programs AS p ON p.ProgramId = s.ProgramId;
GO

-- 8. STORED PROCEDURE: daftar mahasiswa per kode prodi
CREATE OR ALTER PROCEDURE dbo.sp_GetStudentsByProgram
    @ProgramCode NVARCHAR(10)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT Nim, FullName, ProgramName
    FROM dbo.vw_StudentDetails
    WHERE ProgramCode = @ProgramCode
    ORDER BY FullName;
END
GO

SELECT * FROM dbo.vw_StudentDetails ORDER BY Nim;
EXEC dbo.sp_GetStudentsByProgram @ProgramCode = N'IF';
GO
