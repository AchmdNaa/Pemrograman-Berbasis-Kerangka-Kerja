namespace StudentRegistrationSql.Data
{
    public static class DbConfig
    {
        /// <summary>
        /// Sesuaikan "Server" dengan nama instance SQL Server di laptop
        /// (cek di SSMS saat login, contoh: .\SQLEXPRESS atau (localdb)\MSSQLLocalDB).
        /// Integrated Security = login memakai akun Windows.
        /// TrustServerCertificate = perlu karena SQL Server Express memakai sertifikat self-signed.
        /// </summary>
        public const string ConnectionString =
            @"Server=.\SQLEXPRESS;Database=StudentRegistrationDB;Integrated Security=True;TrustServerCertificate=True;";
    }
}
