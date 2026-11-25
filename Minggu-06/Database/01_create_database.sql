/* =====================================================================
   PBKK Minggu 6 — Hands-On Lab 1: Membangun Database Student Registration
   Nama : Achmad Najwa (5025231265) — PBKK D
   Jalankan di SQL Server Management Studio (SSMS) terhadap instance
   .\SQLEXPRESS. Script aman dijalankan ulang (database lama dihapus dulu).
   ===================================================================== */

USE master;
GO

IF DB_ID(N'StudentRegistrationDB') IS NOT NULL
BEGIN
    ALTER DATABASE StudentRegistrationDB SET SINGLE_USER WITH ROLLBACK IMMEDIATE;
    DROP DATABASE StudentRegistrationDB;
END
GO

CREATE DATABASE StudentRegistrationDB;
GO

USE StudentRegistrationDB;
GO

/* ---------- Tabel Program Studi ---------- */
CREATE TABLE dbo.Programs (
    ProgramId    INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_Programs PRIMARY KEY,
    ProgramCode  NVARCHAR(10)  NOT NULL CONSTRAINT UQ_Programs_Code UNIQUE,
    ProgramName  NVARCHAR(100) NOT NULL,
    Faculty      NVARCHAR(100) NOT NULL
);
GO

/* ---------- Tabel Mahasiswa ---------- */
CREATE TABLE dbo.Students (
    StudentId  INT IDENTITY(1,1) NOT NULL CONSTRAINT PK_Students PRIMARY KEY,
    Nim        CHAR(10)      NOT NULL CONSTRAINT UQ_Students_Nim UNIQUE,
    FullName   NVARCHAR(100) NOT NULL,
    Gender     CHAR(1)       NOT NULL CONSTRAINT CK_Students_Gender CHECK (Gender IN ('L', 'P')),
    BirthDate  DATE          NOT NULL,
    Email      NVARCHAR(100) NULL,
    Phone      NVARCHAR(20)  NULL,
    Address    NVARCHAR(255) NULL,
    ProgramId  INT           NOT NULL
        CONSTRAINT FK_Students_Programs REFERENCES dbo.Programs(ProgramId),
    CreatedAt  DATETIME2(0)  NOT NULL CONSTRAINT DF_Students_CreatedAt DEFAULT SYSDATETIME(),
    CONSTRAINT CK_Students_Nim CHECK (Nim NOT LIKE '%[^0-9]%')
);
GO

CREATE INDEX IX_Students_ProgramId ON dbo.Students(ProgramId);
CREATE INDEX IX_Students_FullName  ON dbo.Students(FullName);
GO
