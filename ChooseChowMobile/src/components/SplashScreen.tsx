import React, { useEffect, useState } from 'react';
import { getToken } from '../api/client';
import { FoodieSplashScreen } from './FoodieSplashScreen';
import { VendorSplashScreen } from './VendorSplashScreen';

interface SplashScreenProps {
  onFinish: () => void;
}

function extractRoleFromToken(token: string): string | null {
  try {
    const parts = token.split('.');
    if (parts.length !== 3) return null;
    const base64 = parts[1].replace(/-/g, '+').replace(/_/g, '/');
    let decoded: string;
    try { decoded = atob(base64); } catch {
      const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
      let str = '';
      for (let i = 0; i < base64.length; i += 4) {
        const a = chars.indexOf(base64[i]);
        const b = chars.indexOf(base64[i + 1]);
        const c = chars.indexOf(base64[i + 2]);
        const d = chars.indexOf(base64[i + 3]);
        str += String.fromCharCode((a << 2) | (b >> 4));
        if (c !== -1) str += String.fromCharCode(((b & 15) << 4) | (c >> 2));
        if (d !== -1) str += String.fromCharCode(((c & 3) << 6) | d);
      }
      decoded = str;
    }
    const payload = JSON.parse(decoded);
    return payload?.user_type || payload?.role || null;
  } catch {
    return null;
  }
}

export const SplashScreen: React.FC<SplashScreenProps> = ({ onFinish }) => {
  const [variant, setVariant] = useState<'foodie' | 'vendor' | null>(null);

  useEffect(() => {
    (async () => {
      try {
        const token = await getToken();
        if (token) {
          const role = extractRoleFromToken(token);
          if (role === 'chef') {
            setVariant('vendor');
            return;
          }
        }
      } catch {
        // ignore
      }
      setVariant('foodie');
    })();
  }, []);

  if (!variant) return null;

  return variant === 'vendor'
    ? <VendorSplashScreen onFinish={onFinish} />
    : <FoodieSplashScreen onFinish={onFinish} />;
};

export default SplashScreen;
