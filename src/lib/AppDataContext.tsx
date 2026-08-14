import { createContext, useCallback, useContext, useEffect, useState, type ReactNode } from 'react';
import { loadDataset, type DataSource } from '../data/store';
import { login as apiLogin, clearToken, ApiError } from './api';

type Status = 'loading' | 'login-required' | 'ready';

interface AppDataContextValue {
  status: Status;
  source: DataSource;
  loginError: string | null;
  isLoggingIn: boolean;
  login: (password: string) => Promise<void>;
  logout: () => void;
  refresh: () => Promise<void>;
}

const AppDataContext = createContext<AppDataContextValue | null>(null);

export function AppDataProvider({ children }: { children: ReactNode }) {
  const [status, setStatus] = useState<Status>('loading');
  const [source, setSource] = useState<DataSource>('standalone');
  const [loginError, setLoginError] = useState<string | null>(null);
  const [isLoggingIn, setIsLoggingIn] = useState(false);

  const refresh = useCallback(async () => {
    setStatus('loading');
    const result = await loadDataset();
    if (result.status === 'login-required') {
      setStatus('login-required');
    } else {
      setSource(result.source);
      setStatus('ready');
    }
  }, []);

  useEffect(() => {
    void refresh();
  }, [refresh]);

  const login = useCallback(
    async (password: string) => {
      setIsLoggingIn(true);
      setLoginError(null);
      try {
        await apiLogin(password);
        await refresh();
      } catch (err) {
        setLoginError(err instanceof ApiError ? err.message : 'Could not sign in — please try again.');
      } finally {
        setIsLoggingIn(false);
      }
    },
    [refresh],
  );

  const logout = useCallback(() => {
    clearToken();
    setStatus('login-required');
  }, []);

  return (
    <AppDataContext.Provider value={{ status, source, loginError, isLoggingIn, login, logout, refresh }}>
      {children}
    </AppDataContext.Provider>
  );
}

export function useAppData(): AppDataContextValue {
  const ctx = useContext(AppDataContext);
  if (!ctx) throw new Error('useAppData must be used within AppDataProvider');
  return ctx;
}
