import { create } from "zustand";
import { persist, createJSONStorage } from "zustand/middleware";

const useAuthStore = create(
    persist(
        (set) => ({
            user: null,
            accessToken: null,
            role: null,
            isAuthenticated: false,

            setAuth: (data) =>
                set({
                    user: data.user,
                    accessToken: data.accessToken,
                    role: data.user?.role || null,
                    isAuthenticated: true,
                }),

            clearAuth: () =>
                set({
                    user: null,
                    accessToken: null,
                    role: null,
                    isAuthenticated: false,
                }),
        }),
        {
            name: "auth-storage",
            storage: createJSONStorage(() => localStorage),
        }
    )
);

export default useAuthStore;
