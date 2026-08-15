import { useState } from 'react';
import { useAppData } from '../lib/AppDataContext';

export function LoginPage() {
  const { login, loginError, isLoggingIn } = useAppData();
  const [password, setPassword] = useState('');

  return (
    <div className="flex h-screen w-screen items-center justify-center bg-[var(--surface-2)]">
      <form
        onSubmit={(e) => {
          e.preventDefault();
          void login(password);
        }}
        className="flex w-full max-w-sm flex-col gap-4 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-6"
      >
        <div>
          <div className="text-[15px] font-semibold text-[var(--text-primary)]">People Analytics</div>
          <div className="text-[12px] text-[var(--text-muted)] mt-0.5">Sign in to view the HR &amp; workforce dashboard</div>
        </div>
        <label className="flex flex-col gap-1">
          <span className="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Password</span>
          <input
            type="password"
            autoFocus
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            className="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-3 py-2 text-[13px] text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--series-1)]"
          />
        </label>
        {loginError && <div className="text-[12px] text-[var(--status-critical)]">{loginError}</div>}
        <button
          type="submit"
          disabled={isLoggingIn || !password}
          className="rounded-md bg-[var(--series-1)] px-3 py-2 text-[13px] font-medium text-white disabled:opacity-50"
        >
          {isLoggingIn ? 'Signing in…' : 'Sign in'}
        </button>
      </form>
    </div>
  );
}
