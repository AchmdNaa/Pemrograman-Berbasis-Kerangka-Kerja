using System;
using System.Collections.Generic;

namespace AksesKita.Desktop.Rules;

/// <summary>
/// Pembatas percobaan login: setelah beberapa kali gagal berturut-turut, akun dikunci sementara.
/// Waktu bisa disuntikkan supaya mudah diuji.
/// </summary>
public class LoginThrottle
{
    private readonly int _maxFailures;
    private readonly TimeSpan _lockDuration;
    private readonly Func<DateTime> _now;
    private readonly Dictionary<string, (int Failures, DateTime? LockedUntil)> _state = new();

    public LoginThrottle(int maxFailures = 5, TimeSpan? lockDuration = null, Func<DateTime>? now = null)
    {
        _maxFailures = maxFailures;
        _lockDuration = lockDuration ?? TimeSpan.FromSeconds(60);
        _now = now ?? (() => DateTime.Now);
    }

    /// <summary>True bila kunci masih berlaku; <paramref name="remaining"/> berisi sisa waktunya.</summary>
    public bool IsLocked(string key, out TimeSpan remaining)
    {
        remaining = TimeSpan.Zero;
        if (!_state.TryGetValue(key, out var s) || s.LockedUntil is not DateTime until) return false;

        var left = until - _now();
        if (left <= TimeSpan.Zero)
        {
            _state.Remove(key); // kunci sudah habis, mulai dari nol
            return false;
        }
        remaining = left;
        return true;
    }

    public void RecordFailure(string key)
    {
        _state.TryGetValue(key, out var s);
        var failures = s.Failures + 1;
        _state[key] = failures >= _maxFailures
            ? (0, _now() + _lockDuration)
            : (failures, null);
    }

    public void Reset(string key) => _state.Remove(key);
}
