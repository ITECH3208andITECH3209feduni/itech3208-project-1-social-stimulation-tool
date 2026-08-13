import { useEffect } from "react";
import { jwtDecode } from "jwt-decode";
import loggerUtil from "@/utils/logger.utils";

const useTokenExpiryCheck = (token, callback) => {
    useEffect(() => {
        try {
            if (!token) return;
            const decoded = jwtDecode(token);
            const currentTime = Date.now() / 1000;

            if (decoded.exp < currentTime) {
                callback();
            } else {
                const timeout = (decoded.exp - currentTime) * 1000;
                const timer = setTimeout(() => callback(), timeout - 500);
                return () => clearTimeout(timer);
            }
        } catch (error) {
            loggerUtil.error("Invalid token: ", error);
            callback();
        }
    }, [callback]);
};

export default useTokenExpiryCheck;
