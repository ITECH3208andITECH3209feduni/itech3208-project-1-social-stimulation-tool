// useTokenExpiryCheck.js
import { jwtDecode } from "jwt-decode";
import { useEffect } from "react";

const useTokenExpiryCheck = ({ tokenValue, onExpired }) => {
    useEffect(() => {
        if (!tokenValue) return;

        try {
            const { exp } = jwtDecode(tokenValue);
            const now = Date.now() / 1000;

            if (exp <= now) {
                onExpired?.();
                return;
            }

            const timeout = (exp - now) * 1000;
            const timer = setTimeout(() => {
                onExpired?.();
            }, timeout);

            return () => clearTimeout(timer);
        } catch {
            onExpired?.();
        }
    }, [tokenValue, onExpired]);
};

export default useTokenExpiryCheck;
